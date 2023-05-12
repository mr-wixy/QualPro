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

$formUrl = admin_url('admin.php?page='.plugin_basename(__FILE__));

echo qualpro_account($formUrl);