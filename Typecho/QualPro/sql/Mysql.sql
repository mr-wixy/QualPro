CREATE TABLE `typecho_qauth_user` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'qauth_user表主键',
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `type` varchar(20) NOT NULL COMMENT '第三方登录类型',
  `openid` varchar(100) COMMENT '第三方登录openid',
  `unionid` varchar(100) COMMENT '第三方登录unionid',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=%charset%;