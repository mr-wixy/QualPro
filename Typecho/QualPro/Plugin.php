<?php
/**
 * QualPro是基于QuickAuth平台的集成登录插件
 * 
 * @package QualPro 
 * @author wixy
 * @version 1.0.1
 * @link https://blog.wixy.cn
 */

class QualPro_Plugin implements Typecho_Plugin_Interface {
	
    /** @var string 提交路由前缀 */
	const PLUGIN_NAME  = 'QualPro';
	const PLUGIN_VERSION  = '1.0.1';
	const PLUGIN_PATH  = __TYPECHO_ROOT_DIR__.__TYPECHO_PLUGIN_DIR__.'/QualPro/';
    const LOGIN_TYPE = ['wechat'=>'微信','miniprogram'=>'小程序','sms'=>'验证码','qq'=>'QQ','github'=>'Github','weibo'=>'微博','alipay'=>'支付宝','dingtalk'=>'钉钉','gitee'=>'Gitee',];

	/**
	 * 启用插件方法,如果启用失败,直接抛出异常
	 *
	 * @static
	 * @access public
	 * @return void
	 * @throws Typecho_Plugin_Exception
	 */
	public static function activate(){
	   
        $info = self::installDb();
        
        Typecho_Plugin::factory('admin/footer.php')->end = array(__class__, 'render_footer');
        Helper::addPanel(1, 'QualPro/Account.php', '账号绑定', '第三方账号绑定', 'subscriber');
		Helper::addRoute('qualpro_ping',__TYPECHO_ADMIN_DIR__.'qualpro/ping','QualPro_Action','ping');
		Helper::addRoute('qualpro_qauth',__TYPECHO_ADMIN_DIR__.'qualpro/qauth','QualPro_Action','qauth');
		Helper::addRoute('qualpro_auth',__TYPECHO_ADMIN_DIR__.'qualpro/auth','QualPro_Action','auth');
		Helper::addRoute('qualpro_unbinding',__TYPECHO_ADMIN_DIR__.'qualpro/unbinding','QualPro_Action','unbinding');
		return _t($info);
	}
	
	public static function installDb()
    {
        $installDb = Typecho_Db::get();
		$type = explode('_', $installDb->getAdapterName());
		$type = array_pop($type);
		$prefix = $installDb->getPrefix();
		$scripts = file_get_contents('usr/plugins/QualPro/sql/'.$type.'.sql');
		$scripts = str_replace('typecho_', $prefix, $scripts);
		$scripts = str_replace('%charset%', 'utf8', $scripts);
		$scripts = explode(';', $scripts);
		
		try {
			foreach ($scripts as $script) {
				$script = trim($script);
				if ($script) {
					$installDb->query($script, Typecho_Db::WRITE);
				}
			}
			return '建立QualPro数据表，插件启用成功';
		} catch (Typecho_Db_Exception $e) {
			$code = $e->getCode();
			if (('Mysql' == $type && (1050 == $code || '42S01' == $code)) ||
					('SQLite' == $type && ('HY000' == $code || 1 == $code))) {
				try {
					$script = 'SELECT `id`, `uid`, `type`, `openid`, `unionid` from `' . $prefix . 'qauth_user`';
					$installDb->query($script, Typecho_Db::READ);
					return '检测到QualPro数据表，QualPro插件启用成功';					
				} catch (Typecho_Db_Exception $e) {
					$code = $e->getCode();
					if (('Mysql' == $type && (1054 == $code || '42S22' == $code)) ||
							('SQLite' == $type && ('HY000' == $code || 1 == $code))) {
						return QualPro_Plugin::updateDb($installDb, $type, $prefix);
					}
					throw new Typecho_Plugin_Exception('数据表检测失败，QualPro插件启用失败。错误号：'.$code);
				}
			} else {
				throw new Typecho_Plugin_Exception('数据表建立失败，QualPro插件启用失败。错误号：'.$code);
			}
		}
    }
    
    
	public static function updateDb($installDb, $type, $prefix)
	{
		$scripts = file_get_contents('usr/plugins/QualPro/sql/Update_'.$type.'.sql');
		$scripts = str_replace('typecho_', $prefix, $scripts);
		$scripts = str_replace('%charset%', 'utf8', $scripts);
		$scripts = explode(';', $scripts);
		try {
			foreach ($scripts as $script) {
				$script = trim($script);
				if ($script) {
					$installDb->query($script, Typecho_Db::WRITE);
				}
			}
			return '检测到旧版本QualPro数据表，升级成功';
		} catch (Typecho_Db_Exception $e) {
			$code = $e->getCode();
			if (('Mysql' == $type && (1060 == $code || '42S21' == $code))) {
				return 'QualPro数据表已经存在，插件启用成功';
			}
			throw new Typecho_Plugin_Exception('QualPro插件启用失败。错误号：'.$code);
		}
	}
    
