CREATE TABLE `typecho_qauth_user` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `unionid` varchar(50) DEFAULT NULL
);
