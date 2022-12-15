<?php 

$current_user = wp_get_current_user();

if(isset($_POST['unbinding_type'])){
    if(delete_user_meta($current_user->data->ID, $_POST['unbinding_type'], '')){
        if(strpos($_POST['unbinding_type'],'wechat') !== false){
            delete_user_meta($current_user->data->ID, 'qualpro_wechat_unionid', '');
        }
	    echo '<div class="updated settings-error"><p>解绑成功</p></div>';
    }
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
$qualpro_facebook  = get_option('qualpro_facebook');
$qualpro_telegram  = get_option('qualpro_telegram');


echo '<link rel="stylesheet" href="'.constant("qualpro_plugin").'res/qualpro.css">';
?>
<div class="wrap">
    
    <div class="qualpro-settings-header">
    	<div class="qualpro-settings-title-section">
    		<div style="display: inline-block; font-weight: 600; margin: 0 .8rem 1rem; font-size: 23px; padding: 9px 0 4px; line-height: 1.3;color: #1d2327;">第三方账号绑定</div>
    	</div>
    	<nav aria-label="描述">
    		<p  style="text-align: center;">您可以在此处绑定或者解绑您的第三方账号</p>
    	</nav>
    </div>
    
    <div class="privacy-settings-body hide-if-no-js">
        <form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
		    <table class="form-table widefat importers striped">	
		    <?php if($qualpro_wechat):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-wechat qualpro-wechat-icon"></span>&nbsp;<strong>微信</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
				        	<?php if($wechatopenid): ?>
        				        <span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_wechat_offiaccount_openid" class="button">解绑</button>   
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=wechat" class="button button-primary">绑定</a>';
                            endif; ?>   
				        </div> 
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if($qualpro_miniprogram):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-miniprogram qualpro-miniprogram-icon"></span>&nbsp;<strong>小程序</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($miniprogramopenid):?>
        				        <span style="color:green;font-weight:600;">已绑定</span>
    				            <button type="submit" name="unbinding_type" value="qualpro_wechat_miniprogram_openid" class="button">解绑</button>   
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=miniprogram" class="button button-primary">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if($qualpro_qq):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-qq qualpro-qq-icon"></span>&nbsp;<strong>QQ</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($qqopenid):?>
        				        <span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_qq_openid" class="button">解绑</button>  
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=qq" class="button button-primary">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if($qualpro_github):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-github qualpro-github-icon"></span>&nbsp;<strong>Github</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($githubopenid):?>
        				        <span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_github_openid" class="button">解绑</button>  
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=github" class="button button-primary">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if($qualpro_dingtalk):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-dingding qualpro-dingding-icon"></span>&nbsp;<strong>钉钉</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($dingtalkopenid):?>
        				        <span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_dingtalk_openid" class="button">解绑</button>  
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=dingtalk" class="button button-primary">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if($qualpro_weibo):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-weibo qualpro-weibo-icon"></span>&nbsp;<strong>微博</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($weiboopenid):?>
        				        <span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_weibo_openid" class="button">解绑</button>  
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=weibo" class="button button-primary">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if($qualpro_alipay):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-alipay qualpro-alipay-icon"></span>&nbsp;<strong>支付宝</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($alipayopenid):?>
        				        <span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_alipay_openid" class="button">解绑</button>  
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=alipay" class="button button-primary">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    <?php if($qualpro_gitee):?>
    		    <tr>
    				<th valign="top" style="width: 50%; padding-left: 100px;">
    				    <div class="account-table-th-title">
    				        <span class="iconfont icon-si-gitee qualpro-gitee-icon"></span>&nbsp;<strong>Gitee</strong>
    				    </div>
    				</th>
    				<td style="width:50%;">
    				    <div class="account-table-td-content">
    			        	<?php if($giteeopenid):?>
        				        <span style="color:green;font-weight:600;">已绑定</span>
        				        <button type="submit" name="unbinding_type" value="qualpro_gitee_openid" class="button">解绑</button>  
                            <?php else : 
                                echo '<span style="color:#a9a9a9">未绑定</span>';
                                echo '<a href="'.constant("qualpro_plugin").'page/qauth.php?type=gitee" class="button button-primary">绑定</a>';
                            endif; ?>
				        </div>
    				</td>
    			</tr>
			<?php endif; ?>	
		    </table>
		</form>
	</div>
</div>