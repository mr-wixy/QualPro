<?php echo '<link rel="stylesheet" href="'.constant("qualpro_plugin").'res/qualpro.css">'; ?>
<div class="wrap">
    <div class="qualpro-settings-header">
    	<div class="qualpro-settings-title-section">
    		<div class="qualpro-settings-title-section-title">QualPro设置</div>
    	</div>
    	<nav class="qualpro-settings-tabs-wrapper hide-if-no-js" aria-label="次要菜单">
    		<a href="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>" class="qualpro-settings-tab active" aria-current="true">基础配置</a>
    		<a href="<?php echo admin_url('admin.php?page=qualpro/page/setting-oauth.php'); ?>" class="qualpro-settings-tab">接入平台</a>
    	</nav>
    </div>
    
    <div class="qualpro-settings-body hide-if-no-js">
        <h2>QuickAuth配置信息</h2>
         <?php 
            if(isset($_POST['submit']) && current_user_can('administrator') && $_POST['submit']=='save_config')
            {
            	$qauth_appkey   = isset($_POST['qauth_appkey'])?trim($_POST['qauth_appkey']):null;
            	$qauth_user_secret   = isset($_POST['qauth_user_secret'])?trim($_POST['qauth_user_secret']):null;
            	$qauth_api   = isset($_POST['qauth_api'])?trim($_POST['qauth_api']):null;
            	$qauth_auto_register   = isset($_POST['qauth_auto_register'])?trim($_POST['qauth_auto_register']):null;
            	$qauth_state_check   = isset($_POST['qauth_state_check'])?trim($_POST['qauth_state_check']):null;
            	$qauth_support_cn_name   = isset($_POST['qauth_support_cn_name'])?trim($_POST['qauth_support_cn_name']):null;
            	$qualpro_redirect_type = isset($_POST['qualpro_redirect_type'])?trim($_POST['qualpro_redirect_type']):null;
            	$qualpro_redirect_url = isset($_POST['qualpro_redirect_url'])?trim($_POST['qualpro_redirect_url']):null;
            	
            	update_option('qauth_appkey', $qauth_appkey);
            	update_option('qauth_user_secret', $qauth_user_secret);
            	update_option('qauth_api', $qauth_api);
            	update_option('qauth_auto_register', $qauth_auto_register);
            	update_option('qauth_state_check', $qauth_state_check);
            	update_option('qauth_support_cn_name', $qauth_support_cn_name);
            	update_option('qualpro_redirect_type', $qualpro_redirect_type);
            	update_option('qualpro_redirect_url', $qualpro_redirect_url);
            	echo '<div class="updated settings-error"><p>保存成功</p></div>';
            }
            $qauth_appkey  = get_option('qauth_appkey');
            $qauth_user_secret  = get_option('qauth_user_secret');
            $qauth_api  = get_option('qauth_api');
            $qauth_auto_register  = get_option('qauth_auto_register');
            $qauth_state_check  = get_option('qauth_state_check');
            $qauth_support_cn_name  = get_option('qauth_support_cn_name');
            $qualpro_redirect_type  = get_option('qualpro_redirect_type');
            $qualpro_redirect_url  = get_option('qualpro_redirect_url');
                        
            $config_ok = false;
            $config_data = [];
            if($qauth_api != null && $qauth_api != "" && $qauth_appkey != null && $qauth_appkey != "" && $qauth_user_secret != null && $qauth_user_secret != ""){
                $response = wp_remote_get($qauth_api.'/basic/app?appkey='.$qauth_appkey.'&secret='.$qauth_user_secret);
                $body = wp_remote_retrieve_body( $response );
                $content_obj = json_decode($body);
                
                if($content_obj && $content_obj->code == 0){
                    $config_ok = true;
                    $config_data = $content_obj->res;
                }
            }
            
            ?>
    	<p class="description">1、登录 <a target="_blank" href="https://qauth.cn">QuickAuth</a>网站</p>
        <p class="description">2、<a target="_blank" href="https://qauth.cn/app">创建应用</a> 填写相关信息 保存并发布</p>
        <p class="description">3、在此页面中配置 AppKey和UserSecret </p>
    	<hr>
        <?php if($config_ok):?>
        <table class="form-table widefat importers striped">	
        <tbody>
            <tr class="importer-item">
				<td class="import-system" style="background-color:#e7e7e773;">
					QuickAuth用户
				</td>
				<td class="desc" style="text-align: center;">
					<span class="importer-desc"><?php echo $config_data->user_info->name ?></span>
				</td>
				<td class="import-system" style="background-color:#e7e7e773;">
					手机号
				</td>
				<td class="desc" style="text-align: center;">
					<span class="importer-desc"><?php echo $config_data->user_info->mobile ?></span>
				</td>
			</tr>
			<tr class="importer-item">
				<td class="import-system" style="background-color:#e7e7e773;">
				    订阅状态
				</td>
				<td class="desc" style="text-align: center;">
				    <?php 
				        if($config_data->user_info->state == 'normal'){
				            echo '<span class="importer-desc" style="color:green;font-weight:600;">正常</span>';
				        }
				        else{
				            echo '<span class="importer-desc" style="color:gray;">已过期&nbsp;<a target="_blank" href="https://qauth.cn/subscribe">续费</a></span>';
				        }
			        ?>
				</td>
				<td class="import-system" style="background-color:#e7e7e773;">
				    订阅到期时间
				</td>
				<td class="desc" style="text-align: center;">
					<span class="importer-desc"><?php echo $config_data->user_info->due_date ?></span>
				</td>
			</tr>
			<tr class="importer-item">
				<td class="import-system" style="background-color:#e7e7e773;">
				    应用名称
				</td>
				<td class="desc" style="text-align: center;">
					<span class="importer-desc"><?php echo $config_data->app_name ?></span>
				</td>
				<td class="import-system" style="background-color:#e7e7e773;">
				    应用状态
				</td>
				<td class="desc" style="text-align: center;">
				    <?php 
				        if($config_data->app_state == 'published'){
				            echo '<span class="importer-desc" style="color:green;font-weight:600;">已发布</span>';
				        }
				        else{
				            echo '<span class="importer-desc" style="color:gray;">未发布</span>';
				        }
			        ?>
				</td>
			</tr>
			</tbody>
        </table>
    	<hr>
        <?php endif; ?>  
        <form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
    		<table class="form-table">
    			<tr>
    				<th valign="top"><strong>QuickAuthApi</strong>
    				</th>
    				<td><input type="text" id="qauth_api" name="qauth_api"
    					value="<?php echo $qauth_api ; ?>" class="regular-text"/>（默认配置，正常情况无需修改）
    				</td>
    			</tr>
    			<tr>
    				<th valign="top"><strong>AppKey</strong>
    				</th>
    				<td><input type="text" id="qauth_appkey" name="qauth_appkey"
    					value="<?php echo $qauth_appkey ; ?>" class="regular-text"/>&nbsp;<a href="https://qauth.cn/app" target="_blank">获取AppKey</a>
    				</td>
    			</tr>
    			<tr>
    				<th valign="top"><strong>UserSecret</strong>
    				</th>
    				<td><input type="text" id="qauth_user_secret" name="qauth_user_secret"
    					value="<?php echo $qauth_user_secret ; ?>" class="regular-text"/>&nbsp;<a href="https://qauth.cn/config/secret" target="_blank">获取UserSecret</a>
    				</td>
    			</tr>
    			<tr>
    				<th valign="top"><strong>未绑定用户自动注册</strong>
    				</th>
    				<td><input type="checkbox" id="qauth_auto_register" name="qauth_auto_register" value="1"  <?php if($qauth_auto_register) echo 'checked'; ?>/>开启
    				</td>
    			</tr>
    			<tr>
    				<th valign="top"><strong>启用state验证</strong>
    				</th>
    				<td><input type="checkbox" id="qauth_state_check" name="qauth_state_check" value="1"  <?php if($qauth_state_check) echo 'checked'; ?>/>开启
    				</td>
    			</tr>
    			<tr>
				<th valign="top">
					<p style="margin: 0;">允许使用中文用户名</p>
					<small style="color: orangered;">请确保网站已经支持中文用户名</small></th>
    				<td><input type="checkbox" id="qauth_support_cn_name" name="qauth_support_cn_name" value="1"  <?php if($qauth_support_cn_name) echo 'checked'; ?>/>开启
    				</td>
    			</tr>
    			<tr>
    				<th valign="top"><strong>成功登录后跳转地址</strong>
    				</th>
    				<td>
    				   <select class="regular-text" id="qualpro_redirect_type" name="qualpro_redirect_type" onchange="redirectChange()">
                            <option value="1" <?php if($qualpro_redirect_type == 1) echo 'selected'; ?>>首页</option>
                            <option value="2" <?php if($qualpro_redirect_type == 2) echo 'selected'; ?>>后台</option>
                            <option value="3" <?php if($qualpro_redirect_type == 3) echo 'selected'; ?>>自定义</option>
                        </select>
    				</td>
    			</tr>
    			<tr>
    				<th valign="top"><strong>自定义跳转地址</strong>
    				</th>
    				<td><input class="regular-text" type="text" id="qualpro_redirect_url" name="qualpro_redirect_url" value="<?php echo $qualpro_redirect_url ; ?>"/>
    				</td>
    			</tr>
    			<tr>
    				<td colspan="2">
    					<p class="submit" style="text-align:center;">
    						<button type="submit" name="submit" value="save_config" class="button-primary">保存配置</button>
    					</p>
    				</td>
    			</tr>
    		</table>
    	</form>
    </div>
</div>

<script type="text/JavaScript">
    redirectChange();
    function redirectChange(){
        var objS = document.getElementById("qualpro_redirect_type");
        var type = objS.options[objS.selectedIndex].value;
        var urlInput = document.getElementById("qualpro_redirect_url");
        if(type == 3){
            urlInput.readOnly = false;
        }
        else{
            urlInput.readOnly = true;
            if(type == 1){
                urlInput.value = "/";
            }
            else if(type == 2){
                urlInput.value = "/wp-admin";
            }
        }
    }
</script>
