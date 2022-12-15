<?php
session_start();
header("Content-Type: text/html;charset=utf-8");
include_once('../../../../wp-load.php');

//接口地址
$qauth_api  = get_option('qauth_api');
$qauth_appkey  = get_option('qauth_appkey');
$qauth_user_secret  = get_option('qauth_user_secret');

$type = $_GET['type'];
$redirect = $_GET['redirect'];
$state = md5(uniqid(microtime()));
$_SESSION['qualpro_state'] = $state;
$url = $qauth_api.'/oauth?';

if(isset($type)){
    switch ($type) {
        case 'wechat':
            $url = $url.'type=wechat&appkey='.$qauth_appkey.'&state='.$state;
            break;
        case 'miniprogram':
            $url = $url.'type=wechat&detailType=miniprogram&appkey='.$qauth_appkey.'&state='.$state;
            break;
        case 'qq':
            $url = $url.'type=qq&appkey='.$qauth_appkey.'&state='.$state;
            break;
        case 'github':
            $url = $url.'type=github&appkey='.$qauth_appkey.'&state='.$state;
            break;
        case 'dingtalk':
            $url = $url.'type=dingtalk&appkey='.$qauth_appkey.'&state='.$state;
            break;
        case 'weibo':
            $url = $url.'type=weibo&appkey='.$qauth_appkey.'&state='.$state;
            break;
        case 'alipay':
            $url = $url.'type=alipay&appkey='.$qauth_appkey.'&state='.$state;
            break;
        case 'gitee':
            $url = $url.'type=gitee&appkey='.$qauth_appkey.'&state='.$state;
            break;
        case 'sms':
            $url = $url.'type=sms&appkey='.$qauth_appkey.'&state='.$state;
            break;
        case 'facebook':
            $url = $url.'type=facebook&appkey='.$qauth_appkey.'&state='.$state;
            break;
        case 'telegram':
            $url = $url.'type=telegram&appkey='.$qauth_appkey.'&state='.$state;
            break;
        default:
            wp_die("错误的参数 type=".$type );
            break;
    }
    wp_redirect($url.'&redirect='.$redirect);
    exit;
}
else{
    wp_die("type无效");
}