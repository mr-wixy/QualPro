# WordPress 插件

## QualPro插件

该WordPress插件是基于QuickAuth的API开发的，支持QuickAuth所有的第三方登录方式

## 更新记录

### v1.0.2 2023-06-17

1. 登录组件新增ShortCode调用方式，调用代码为 `[qualpro_login]`
2. 新增启用中文用户名选项，自动注册用户名允许为中文（启用前请确保网站已经修改支持中文用户名）
3. BUG修复及优化

修改主题functions.php文件以支持中文用户名

添加以下代码

```php
/*---------------------------------*/	
/* wordpress支持中文名注册！
/*---------------------------------*/
function loper_sanitize_user($username, $raw_username, $strict) {
    $username = wp_strip_all_tags($raw_username);
    $username = remove_accents($username);
    $username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
    $username = preg_replace('/&.+?;/', '', $username); // Kill entities
    if ($strict) {
        $username = preg_replace('|[^a-z\p{Han}0-9 _.\-@]|iu', '', $username);
    }
    $username = trim($username);
    $username = preg_replace('|\s+|', ' ', $username);
    return $username;
}
add_filter('sanitize_user', 'loper_sanitize_user', 10, 3);
```  

### v1.0.1 2023-05-12

1. 新增state验证配置功能，用户可以在配置界面取消state验证功能
2. 新增用户第三方账号绑定界面调用方法，调用方式如下

```php
$formUrl = admin_url('admin.php?page='.plugin_basename(__FILE__));
echo qualpro_account($formUrl);
```  

## 安装配置

安装和配置教程请参考

> [QualPro-一个基于QuickAuth的WordPress集成登录插件](https://blog.wixy.cn/archives/79.html)
