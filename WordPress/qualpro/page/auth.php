<?php
session_start();
header("Content-Type: text/html;charset=utf-8");
include_once('../../../../wp-load.php');

//接口地址
$qauth_api  = get_option('qauth_api');
$qauth_appkey  = get_option('qauth_appkey');
$qauth_user_secret  = get_option('qauth_user_secret');
$qauth_auto_register = get_option('qauth_auto_register');
$qauth_state_check = get_option('qauth_state_check');
$qauth_support_cn_name = get_option('qauth_support_cn_name');
$qualpro_redirect_url = get_option('qualpro_redirect_url');

if(isset($_GET['code']) && isset($_GET['state'])){
    $code = $_GET['code'];
    $state = $_GET['state'];
    if($qauth_state_check){
        if($state != $_SESSION['qualpro_state']){
            wp_die("请求state无效！");
        }
    }
    $response = wp_remote_get($qauth_api.'/authinfov2?code='.$code.'&appkey='.$qauth_appkey.'&secret='.$qauth_user_secret);
    $body = wp_remote_retrieve_body( $response );
    $content_obj = json_decode($body);
    if($content_obj->code === 0){
        $auth_obj = $content_obj->res;
        $user_obj = $auth_obj->userInfo;
        
        $qualproTypeStr = 'qualpro_'.$auth_obj->authType;
        $userMetaKey = 'qualpro_'.$auth_obj->authType.'_openid';
        if($auth_obj->authType == "wechat"){
            $userMetaKey = 'qualpro_'.$auth_obj->authType.'_'.$auth_obj->detailType.'_openid';
            if($auth_obj->detailType == 'miniprogram'){
                $qualproTypeStr = 'qualpro_miniprogram';
            }
            else if($auth_obj->detailType == 'offiaccount'){
                $qualproTypeStr = 'qualpro_wechat';
            }
            else
                $qualproTypeStr = $qualproTypeStr.'_'.$auth_obj->detailType;
        }
        if(!get_option($qualproTypeStr)){
            wp_die("暂未开启".$qualproTypeStr.'登录！');
        }
        
        //可以使用unionid登录
        if($auth_obj->authType == "wechat" && !is_user_logged_in()){
            $union_user_query = new WP_User_Query( array( 'meta_key' => 'qualpro_wechat_unionid', 'meta_value' => $user_obj->unionId) );
            if($union_user_query->get_results()){
                $login_user=$union_user_query->get_results()[0]->data;
                wp_set_current_user( $login_user->ID);
            	wp_set_auth_cookie( $login_user->ID);
            	if($auth_obj->redirectUrl){
            	    wp_redirect($auth_obj->redirectUrl); 
            	}
            	else{
            	    wp_redirect( home_url().$qualpro_redirect_url ); 
            	} 
        	    exit;
            }
        }
       
        $user_query = new WP_User_Query( array( 'meta_key' => $userMetaKey, 'meta_value' => $user_obj->openId) );
        //绑定
        if(is_user_logged_in()){
            $queryRes = $user_query->get_results();
            if($queryRes){
                $current_user = wp_get_current_user();
                if($current_user->data->ID != $queryRes[0]->data->ID){
                    wp_die("此第三方账号已被其他账号绑定过了，请先解绑！");
                }
                else{
                    if($auth_obj->redirectUrl){
            	        wp_redirect($auth_obj->redirectUrl); 
                	}
                	else{
            	        wp_redirect( home_url().$qualpro_redirect_url ); 
                	} 
                }
            }
            else{
                // var_dump($auth_obj);
                // exit;
                $current_user = wp_get_current_user();
                add_user_meta($current_user->data->ID, $userMetaKey, $user_obj->openId);
                if($auth_obj->authType == "wechat"){
                    $union_meta = get_user_meta($current_user->data->ID, 'qualpro_wechat_unionid',true);
                    if($union_meta == ""){
                        add_user_meta($current_user->data->ID, 'qualpro_wechat_unionid', $user_obj->unionId);
                    }
                }
                if($auth_obj->redirectUrl){
            	    wp_redirect($auth_obj->redirectUrl); 
            	}
            	else{
            	    wp_redirect( home_url().'/wp-admin/admin.php?page=qualpro/page/account.php' ); 
            	} 
            }
        }
        //登录
        else{
            if($user_query->get_results()){
                $login_user=$user_query->get_results()[0]->data;
                if($auth_obj->authType == "wechat"){
                    $union_meta = get_user_meta( $login_user->ID, 'qualpro_wechat_unionid',true);
                    if($union_meta == ""){
                        add_user_meta($login_user->ID, 'qualpro_wechat_unionid', $user_obj->unionId);
                    }
                }
                wp_set_current_user( $login_user->ID);
            	wp_set_auth_cookie( $login_user->ID);
            	if($auth_obj->redirectUrl){
            	    wp_redirect($auth_obj->redirectUrl); 
            	}
            	else{
            	    wp_redirect( home_url().$qualpro_redirect_url ); 
            	} 
        	    exit;
            }
            else{
                //未绑定用户
                if($qauth_auto_register == '1'){
                    $newUserName = $user_obj->randomName;
                    if($qauth_support_cn_name){
                        $newUserName = $user_obj->nickName;
                    }
                    $user_id = username_exists($newUserName); 
                    if($user_id){  
                        $newUserName = $newUserName.'_'.substr(md5(uniqid(microtime())), 0, 4);
                    } 
                    $random_password = substr(md5(uniqid(microtime())), 0, 6);  	
                    $user_id = wp_create_user($newUserName, $random_password, $newUserName.'@qauth.cn');
                    if($auth_obj->authType == "wechat"){
                        add_user_meta($user_id, 'qualpro_'.$auth_obj->authType.'_'.$auth_obj->detailType.'_openid', $user_obj->openId);
                        add_user_meta($user_id, 'qualpro_wechat_unionid', $auth_obj->unionId);
                    }
                    else{
                        add_user_meta($user_id, 'qualpro_'.$auth_obj->authType.'_openid', $user_obj->openId);
                    }
                    wp_set_current_user( $user_id);
        	        wp_set_auth_cookie( $user_id);
                	if($auth_obj->redirectUrl){
                	    wp_redirect($auth_obj->redirectUrl); 
                	}
                	else{
                	    wp_redirect( home_url().$qualpro_redirect_url ); 
                	}
        	        exit;
                }
                else{
                    wp_die("未绑定用户禁止登录！");
                }
            }
        }
    }
    else{
        wp_die('QuickAuth接口调用出错【'.$content_obj->msg.'】');
    }
}else{
        wp_die('错误的参数');
}