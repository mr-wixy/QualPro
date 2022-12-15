<?php echo '<link rel="stylesheet" href="'.constant("qualpro_plugin").'res/qualpro.css">'; ?>
<div class="wrap">
<?php 
if(isset($_POST['submit']) && current_user_can('administrator') && $_POST['submit']=='save_config')
{
	$qualpro_wechat = isset($_POST['qualpro_wechat'])?trim($_POST['qualpro_wechat']):null;
	$qualpro_miniprogram   = isset($_POST['qualpro_miniprogram'])?trim($_POST['qualpro_miniprogram']):null;
	$qualpro_qq   = isset($_POST['qualpro_qq'])?trim($_POST['qualpro_qq']):null;
	$qualpro_github   = isset($_POST['qualpro_github'])?trim($_POST['qualpro_github']):null;
	$qualpro_dingtalk   = isset($_POST['qualpro_dingtalk'])?trim($_POST['qualpro_dingtalk']):null;
	$qualpro_weibo   = isset($_POST['qualpro_weibo'])?trim($_POST['qualpro_weibo']):null;
	$qualpro_alipay   = isset($_POST['qualpro_alipay'])?trim($_POST['qualpro_alipay']):null;
	$qualpro_gitee   = isset($_POST['qualpro_gitee'])?trim($_POST['qualpro_gitee']):null;
	$qualpro_sms   = isset($_POST['qualpro_sms'])?trim($_POST['qualpro_sms']):null;
	
	update_option('qualpro_wechat', $qualpro_wechat);
	update_option('qualpro_miniprogram', $qualpro_miniprogram);
	update_option('qualpro_qq', $qualpro_qq);
	update_option('qualpro_github', $qualpro_github);
	update_option('qualpro_dingtalk', $qualpro_dingtalk);
	update_option('qualpro_weibo', $qualpro_weibo);
	update_option('qualpro_alipay', $qualpro_alipay);
	update_option('qualpro_gitee', $qualpro_gitee);
	update_option('qualpro_sms', $qualpro_sms);

	echo '<div class="updated settings-error"><p>保存成功</p></div>';

}

$qualpro_wechat  = get_option('qualpro_wechat');
$qualpro_miniprogram  = get_option('qualpro_miniprogram');
$qualpro_qq  = get_option('qualpro_qq');
$qualpro_github  = get_option('qualpro_github');
$qualpro_dingtalk  = get_option('qualpro_dingtalk');
$qualpro_weibo  = get_option('qualpro_weibo');
$qualpro_alipay  = get_option('qualpro_alipay');
$qualpro_gitee  = get_option('qualpro_gitee');
$qualpro_sms  = get_option('qualpro_sms');
?>

<div class="qualpro-settings-header">
	<div class="qualpro-settings-title-section">
		<div style="display: inline-block; font-weight: 600; margin: 0 .8rem 1rem; font-size: 23px; padding: 9px 0 4px; line-height: 1.3;color: #1d2327;">QualPro设置</div>
	</div>
	<nav class="qualpro-settings-tabs-wrapper hide-if-no-js" aria-label="次要菜单">
		<a href="<?php echo admin_url('admin.php?page=qualpro/page/setting.php'); ?>" class="qualpro-settings-tab" aria-current="true">基础配置</a>
		<a href="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>" class="qualpro-settings-tab active">接入平台</a>
	</nav>
</div>

<div class="qualpro-settings-body hide-if-no-js">
	<!--<h2>第三方登录设置</h2>-->
	<!--<hr>-->
    <form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
	    <table class="form-table">	
		    <tr>
				<td valign="top" style="text-align: center;"><strong>微信登录</strong>
				</td>
				<td style="text-align: center;"><input type="checkbox" id="qualpro_wechat" name="qualpro_wechat" value="1"  <?php if($qualpro_wechat) echo 'checked'; ?>/>开启
				</td>
				<td valign="top" style="text-align: center;"><strong>小程序登录</strong>
				</td>
				<td style="text-align: center;"><input type="checkbox" id="qualpro_miniprogram" name="qualpro_miniprogram" value="1"  <?php if($qualpro_miniprogram) echo 'checked'; ?>/>开启
				</td>
			</tr>
			<tr>
				<td valign="top" style="text-align: center;"><strong>QQ登录</strong>
				</td>
				<td style="text-align: center;"><input type="checkbox" id="qualpro_qq" name="qualpro_qq" value="1"  <?php if($qualpro_qq) echo 'checked'; ?>/>开启
				</td>
				<td valign="top" style="text-align: center;"><strong>Github登录</strong>
				</td>
				<td style="text-align: center;"><input type="checkbox" id="qualpro_github" name="qualpro_github" value="1"  <?php if($qualpro_github) echo 'checked'; ?>/>开启
				</td>
			</tr>
			<tr>
				<td valign="top" style="text-align: center;"><strong>钉钉登录</strong>
				</td>
				<td style="text-align: center;"><input type="checkbox" id="qualpro_dingtalk" name="qualpro_dingtalk" value="1"  <?php if($qualpro_dingtalk) echo 'checked'; ?>/>开启
				</td>
				<td valign="top" style="text-align: center;"><strong>微博登录</strong>
				</td>
				<td style="text-align: center;"><input type="checkbox" id="qualpro_weibo" name="qualpro_weibo" value="1"  <?php if($qualpro_weibo) echo 'checked'; ?>/>开启
				</td>
			</tr>
			<tr>
				<td valign="top" style="text-align: center;"><strong>支付宝登录</strong>
				</td>
				<td style="text-align: center;"><input type="checkbox" id="qualpro_alipay" name="qualpro_alipay" value="1"  <?php if($qualpro_alipay) echo 'checked'; ?>/>开启
				</td>
				<td valign="top" style="text-align: center;"><strong>Gitee登录</strong>
				</td>
				<td style="text-align: center;"><input type="checkbox" id="qualpro_gitee" name="qualpro_gitee" value="1"  <?php if($qualpro_gitee) echo 'checked'; ?>/>开启
				</td>
			</tr>
		    <tr>
				<td valign="top" style="text-align: center;"><strong>验证码登录</strong>
				</td>
				<td style="text-align: center;"><input type="checkbox" id="qualpro_sms" name="qualpro_sms" value="1"  <?php if($qualpro_sms) echo 'checked'; ?>/>开启
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<p class="submit" style="text-align: center;">
						<button type="submit" name="submit" value="save_config" class="button-primary">保存配置</button>
					</p>
				</td>
			</tr>
	    </table>
	</form>
</div>

</div>