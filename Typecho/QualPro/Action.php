<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

class QualPro_Action extends Typecho_Widget
{
    private function throwJson($data){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }
    
    private function redirect($location){
        header("Location: $location");
        exit();
    }
    
    /* 测试接口 */
    public function ping(){ 
        $all = Typecho_Plugin::export();
        $version = Typecho_Cookie::get('__typecho_check_version');
        $data = [
            "code" => 0,
            "msg" => "pong",
            "data" => [
                "name" => "QualPro For Typecho",
                "enable" => array_key_exists('QualPro', $all['activated']),
        	    "version" => QualPro_Plugin::PLUGIN_VERSION
                ]
        ];
        $this->throwJson($data);
    }

    /* 取消用户绑定 */
    public function unbinding()
    {
        require_once __TYPECHO_ROOT_DIR__ . __TYPECHO_ADMIN_DIR__ . 'common.php';
        $uid = $user->__get('uid');
            $db = Typecho_Db::get();
        if(isset($_GET['unbinding_type'])){
            $delete = $db->delete('table.qauth_user')->where('uid = ?', $uid)->where('type = ?', $_GET['unbinding_type']);
            $deletedRows = $db->query($delete);
            if($deletedRows > 0){
                if(strpos($_GET['unbinding_type'],'wechat') !== false){
                    $delete = $db->delete('table.qauth_user')
                        ->where('uid = ?', $uid)->where('type = "wechat"');
                    $deletedRows = $db->query($delete);
                }
                $this->widget('Widget_Notice')->set(_t('解绑成功'), 'success');
            }
        }
        $path = QualPro_Plugin::tourl("extending.php?panel=QualPro/Account.php");
        $this->redirect($path);
    }

