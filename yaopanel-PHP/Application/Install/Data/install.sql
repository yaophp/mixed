-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2016-06-07 04:50:14
-- 服务器版本: 5.5.34
-- PHP 版本: 5.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `yaopanel`
--

-- --------------------------------------------------------

--
-- 表的结构 `config`
--
DROP TABLE IF  EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `value` text NOT NULL COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `type` (`type`),
  KEY `group` (`group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

--
-- 转存表中的数据 `config`
--

INSERT INTO `config` (`id`, `name`, `type`, `title`, `group`, `extra`, `remark`, `create_time`, `update_time`, `status`, `value`, `sort`) VALUES
(1, 'WEB_SITE_TITLE', 1, '网站标题', 1, '', '网站标题前台显示标题', 1378898976, 1379235274, 1, 'Yaopanel', 0),
(2, 'WEB_SITE_DESCRIPTION', 2, '网站描述', 1, '', '网站搜索引擎描述', 1378898976, 1379235841, 1, '自豪地使用 Yaopanel  ', 1),
(3, 'WEB_SITE_KEYWORD', 2, '网站关键字', 1, '', '网站搜索引擎关键字', 1378898976, 1465271011, 1, 'Yaopanel,shadowsocks,manyuser,php,mysql', 0),
(4, 'WEB_SITE_CLOSE', 4, '关闭站点', 4, '0:关闭,1:开启', '站点关闭后其他用户不能访问，管理员可以正常访问', 1378898976, 1403180883, 1, '1', 1),
(5, 'CONFIG_TYPE_LIST', 3, '配置类型列表', 99, '', '主要用于数据解析和页面表单的生成', 1378898976, 1403180785, 1, '0:数字\n\n1:字符\n\n2:文本\n\n3:数组\n\n4:枚举', 2),
(6, 'WEB_SITE_ICP', 1, '网站备案号', 1, '', '设置在网站底部显示的备案号，如“沪ICP备12007941号-2', 1378900335, 1465270993, 1, '', 0),
(7, 'CONFIG_GROUP_LIST', 3, '配置分组', 99, '', '配置分组', 1379228036, 1403180598, 1, '1:基本\n\n2:内容\n\n3:用户\n\n4:系统', 4),
(8, 'LIST_ROWS', 0, '后台每页记录数', 2, '', '后台数据每页显示记录数', 1379503896, 1380427745, 1, '15', 10),
(9, 'USER_ALLOW_REGISTER', 4, '是否允许用户注册', 3, '0:关闭注册\n\n1:允许注册', '是否开放用户注册', 1379504487, 1379504580, 1, '1', 3),
(10, 'DATA_BACKUP_PATH', 1, '数据库备份根路径', 4, '', '路径必须以 / 结尾', 1381482411, 1403180476, 1, './Data/', 5),
(11, 'DATA_BACKUP_PART_SIZE', 0, '数据库备份卷大小', 99, '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', 1381482488, 1403181308, 1, '20971520', 7),
(12, 'DATA_BACKUP_COMPRESS', 4, '数据库备份文件是否启用压缩', 4, '0:不压缩\n\n1:启用压缩', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', 1381713345, 1381729544, 1, '0', 9),
(13, 'DATA_BACKUP_COMPRESS_LEVEL', 4, '数据库备份文件压缩级别', 4, '1:普通\n\n4:一般\n\n9:最高', '数据库备份文件的压缩级别，该配置在开启压缩时生效', 1381713408, 1381713408, 1, '1', 10),
(14, 'DEVELOP_MODE', 4, '开启开发者模式', 99, '0:关闭\n\n1:开启', '是否开启开发者模式', 1383105995, 1403180320, 1, '1', 11),
(15, 'ADMIN_ALLOW_IP', 2, '后台允许访问IP', 4, '', '多个用逗号分隔，如果不配置表示不限制IP访问', 1387165454, 1387165553, 1, '', 12),
(16, 'SHOW_PAGE_TRACE', 4, '是否显示页面Trace', 99, '0:关闭\n\n1:开启', '是否显示页面Trace信息', 1387165685, 1403180765, 1, '0', 1),
(38, 'COUPON_RAND_TYPE', 4, '优惠码 折扣大小生成概率', 2, '0:百分比,1:千分比', '给用户惊喜就百分比，做奸商就千分比', 1465034001, 1465034001, 1, '0', 0),
(39, 'COUPON_EXPIRE', 0, '优惠码 有效期', 2, '', '单位：秒，默认一个月。', 1465034170, 1465034170, 1, '2592000', 0),
(40, 'NUM_BUILD_INVITE', 0, '年付会员可以生成的 邀请码 数量', 2, '', '每一次年付订单的用户可以获得的 邀请码数量', 1465034360, 1465034450, 1, '1', 0),
(41, 'NUM_BUILD_COUPON', 0, '年付会员可以生成的 优惠码 数量', 2, '', '每一次年付订单的用户可以获得 优惠码 数量', 1465034429, 1465034429, 1, '1', 0),
(42, 'SWITCH_PRODUCT_FREE', 4, '免费版 申请开关', 2, '1:开放申请,0:关闭申请', '关闭申请后，用户将不能申请免费版', 1465034682, 1465034682, 1, '1', 0),
(43, 'SWITCH_COUPON_USE', 4, '优惠码 使用开关', 2, '0:不可用;1:可用', '如果选择不可用，则用户在确定订单时输入的优惠码 无效', 1465182113, 1465182313, 1, '1', 0),
(44, 'ORDER_EXPIRE', 0, '订单超时时间（单位：秒）', 2, '', '下订单后，超过多少时间没有完成付费，则关闭本次交易。默认1小时', 1465207561, 1465207561, 1, '3600', 0),
(45, 'BASE_INFO_EMAIL', 1, '联系邮箱', 1, '', '显示在页面底部', 1465270620, 1465271063, 1, 'help@admin.com', 1),
(46, 'BASE_INFO_TELPHONE', 1, '联系固话', 1, '', '显示在页面底部', 1465270671, 1465271073, 1, '0755-8888888', 1),
(47, 'BASE_INFO_MOBILE', 1, '联系手机', 1, '', '显示在页面底部', 1465270724, 1465270873, 1, '13800138000', 1),
(48, 'BASE_INFO_ADDRESS', 1, '联系地址', 1, '', '显示在页面底部', 1465270782, 1465271086, 1, '深圳南山马花路213号', 1);

-- --------------------------------------------------------

--
-- 表的结构 `coupon`
--
DROP TABLE IF  EXISTS `coupon`;
CREATE TABLE IF NOT EXISTS `coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `useid` int(11) unsigned NOT NULL COMMENT '使用者id',
  `code` char(128) NOT NULL,
  `off` tinyint(3) unsigned NOT NULL DEFAULT '15',
  `time_create` int(10) unsigned NOT NULL,
  `time_use` int(10) unsigned NOT NULL COMMENT '使用时间',
  `time_expire` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`useid`,`time_create`,`time_use`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠码表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `invite`
--
DROP TABLE IF  EXISTS `invite`;
CREATE TABLE IF NOT EXISTS `invite` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `useid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用者id',
  `code` char(128) NOT NULL,
  `status` tinyint(3) NOT NULL,
  `time_create` int(10) unsigned NOT NULL,
  `time_use` int(11) unsigned NOT NULL COMMENT '使用时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`useid`,`time_create`,`time_use`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请码' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `node`
--
DROP TABLE IF  EXISTS `node`;
CREATE TABLE IF NOT EXISTS `node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(128) NOT NULL,
  `type` char(64) NOT NULL,
  `server` char(128) NOT NULL,
  `method` char(128) NOT NULL,
  `info` char(128) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `order` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节点表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `order_log`
--
DROP TABLE IF  EXISTS `order_log`;
CREATE TABLE IF NOT EXISTS `order_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `log` varchar(1200) NOT NULL,
  `time_create` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单日志表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `order_trade`
--
DROP TABLE IF  EXISTS `order_trade`;
CREATE TABLE IF NOT EXISTS `order_trade` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `benefit_id` int(11) unsigned NOT NULL COMMENT '受益人',
  `trade_number` char(64) NOT NULL,
  `detail` char(128) NOT NULL COMMENT '订单详情',
  `price` float(7,2) unsigned NOT NULL,
  `payment` char(16) NOT NULL DEFAULT 'alipay',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `time_create` int(10) unsigned NOT NULL,
  `time_update` int(10) unsigned NOT NULL,
  `time_expire` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`time_create`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `post`
--
DROP TABLE IF  EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `catalog` char(16) NOT NULL,
  `title` char(64) NOT NULL,
  `content` varchar(3200) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态',
  `time_create` int(10) unsigned NOT NULL,
  `time_update` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='文章表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `post`
--

INSERT INTO `post` (`id`, `uid`, `catalog`, `title`, `content`, `status`, `time_create`, `time_update`) VALUES
(1, 1, 'help', '使用帮助', '&lt;p&gt;&lt;b&gt;使用帮助...\r\n使用帮助...\r\nhelp啦啦啦&lt;/b&gt;&lt;br&gt;&lt;/p&gt;&lt;br&gt;', 1, 1465359582, 1465700743);

-- --------------------------------------------------------

--
-- 表的结构 `product`
--
DROP TABLE IF  EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(16) NOT NULL COMMENT '套餐名称',
  `transfer` int(10) NOT NULL DEFAULT '1' COMMENT '流量G/月 9999无限制',
  `monthly` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '月付 元',
  `quarterly` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '季付 元',
  `annually` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '年付 元',
  `service` varchar(800) NOT NULL DEFAULT '可自由切换线路节点 全平台支持（电脑/苹果/安卓）' COMMENT '产品/服务详情',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='流量套餐表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `product`
--

INSERT INTO `product` (`id`, `name`, `transfer`, `monthly`, `quarterly`, `annually`, `service`, `status`) VALUES
(1, '免费版', 10, 0, 0, 0, '可自由切换线路节点 全平台支持（电脑/苹果/安卓）', 1),
(2, '限量版', 30, 15, 45, 148, '可自由切换线路节点 全平台支持（电脑/苹果/安卓）', 1),
(3, '标准版', 9999, 28, 78, 278, '可自由切换线路节点 全平台支持（电脑/苹果/安卓）', 1);


-- --------------------------------------------------------

--
-- 表的结构 `profile`
--
DROP TABLE IF  EXISTS `profile`;
CREATE TABLE IF NOT EXISTS `profile` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `nickname` char(10) NOT NULL,
  `mobile` char(11) NOT NULL,
  `qq` char(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资料表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--
DROP TABLE IF  EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` char(32) NOT NULL,
  `pass` char(32) NOT NULL,
  `passwd` char(32) NOT NULL,
  `t` int(11) NOT NULL DEFAULT '0' COMMENT '上次使用时间',
  `u` bigint(20) NOT NULL DEFAULT '0' COMMENT '上传流量',
  `d` bigint(20) NOT NULL DEFAULT '0' COMMENT '下载流量',
  `transfer_enable` bigint(20) NOT NULL DEFAULT '0' COMMENT '总流量',
  `port` int(11) NOT NULL,
  `switch` tinyint(4) NOT NULL DEFAULT '1',
  `enable` tinyint(4) NOT NULL DEFAULT '1',
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `last_get_gift_time` int(11) NOT NULL DEFAULT '0',
  `last_rest_pass_time` int(11) NOT NULL DEFAULT '0',
  `time_create` int(10) unsigned NOT NULL DEFAULT '0',
  `time_last_login` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`,`port`),
  KEY `email` (`email`,`time_create`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_admin`
--
DROP TABLE IF  EXISTS `user_admin`;
CREATE TABLE IF NOT EXISTS `user_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员表' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