	/**
	 * 禁用插件方法,如果禁用失败,直接抛出异常
	 *
	 * @static
	 * @access public
	 * @return void
	 * @throws Typecho_Plugin_Exception
	 */
	public static function deactivate(){
        Helper::removePanel(1, 'QualPro/Account.php');
        return "插件卸载成功！";
	}
	/**
	 * 获取插件配置面板
	 *
	 * @static
	 * @access public
	 * @param Typecho_Widget_Helper_Form $form 配置面板
	 * @return void
	 */
	public static function config(Typecho_Widget_Helper_Form $form){
	    
		$user = Typecho_Widget::widget('Widget_User');
		$api = new Typecho_Widget_Helper_Form_Element_Text('qauth_api',null,'https://api.qauth.cn',_t('QuickAuthApi：'),_t('<b>QuickAuthApi地址,正常情况下无需修改</b>'));
		$form->addInput($api);
		
		$appkey = new Typecho_Widget_Helper_Form_Element_Text('qauth_app_key',null,'',_t('AppKey：'),_t('<b>QuickAuth后台创建应用时的AppKey <a target="_blank" href="https://qauth.cn/app">获取AppKey</a></b>'));
		$form->addInput($appkey);
		
		$encryptscrypt = new Typecho_Widget_Helper_Form_Element_Text('qauth_user_secret',null,'',_t('UserSecret：'),_t('<b>QuickAuth用户的数据加密密钥 <a target="_blank" href="https://qauth.cn/config/secret">获取UserSecret</a></b>'));
		$form->addInput($encryptscrypt);
		
        $options = self::LOGIN_TYPE;
        $loginTypeInput = new Typecho_Widget_Helper_Form_Element_Checkbox('login_type',$options,array(),_t('登录方式：'),'<b><font color=red>勾选表示启用</font></b>');
		$form->addInput($loginTypeInput);
		
		$allowRegister = new Typecho_Widget_Helper_Form_Element_Radio('allow_register',array('0'=>'否','1'=>'是'),0,_t('允许未绑定微信账号扫码登录：',''),'<b><font color=red>开启后使用没有绑定的第三方账号登录会自动注册新的账号！</font></b>');
		$form->addInput($allowRegister);
		
		echo '<ul class="typecho-option"><li><label class="typecho-label">使用说明：</label>
		<ol>
		<li><p class="description">登陆 <a target="_blank" href="https://qauth.cn">QuickAuth</a>网站</p></li>
		<li><p class="description"><a target="_blank" href="https://qauth.cn/app">创建应用</a> 并填写相关信息（接入方式请选择Typecho插件-QualPro）</p></li>
		<li><p class="description"><a target="_blank" href="https://qauth.cn/app">发布</a> 应用</p></li>
		<li><p class="description">在此页面中配置 AppKey和UserSecret</p></li>
		</ol>
		</li>
		</ul>';
		
	}
	
	/**
	 * 个人用户的配置面板
	 *
	 * @access public
	 * @param Typecho_Widget_Helper_Form $form
	 * @return void
	 */
	public static function personalConfig(Typecho_Widget_Helper_Form $form){
	}
	
	public static function render_footer(){
		$options = self::getoptions();
        if (!Typecho_Widget::widget('Widget_User')->hasLogin()){
            $panelStr = self::qualpro_login();
    	    echo '<script> $(".submit").append("'.addslashes($panelStr).'"); </script>';
        }
	}
	
