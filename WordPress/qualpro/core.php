<?php
global $wpdb;

// 初始化参数
function qauth_init() {
	update_option('qualpro_plugin_enable', '1');
    update_option('qualpro_wechat', '1');
	update_option('qualpro_miniprogram', '1');
	update_option('qualpro_qq', '1');
	update_option('qualpro_github', '1');
	update_option('qualpro_dingtalk', '1');
	update_option('qualpro_weibo', '1');
	update_option('qualpro_alipay', '1');
	update_option('qualpro_gitee', '1');
	update_option('qualpro_sms', '1');
	update_option('qauth_appkey', '');
	update_option('qauth_user_secret', '');
	update_option('qauth_api', 'https://api.qauth.cn');
	update_option('qauth_auto_register', '1');
	update_option('qualpro_redirect_type', '1');
	update_option('qualpro_redirect_url', '/');
}

// 启用
function qauth_install() {
    qauth_init();
    flush_rewrite_rules();
}

// 禁用
function qauth_deactivation() {
	update_option('qualpro_plugin_enable', '0');
    flush_rewrite_rules();
}

// 登录界面新增第三方按钮
function qualpro_login_button(){
    $qauth_api  = get_option('qauth_api');
    $qauth_appkey  = get_option('qauth_appkey');
    $qauth_user_secret  = get_option('qauth_user_secret');
    $qualpro_wechat  = get_option('qualpro_wechat');
    $qualpro_miniprogram  = get_option('qualpro_miniprogram');
    $qualpro_qq  = get_option('qualpro_qq');
    $qualpro_github  = get_option('qualpro_github');
    $qualpro_dingtalk  = get_option('qualpro_dingtalk');
    $qualpro_weibo  = get_option('qualpro_weibo');
    $qualpro_alipay  = get_option('qualpro_alipay');
    $qualpro_gitee  = get_option('qualpro_gitee');
    $qualpro_sms  = get_option('qualpro_sms');
    $qualpro_facebook  = get_option('qualpro_facebook');
    $qualpro_telegram  = get_option('qualpro_telegram');
    
    $ua = $_SERVER['HTTP_USER_AGENT'];
    echo '<link rel="stylesheet" href="'.constant("qualpro_plugin").'res/qualpro.css">';
    echo '<p id="qualpro-title" class="qualpro-box">第三方账号登录</p>';
    if(strpos($ua, 'MicroMessenger') != false){
        echo '<p id="qualpro-title" class="qualpro-box"><a href="'.constant("qualpro_plugin").'page/qauth.php?type=wechat" class="button" style="width:100%;background-color:#2a0;border-color:#2a0;"><span class="iconfont icon-si-wechat" style="font-size:14px;color:white">&nbsp;微信一键登录</span></a></p>';
    }
    echo '<p id="qualpro-box" class="qualpro-box">';
    if($qauth_appkey == "" || $qauth_appkey == null || $qauth_user_secret == "" || $qauth_user_secret == null){
        echo '<span style="color:gray;">请配置QualPro的appkey和user_secret</span>';
    }
    else {
        if(strpos($ua, 'MicroMessenger') == false){
            if($qualpro_wechat) echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=wechat" title="微信扫码登录" class="qualpro-wechat-icon"><span class="iconfont icon-si-wechat"></span></a>';
        }
        if($qualpro_sms) echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=sms" title="验证码登录" class="qualpro-sms-icon"><span class="iconfont icon-si-mobile"></span></a>';
        if($qualpro_miniprogram) echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=miniprogram" title="小程序扫码登录" class="qualpro-miniprogram-icon"><span class="iconfont icon-si-miniprogram"></span></a>';
        if($qualpro_qq) echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=qq" title="QQ快速登录" class="qualpro-qq-icon"><span class="iconfont icon-si-qq"></span></a>';
        if($qualpro_github) echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=github" title="Github快速登录" class="qualpro-github-icon"><span class="iconfont icon-si-github"></span></a>';
        if($qualpro_dingtalk) echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=dingtalk" title="钉钉快速登录" class="qualpro-dingding-icon"><span class="iconfont icon-si-dingding"></span></a>';
        if($qualpro_weibo)  echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=weibo" title="微博快速登录" class="qualpro-weibo-icon"><span class="iconfont icon-si-weibo"></span></a>';
        if($qualpro_alipay)  echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=alipay" title="支付宝快速登录" class="qualpro-alipay-icon"><span class="iconfont icon-si-alipay"></span></a>';
        if($qualpro_gitee) echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=gitee" title="Gitee快速登录" class="qualpro-gitee-icon"><span class="iconfont icon-si-gitee"></span></a>';
        if($qualpro_facebook) echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=facebook" title="Facebook快速登录" class="qualpro-facebook-icon"><span class="iconfont icon-si-facebook"></span></a>';
        if($qualpro_telegram) echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=telegram" title="电报扫码登录" class="qualpro-telegram-icon"><span class="iconfont icon-si-telegram"></span></a>';
    }
    echo '</p>';
}

