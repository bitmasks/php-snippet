#创建数据库
CREATE TABLE `chat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(500) DEFAULT NULL,
  `receiver` varchar(11) DEFAULT NULL,
  `sender` varchar(11) DEFAULT NULL,
  `is_new` int(1) DEFAULT '1' COMMENT '是否新消息',
  `add_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;