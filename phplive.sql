/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : phplive

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-03-15 15:41:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for zb_admin
-- ----------------------------
DROP TABLE IF EXISTS `zb_admin`;
CREATE TABLE `zb_admin` (
  `guid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL DEFAULT '',
  `phone` char(11) NOT NULL DEFAULT '',
  `ip` char(40) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `delete_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`guid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_admin
-- ----------------------------
INSERT INTO `zb_admin` VALUES ('1', 'chang', 'f379eaf3c831b04de153469d1bec345e', '', '127.0.0.1', '0', '1521098973', null);

-- ----------------------------
-- Table structure for zb_cate
-- ----------------------------
DROP TABLE IF EXISTS `zb_cate`;
CREATE TABLE `zb_cate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `delete_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_cate
-- ----------------------------

-- ----------------------------
-- Table structure for zb_room
-- ----------------------------
DROP TABLE IF EXISTS `zb_room`;
CREATE TABLE `zb_room` (
  `guid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bgimgurl` varchar(60) NOT NULL DEFAULT '',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '房间名称',
  `notice` varchar(255) NOT NULL DEFAULT '' COMMENT '房间通知',
  `description` varchar(255) NOT NULL DEFAULT '',
  `people` int(10) unsigned NOT NULL,
  `disable` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否被封',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '直播状态',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `delete_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`guid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_room
-- ----------------------------
INSERT INTO `zb_room` VALUES ('1', '', 'chang的课堂', 'chang开通直播课堂啦', '这是chang的直播课堂描述', '0', '1', '0', '1521096442', '1521098357', null);
INSERT INTO `zb_room` VALUES ('2', '', 'chang的课堂', 'chang开通直播课堂啦', '这是chang的直播课堂描述', '0', '0', '0', '1521096939', '1521096939', null);
INSERT INTO `zb_room` VALUES ('3', '', '今晚带你吃鸡', '首次开播多多指教', '来自绝地求生的玩家chang', '0', '0', '0', '1521097023', '1521097023', null);

-- ----------------------------
-- Table structure for zb_roomtype
-- ----------------------------
DROP TABLE IF EXISTS `zb_roomtype`;
CREATE TABLE `zb_roomtype` (
  `room` int(10) unsigned NOT NULL COMMENT 'zb_room表id',
  `type` int(11) NOT NULL COMMENT 'zb_type表id',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `delete_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `room_type` (`room`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_roomtype
-- ----------------------------

-- ----------------------------
-- Table structure for zb_type
-- ----------------------------
DROP TABLE IF EXISTS `zb_type`;
CREATE TABLE `zb_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_type
-- ----------------------------
INSERT INTO `zb_type` VALUES ('1', '绝地求生', '0', '0', null);
INSERT INTO `zb_type` VALUES ('2', '教育专区', '1521098597', '1521098597', null);

-- ----------------------------
-- Table structure for zb_user
-- ----------------------------
DROP TABLE IF EXISTS `zb_user`;
CREATE TABLE `zb_user` (
  `guid` char(32) NOT NULL COMMENT '全球唯一识别符',
  `nickname` varchar(60) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL DEFAULT '',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1：男 2：女 0/其他:隐私',
  `headimgurl` varchar(60) NOT NULL DEFAULT '',
  `phone` char(11) NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `delete_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`guid`),
  UNIQUE KEY `u_guid` (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_user
-- ----------------------------
INSERT INTO `zb_user` VALUES ('45e74c6c67ffbfbf6f1d1abff25d1f98', 'chang', 'f379eaf3c831b04de153469d1bec345e', '1', '20180315\\30337f99d2b934a81a40788ab4bb5786.png', '11111111111', '1', '1521084755', '1521084755', null);

-- ----------------------------
-- Table structure for zb_usercollection
-- ----------------------------
DROP TABLE IF EXISTS `zb_usercollection`;
CREATE TABLE `zb_usercollection` (
  `room` int(10) unsigned NOT NULL COMMENT '房间号',
  `user` char(32) NOT NULL COMMENT '用户guid',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `delete_time` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_usercollection
-- ----------------------------

-- ----------------------------
-- Table structure for zb_userhistory
-- ----------------------------
DROP TABLE IF EXISTS `zb_userhistory`;
CREATE TABLE `zb_userhistory` (
  `user` char(32) NOT NULL,
  `room` int(10) unsigned NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `delete_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `user_room` (`user`,`room`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_userhistory
-- ----------------------------

-- ----------------------------
-- Table structure for zb_usersuggest
-- ----------------------------
DROP TABLE IF EXISTS `zb_usersuggest`;
CREATE TABLE `zb_usersuggest` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` char(32) NOT NULL DEFAULT '',
  `title` varchar(60) NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(11) NOT NULL,
  `delete_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_usersuggest
-- ----------------------------

-- ----------------------------
-- Table structure for zb_usertipoff
-- ----------------------------
DROP TABLE IF EXISTS `zb_usertipoff`;
CREATE TABLE `zb_usertipoff` (
  `user` char(32) NOT NULL DEFAULT '',
  `room` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `delete_time` int(11) NOT NULL,
  UNIQUE KEY `user_room` (`user`,`room`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_usertipoff
-- ----------------------------

-- ----------------------------
-- Table structure for zb_userzhubo
-- ----------------------------
DROP TABLE IF EXISTS `zb_userzhubo`;
CREATE TABLE `zb_userzhubo` (
  `room` int(10) NOT NULL,
  `user` char(32) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `delete_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `room_user` (`room`,`user`) USING BTREE COMMENT '房间号和用户号唯一'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_userzhubo
-- ----------------------------
INSERT INTO `zb_userzhubo` VALUES ('3', '45e74c6c67ffbfbf6f1d1abff25d1f98', '1521097023', '1521097023', '0');

-- ----------------------------
-- Table structure for zb_userzhubocheck
-- ----------------------------
DROP TABLE IF EXISTS `zb_userzhubocheck`;
CREATE TABLE `zb_userzhubocheck` (
  `user` char(32) NOT NULL DEFAULT '',
  `image` varchar(60) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0：申请认证 1：认证成功 2:拒绝认证',
  `create_time` int(10) unsigned NOT NULL,
  `update_time` int(10) unsigned NOT NULL,
  `delete_time` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zb_userzhubocheck
-- ----------------------------
INSERT INTO `zb_userzhubocheck` VALUES ('45e74c6c67ffbfbf6f1d1abff25d1f98', '20180315\\dfcf4d343d97c0fb55c38e4c28052414.png', '1', '1521095758', '1521095758', '0');