function qualpro_login($turn_to_current_page = false)
{
    $redirect = "";
    if($turn_to_current_page == true){
        $redirect = '&redirect='.home_url(add_query_arg(array()));
    }
    $panelStr = '<div>';
    $qauth_api  = get_option('qauth_api');
    $qauth_appkey  = get_option('qauth_appkey');
    $qauth_user_secret  = get_option('qauth_user_secret');
    $qualpro_wechat  = get_option('qualpro_wechat');
    $qualpro_miniprogram  = get_option('qualpro_miniprogram');
    $qualpro_qq  = get_option('qualpro_qq');
    $qualpro_github  = get_option('qualpro_github');
    $qualpro_dingtalk  = get_option('qualpro_dingtalk');
    $qualpro_weibo  = get_option('qualpro_weibo');
    $qualpro_alipay  = get_option('qualpro_alipay');
    $qualpro_gitee  = get_option('qualpro_gitee');
    $qualpro_sms  = get_option('qualpro_sms');
    $qualpro_facebook  = get_option('qualpro_facebook');
    $qualpro_telegram  = get_option('qualpro_telegram');
    
    $panelStr .= '<link rel="stylesheet" href="'.constant("qualpro_plugin").'res/qualpro.css">';
    $panelStr .= '<p id="qualpro-title" class="qualpro-box" style="margin-top: 15px;">第三方账号登录</p>';
    $panelStr .= '<p id="qualpro-box" class="qualpro-box">';
    if($qauth_appkey == "" || $qauth_appkey == null || $qauth_user_secret == "" || $qauth_user_secret == null){
        $panelStr .= '<span style="color:gray;">请配置QualPro的appkey和user_secret</span>';
    }
    else {
        if($qualpro_wechat) $panelStr .=  '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=wechat'.$redirect.'" title="微信扫码登录" class="qualpro-wechat-icon"><span class="iconfont icon-si-wechat"></span></a>';
        if($qualpro_sms) $panelStr .=  '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=sms'.$redirect.'" title="验证码登录" class="qualpro-sms-icon"><span class="iconfont icon-si-mobile"></span></a>';
        if($qualpro_miniprogram) $panelStr .= '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=miniprogram'.$redirect.'" title="小程序扫码登录" rel="nofollow" class="qualpro-miniprogram-icon"><span class="iconfont icon-si-miniprogram"></span></a>';
        if($qualpro_qq) $panelStr .= '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=qq'.$redirect.'" title="QQ快速登录" rel="nofollow" class="qualpro-qq-icon"><span class="iconfont icon-si-qq"></span></a>';
        if($qualpro_github) $panelStr .= '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=github'.$redirect.'" title="Github快速登录" rel="nofollow" class="qualpro-github-icon"><span class="iconfont icon-si-github"></span></a>';
        if($qualpro_dingtalk) $panelStr .= '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=dingtalk'.$redirect.'" title="钉钉快速登录" rel="nofollow" class="qualpro-dingding-icon"><span class="iconfont icon-si-dingding"></span></a>';
        if($qualpro_weibo)  $panelStr .=  '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=weibo'.$redirect.'" title="微博快速登录" rel="nofollow" class="qualpro-weibo-icon"><span class="iconfont icon-si-weibo"></span></a>';
        if($qualpro_alipay)  $panelStr .=  '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=alipay'.$redirect.'" title="支付宝快速登录" class="qualpro-alipay-icon"><span class="iconfont icon-si-alipay"></span></a>';
        if($qualpro_gitee) $panelStr .=  '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=gitee'.$redirect.'" title="Gitee快速登录" rel="nofollow" class="qualpro-gitee-icon"><span class="iconfont icon-si-gitee"></span></a>';
        if($qualpro_facebook) $panelStr .=  '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=facebook'.$redirect.'" title="Facebook快速登录" rel="nofollow" class="qualpro-facebook-icon"><span class="iconfont icon-si-facebook"></span></a>';
        if($qualpro_telegram) $panelStr .=  '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=telegram'.$redirect.'" title="电报扫码登录" rel="nofollow" class="qualpro-telegram-icon"><span class="iconfont icon-si-telegram"></span></a>';
    }
    return $panelStr."</div>";
}

// 新增菜单
function qualpro_menu() {
	if (function_exists('add_menu_page')) {
		add_menu_page('账号绑定', '账号绑定', 'read', 'qualpro/page/account.php', '','dashicons-admin-users');
	}
	if (function_exists('add_submenu_page')) {
		add_submenu_page('plugins.php', 'QualPro设置','QualPro设置', 'administrator', 'qualpro/page/setting.php', '',4);
		add_submenu_page(null, 'QualPro设置','QualPro设置', 'administrator', 'qualpro/page/setting-oauth.php', '',4);
	}
}