    //qauth登录跳转
    public function qauth(){
        $options = QualPro_Plugin::getoptions();
        $type = $_GET['type'];
        $login_type = $_GET['login_type'];
        $redirect = $_GET['redirect'];
        $state = md5(uniqid(microtime()));
        session_start();
        $_SESSION['qualpro_state'] = $state;
        $_SESSION['qualpro_login_type'] = $login_type;
        $url = $options->qauth_api.'/oauth?';
        $qauth_appkey = $options->qauth_app_key;
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
                    $this->widget('Widget_Notice')->set("错误的参数 type=".$type, 'error');
                    break;
            }
            $url .= '&redirect='.$redirect;
            $this->redirect($url);
            exit;
        }
        else{
            $this->widget('Widget_Notice')->set('type无效！', 'error');
        }
    }
    
    /* qauth回调登录逻辑 */
    public function auth()
    {
        session_start();
        $options = QualPro_Plugin::getoptions();
        $ret   = [];
        $code = $_GET['code'];
        $state = $_GET['state'];
        if($state != $_SESSION['qualpro_state']){
            $ret['msg'] = "请求state无效";
            $this->throwJson($ret);
        }
        
        $api = $options->qauth_api."/authinfo?code=".$code."&appkey=".$options->qauth_app_key."&secret=".$options->qauth_user_secret;
        $body=self::get_curl($api, $paras);
		$content_obj = json_decode($body);
		
        if($content_obj->code === 0){
            $auth_obj = $content_obj->res;
            $user_obj = $auth_obj->userInfo;
            
            $db = Typecho_Db::get();
            
            $qualproTypeStr = $auth_obj->authType;
            if($auth_obj->authType == "wechat"){
                $qualproTypeStr = $qualproTypeStr.'_'.$auth_obj->detailType;
            }
            $loginType = QualPro_Plugin::LOGIN_TYPE[$auth_obj->authType];
            //用户绑定
            if($_SESSION['qualpro_login_type'] == "binding" && Typecho_Widget::widget('Widget_User')->hasLogin()){
                require_once __TYPECHO_ROOT_DIR__ . __TYPECHO_ADMIN_DIR__ . 'common.php';
                $name = $user->__get('name');
                $uid = $user->__get('uid');
                $path = QualPro_Plugin::tourl("extending.php?panel=QualPro/Account.php");
                $user = $db->fetchRow($db->select()->from('table.qauth_user')->where('type = ?', $qualproTypeStr)->where( 'openid ' . ' = ?', $user_obj->openId)->limit(1));
                if($user){
                    $this->widget('Widget_Notice')->set('此第三方账号已被其他用户绑定！', 'error');
                    $this->redirect($path);
                }
                
                $openDataStruct = array(
                    'uid'   =>  $uid,
                    'type'  =>  $qualproTypeStr,
                    'openid'=>  $user_obj->openId,
                    'unionid' => ""
                );
                $openDdataStruct = $this->pluginHandle()->register($openDataStruct);
                $db->query($db->insert('table.qauth_user')->rows($openDdataStruct));
                
                $user = $db->fetchRow($db->select()->from('table.qauth_user')->where('type = ?', 'wechat')->where( 'unionid ' . ' = ?', $user_obj->unionId)->limit(1));
                if($auth_obj->authType == "wechat" && !$user){
                    $openDataStruct = array(
                        'uid'   =>  $uid,
                        'type'  =>  "wechat",
                        'openid'=>  "",
                        'unionid' => $user_obj->unionId
                    );
                    $openDdataStruct = $this->pluginHandle()->register($openDataStruct);
                    $db->query($db->insert('table.qauth_user')->rows($openDdataStruct));
                }
                
                $this->widget('Widget_Notice')->set(_t('用户 <strong>%s</strong> 成功绑定 '.$loginType.' 账号 <strong>%s</strong>', $name, $user_obj->nickName), 'success');
                $this->redirect($path);
            }
            else{
                if($auth_obj->authType == "wechat"){
                    $qauth_user = $db->fetchRow($db->select()->from('table.qauth_user')->where('type = ?', $auth_obj->authType)->where( 'unionid = ?', $user_obj->unionId)->limit(1));
                }
                if(!$qauth_user){
                    $qauth_user = $db->fetchRow($db->select()->from('table.qauth_user')->where('type = ?', $qualproTypeStr)->where( 'openid = ?', $user_obj->openId)->limit(1));
                }
                if($qauth_user){
                    $user = $db->fetchRow($db->select()->from('table.users')->where( 'uid = ?', $qauth_user["uid"])->limit(1));
                    
                    $authCode = function_exists('openssl_random_pseudo_bytes') ? bin2hex(openssl_random_pseudo_bytes(16)) : sha1(Common::randString(20));
                    $user['authCode'] = $authCode;
                    Typecho_Cookie::set('__typecho_uid', $user['uid']);
                    Typecho_Cookie::set('__typecho_authCode', Typecho_Common::hash($authCode));
                    $db->query($db->update('table.users')->expression('logged',
                        'activated')->rows(['authCode' => $authCode])->where('uid = ?', $user['uid']));
                    /** 压入数据 */
                    $this->push($user);
                    $this->_user    = $user;
                    $this->_hasLogin = true;
                    $this->widget('Widget_Notice')->set(_t('用户 <strong>%s</strong> 通过 <strong>'.$loginType.'</strong> 登录成功', $user["name"]), 'success');
                    $this->redirect(Helper::options()->adminUrl);
                }
                else{
                    if($options->allow_register){//匿名账号注册登录
                        $hasher = new PasswordHash(8, true);
                        $generatedPassword = Typecho_Common::randString(7);
                        
                        $newUserName = "qauth_".$user_obj->nickName;
                        $existUser = $db->fetchRow($db->select()->from('table.users')->where( 'name' . ' = ?', $newUserName)->limit(1));
                        if($existUser)
                            $newUserName = "qauth_".$user_obj->nickName.'_'.Typecho_Common::randString(4);
                        
                        $dataStruct = array(
                            'name'      =>  $newUserName,
                            'mail'      =>  $newUserName."@qauth.cn",
                            'screenName'=>  $user_obj->nickName,
                            'password'  =>  $hasher->HashPassword($generatedPassword),
                            'created'   =>  time(),
                            'group'     =>  'subscriber'
                        );
                        $dataStruct = $this->pluginHandle()->register($dataStruct);
                        $insertId = $db->query($db->insert('table.users')->rows($dataStruct));
                        
                        $openDataStruct = array(
                            'uid'   =>  $insertId,
                            'type'  =>  $qualproTypeStr,
                            'openid'=>  $user_obj->openId,
                            'unionid' => ""
                        );
                        $openDdataStruct = $this->pluginHandle()->register($openDataStruct);
                        $db->query($db->insert('table.qauth_user')->rows($openDdataStruct));
                        if($auth_obj->authType == "wechat"){
                            $openDataStruct = array(
                                'uid'   =>  $insertId,
                                'type'  =>  "wechat",
                                'openid'=>  "",
                                'unionid' => $user_obj->unionId
                            );
                            $openDdataStruct = $this->pluginHandle()->register($openDataStruct);
                            $db->query($db->insert('table.qauth_user')->rows($openDdataStruct));
                        }
                        
                        $user = $db->fetchRow($db->select()->from('table.users')->where( 'uid = ?', $insertId)->limit(1));
                        $authCode = function_exists('openssl_random_pseudo_bytes') ? bin2hex(openssl_random_pseudo_bytes(16)) : sha1(Typecho_Common::randString(20));
                        $user['authCode'] = $authCode;
                        Typecho_Cookie::set('__typecho_uid', $user['uid']);
                        Typecho_Cookie::set('__typecho_authCode', Typecho_Common::hash($authCode));
            
                        $db->query($db->update('table.users')->expression('logged',
                            'activated')->rows(['authCode' => $authCode])->where('uid = ?', $user['uid']));
            
                        $this->push($user);
                        $this->_user    = $user;
                        $this->_hasLogin = true;
    
                        $this->widget('Widget_Notice')->set(_t('用户 <strong>%s</strong> 已经成功注册, 密码为 <strong>%s</strong>', $newUserName, $generatedPassword), 'success');
                        $this->redirect(Helper::options()->adminUrl);
                    }
                    else{
                        $this->widget('Widget_Notice')->set('该第三方账号未绑定用户，无法登陆！', 'error');
                        $this->redirect(Helper::options()->loginUrl);
                    }
                }
                
            }
        }
        else{
            $ret['msg'] = $content_obj->msg;
            $this->throwJson($ret);
        }
    }

    /** Curl单例封装函数 */
    public static function get_curl($url, $paras = [])
    {
        //echo $paras;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept:*/*";
        $httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
        $httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
        $httpheader[] = "Connection:close";
        if($paras['httpheader']){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $paras['httpheader']);
        }
        else{
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        }
        if ($paras['ctime']) { // 连接超时
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $paras['ctime']);
        }
        if ($paras['rtime']) { // 读取超时
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $paras['rtime']);
        }
        if ($paras['post']) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paras['post']);
        }
        if ($paras['header']) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }
        if ($paras['cookie']) {
            curl_setopt($ch, CURLOPT_COOKIE, $paras['cookie']);
        }
        if ($paras['refer']) {
            curl_setopt($ch, CURLOPT_REFERER, $paras['refer']);
        }
        if ($paras['ua']) {
            curl_setopt($ch, CURLOPT_USERAGENT, $paras['ua']);
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT,
                "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");
        }
        if ($paras['nobody']) {
            curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
}
