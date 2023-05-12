<?php
/*
Plugin Name: QualPro
Plugin URI: https://github.com/mr-wixy/QualPro
Description: QualPro是基于QuickAuth平台的集成登录插件
Version: 1.0.1
Author: wixy
Author URI: https://blog.wixy.cn/
*/

define("qualpro_plugin",plugin_dir_url( __FILE__ ));

include_once('core.php');

add_filter('login_form', 'qualpro_login_button');

add_action('admin_menu', 'qualpro_menu');

register_activation_hook( __FILE__, 'qauth_install' );

register_deactivation_hook( __FILE__, 'qauth_deactivation' );
