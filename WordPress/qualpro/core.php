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
	update_option('qauth_state_check', '1');
	update_option('qauth_support_cn_name', '0');
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
    if(strpos($ua, 'MicroMessenger') != false && $qualpro_wechat){
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
	if(is_user_logged_in()){
		return;
	}
	
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

function qualpro_account($form_url, $turn_page = null) {
    
    $current_user = wp_get_current_user();
    
    if($turn_page == null){
        $turn_page = '&redirect='.home_url(add_query_arg(array()));
    }
    else{
        $turn_page = '&redirect='.$turn_page;
    }
    
    $wechatopenid = get_user_meta($current_user->data->ID, 'qualpro_wechat_offiaccount_openid', true);
    $miniprogramopenid = get_user_meta($current_user->data->ID, 'qualpro_wechat_miniprogram_openid', true);
    $qqopenid = get_user_meta($current_user->data->ID, 'qualpro_qq_openid', true);
    $githubopenid = get_user_meta($current_user->data->ID, 'qualpro_github_openid', true);
    $dingtalkopenid = get_user_meta($current_user->data->ID, 'qualpro_dingtalk_openid', true);
    $weiboopenid = get_user_meta($current_user->data->ID, 'qualpro_weibo_openid', true);
    $alipayopenid = get_user_meta($current_user->data->ID, 'qualpro_alipay_openid', true);
    $giteeopenid = get_user_meta($current_user->data->ID, 'qualpro_gitee_openid', true);
    
    $qualpro_wechat  = get_option('qualpro_wechat');
    $qualpro_miniprogram  = get_option('qualpro_miniprogram');
    $qualpro_qq  = get_option('qualpro_qq');
    $qualpro_github  = get_option('qualpro_github');
    $qualpro_dingtalk  = get_option('qualpro_dingtalk');
    $qualpro_weibo  = get_option('qualpro_weibo');
    $qualpro_alipay  = get_option('qualpro_alipay');
    $qualpro_gitee  = get_option('qualpro_gitee');
    
    $accountHtml = '<link rel="stylesheet" href="'.constant("qualpro_plugin").'res/qualpro.css">';
    $accountHtml.= '<div class="wrap">
        <div class="qualpro-settings-header">
        	<div class="qualpro-settings-title-section">
        		<div style="display: inline-block; font-weight: 600; margin: 0 .8rem 1rem; font-size: 23px; padding: 9px 0 4px; line-height: 1.3;color: #1d2327;">第三方账号绑定</div>
        	</div>
        	<nav aria-label="描述">
        		<p  style="text-align: center;">您可以在此处绑定或者解绑您的第三方账号</p>
        	</nav>
        </div>';
        $accountHtml .= ' <div class="privacy-settings-body hide-if-no-js">
        <form method="post" action="'.$form_url.'">
		    <table class="form-table widefat importers striped" style="width:100%">	';
		if($qualpro_wechat){
		    $accountHtml .= '<tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-wechat qualpro-wechat-icon"></span>&nbsp;<strong>微信</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">';
    		if($wechatopenid){
    		    $accountHtml .= '<span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_wechat_offiaccount_openid" class="button">解绑</button>   ';
    		}
    		else{
    		    $accountHtml .= '<span style="color:#a9a9a9">未绑定</span><a href="'.constant("qualpro_plugin").'page/qauth.php?type=wechat'.$turn_page.'" class="button button-primary">绑定</a>';
    		}
    		$accountHtml .= '</div></td></tr>';
		}
		if($qualpro_miniprogram){
		    $accountHtml .= '<tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-miniprogram qualpro-miniprogram-icon"></span>&nbsp;<strong>小程序</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">';
    		if($miniprogramopenid){
    		    $accountHtml .= '<span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_wechat_miniprogram_openid" class="button">解绑</button>   ';
    		}
    		else{
    		    $accountHtml .= '<span style="color:#a9a9a9">未绑定</span><a href="'.constant("qualpro_plugin").'page/qauth.php?type=miniprogram'.$turn_page.'" class="button button-primary">绑定</a>';
    		}
    		$accountHtml .= '</div></td></tr>';
		}
		
		if($qualpro_qq){
		    $accountHtml .= '<tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-qq qualpro-qq-icon"></span>&nbsp;<strong>QQ</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">';
    		if($qqopenid){
    		    $accountHtml .= '<span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_qq_openid" class="button">解绑</button>   ';
    		}
    		else{
    		    $accountHtml .= '<span style="color:#a9a9a9">未绑定</span><a href="'.constant("qualpro_plugin").'page/qauth.php?type=qq'.$turn_page.'" class="button button-primary">绑定</a>';
    		}
    		$accountHtml .= '</div></td></tr>';
		}
		
		if($qualpro_github){
		    $accountHtml .= '<tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-github qualpro-github-icon"></span>&nbsp;<strong>Github</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">';
    		if($githubopenid){
    		    $accountHtml .= '<span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_github_openid" class="button">解绑</button>   ';
    		}
    		else{
    		    $accountHtml .= '<span style="color:#a9a9a9">未绑定</span><a href="'.constant("qualpro_plugin").'page/qauth.php?type=github'.$turn_page.'" class="button button-primary">绑定</a>';
    		}
    		$accountHtml .= '</div></td></tr>';
		}
		
		if($qualpro_dingtalk){
		    $accountHtml .= '<tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-dingding qualpro-dingding-icon"></span>&nbsp;<strong>钉钉</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">';
    		if($dingtalkopenid){
    		    $accountHtml .= '<span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_dingtalk_openid" class="button">解绑</button>   ';
    		}
    		else{
    		    $accountHtml .= '<span style="color:#a9a9a9">未绑定</span><a href="'.constant("qualpro_plugin").'page/qauth.php?type=dingtalk'.$turn_page.'" class="button button-primary">绑定</a>';
    		}
    		$accountHtml .= '</div></td></tr>';
		}
		
		if($qualpro_weibo){
		    $accountHtml .= '<tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-weibo qualpro-weibo-icon"></span>&nbsp;<strong>微博</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">';
    		if($weiboopenid){
    		    $accountHtml .= '<span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_weibo_openid" class="button">解绑</button>   ';
    		}
    		else{
    		    $accountHtml .= '<span style="color:#a9a9a9">未绑定</span><a href="'.constant("qualpro_plugin").'page/qauth.php?type=weibo'.$turn_page.'" class="button button-primary">绑定</a>';
    		}
    		$accountHtml .= '</div></td></tr>';
		}
		
		if($qualpro_alipay){
		    $accountHtml .= '<tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-alipay qualpro-alipay-icon"></span>&nbsp;<strong>支付宝</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">';
    		if($alipayopenid){
    		    $accountHtml .= '<span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_alipay_openid" class="button">解绑</button>   ';
    		}
    		else{
    		    $accountHtml .= '<span style="color:#a9a9a9">未绑定</span><a href="'.constant("qualpro_plugin").'page/qauth.php?type=alipay'.$turn_page.'" class="button button-primary">绑定</a>';
    		}
    		$accountHtml .= '</div></td></tr>';
		}
		
		if($qualpro_gitee){
		    $accountHtml .= '<tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-gitee qualpro-gitee-icon"></span>&nbsp;<strong>Gitee</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">';
    		if($giteeopenid){
    		    $accountHtml .= '<span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_gitee_openid" class="button">解绑</button>   ';
    		}
    		else{
    		    $accountHtml .= '<span style="color:#a9a9a9">未绑定</span><a href="'.constant("qualpro_plugin").'page/qauth.php?type=gitee'.$turn_page.'" class="button button-primary">绑定</a>';
    		}
    		$accountHtml .= '</div></td></tr>';
		}
		
		$accountHtml .= ' </table></form></div></div>';
		echo $accountHtml;
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