	public static function qualpro_login(){
	    $qauthUrl = __TYPECHO_ADMIN_DIR__.'qualpro/qauth';
    	$options = self::getoptions();
        $qauth_api  = $options->qauth_api;
        $qauth_appkey  = $options->qauth_app_key;
        $qauth_user_secret  = $options->qauth_user_secret;
        $qauth_login_type  = $options->login_type;
        $panelStr = "";
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $panelStr .= '<div style="margin-top:1rem;">';
        $panelStr .= '<link rel="stylesheet" href="'.Helper::options()->pluginUrl . '/QualPro/res/qualpro.css" />';
        //$panelStr .= '<div class="social-text"><div class="social-text-border"></div><div class="absolute-centered">第三方账号登录</div><div class="social-text-border"></div></div>';
        // if(strpos($ua, 'MicroMessenger') == false){
        //     $panelStr .= '<a href="'.$qauthUrl.'?type=wechat" class="btn btn-l" style="background: #2a0; margin-top:5px; color:white;">微信一键登录</a>';
        // }
        
        $panelStr .= '<p id="qualpro-box" class="qualpro-box">';
        if($qauth_appkey == "" || $qauth_appkey == null || $qauth_user_secret == "" || $qauth_user_secret == null){
            $panelStr .= '<span style="color:gray;">请配置QualPro的appkey和user_secret</span>';
        }
        else {
            //if(strpos($ua, 'MicroMessenger') == false){
                if(in_array("wechat",$qauth_login_type)) $panelStr .= '<a href="'.$qauthUrl.'?type=wechat" title="微信扫码登录" class="qualpro-wechat-icon"><span class="iconfont icon-si-wechat"></span></a>';
            //}
            if(in_array("sms",$qauth_login_type)) $panelStr .= '<a href="'.$qauthUrl.'?type=sms" title="验证码登录" class="qualpro-sms-icon"><span class="iconfont icon-si-mobile"></span></a>';
            if(in_array("miniprogram",$qauth_login_type)) $panelStr .= '<a href="'.$qauthUrl.'?type=miniprogram" title="小程序扫码登录" class="qualpro-miniprogram-icon"><span class="iconfont icon-si-miniprogram"></span></a>';
            if(in_array("qq",$qauth_login_type)) $panelStr .= '<a href="'.$qauthUrl.'?type=qq" title="QQ快捷登录" class="qualpro-qq-icon"><span class="iconfont icon-si-qq"></span></a>';
            if(in_array("github",$qauth_login_type)) $panelStr .= '<a href="'.$qauthUrl.'?type=github" title="Github快捷登录" class="qualpro-github-icon"><span class="iconfont icon-si-github"></span></a>';
            if(in_array("dingtalk",$qauth_login_type)) $panelStr .= '<a href="'.$qauthUrl.'?type=dingtalk" title="钉钉快捷登录" class="qualpro-dingding-icon"><span class="iconfont icon-si-dingding"></span></a>';
            if(in_array("weibo",$qauth_login_type))  $panelStr .= '<a href="'.$qauthUrl.'?type=weibo" title="微博快捷登录" class="qualpro-weibo-icon"><span class="iconfont icon-si-weibo"></span></a>';
            if(in_array("alipay",$qauth_login_type))  $panelStr .= '<a href="'.$qauthUrl.'?type=alipay" title="支付宝快捷登录" class="qualpro-alipay-icon"><span class="iconfont icon-si-alipay"></span></a>';
            if(in_array("gitee",$qauth_login_type)) $panelStr .= '<a href="'.$qauthUrl.'?type=gitee" title="Gitee快捷登录" class="qualpro-gitee-icon"><span class="iconfont icon-si-gitee"></span></a>';
            if(in_array("facebook",$qauth_login_type)) $panelStr .= '<a href="'.$qauthUrl.'?type=facebook" title="Facebook快捷登录" class="qualpro-facebook-icon"><span class="iconfont icon-si-facebook"></span></a>';
            if(in_array("telegram",$qauth_login_type)) $panelStr .= '<a href="'.$qauthUrl.'?type=telegram" title="电报扫码登录" class="qualpro-telegram-icon"><span class="iconfont icon-si-telegram"></span></a>';
        }
        $panelStr .= '</p></div>';
        return $panelStr;
    }
	
	
	/** 生成URL，解决部分博客未开启伪静态，仅对本插件有效 */
	public static function tourl($action){
		return Typecho_Common::url(__TYPECHO_ADMIN_DIR__.$action, Helper::options()->index);
	}
	
	/** 获取插件配置 */
	public static function getoptions(){
		return Helper::options()->plugin(qualpro_Plugin::PLUGIN_NAME);
	}
}