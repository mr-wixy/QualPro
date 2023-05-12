# WordPress 插件

## QualPro插件

该WordPress插件是基于QuickAuth的API开发的，支持QuickAuth所有的第三方登录方式

## 更新记录

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
