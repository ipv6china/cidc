
-- ----------------------------
-- Table structure for `idc_account`
-- ----------------------------
DROP TABLE IF EXISTS `idc_account`;
CREATE TABLE `idc_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` varchar(50) NOT NULL,
  `machine_id` varchar(20) NOT NULL COMMENT '机器ID',
  `type` int(5) NOT NULL COMMENT '帐号类型（0：系统帐号）',
  `rtx` varchar(20) NOT NULL COMMENT 'RTX用户名',
  `realname` varchar(20) NOT NULL COMMENT '姓名',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态（0:正常，1:锁定）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=665 DEFAULT CHARSET=utf8 COMMENT='帐号表';

-- ----------------------------
-- Table structure for `idc_cabinet`
-- ----------------------------
DROP TABLE IF EXISTS `idc_cabinet`;
CREATE TABLE `idc_cabinet` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idc_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '机房ID',
  `name` varchar(20) NOT NULL COMMENT '机柜名称',
  `description` text NOT NULL COMMENT '机柜描述',
  `place` varchar(50) NOT NULL COMMENT '存放地点',
  `height` int(10) unsigned NOT NULL,
  `height_used` int(10) NOT NULL COMMENT '已使用高度',
  `admin_dept` varchar(50) NOT NULL COMMENT '使用部门',
  `admin_username` varchar(20) NOT NULL COMMENT '负责人',
  `admin_realname` varchar(20) NOT NULL COMMENT '负责人姓名',
  `equipment_amount` int(10) NOT NULL COMMENT '设备数量',
  `machine_amount` int(10) NOT NULL COMMENT '机器数量',
  `locked` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否锁定',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='机柜';

-- ----------------------------
-- Table structure for `idc_cabinet_detail`
-- ----------------------------
DROP TABLE IF EXISTS `idc_cabinet_detail`;
CREATE TABLE `idc_cabinet_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cabinet_id` int(10) unsigned NOT NULL,
  `cabinet_name` varchar(20) NOT NULL COMMENT '机柜名称',
  `device_tag` varchar(50) NOT NULL COMMENT '设备标签',
  `equipment_type` int(10) NOT NULL COMMENT '设备类型(0:机器,1:switch,2:router)',
  `equipment_name` varchar(20) NOT NULL COMMENT '设备名称',
  `equipment_id` int(10) unsigned NOT NULL,
  `equipment_height` int(10) unsigned NOT NULL COMMENT '设备高度',
  `equipment_status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '状态(0:正常，1：闲置，2：损坏)',
  `position` int(10) unsigned NOT NULL COMMENT '存放顺序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COMMENT='机柜详情';

-- ----------------------------
-- Table structure for `idc_domain_record`
-- ----------------------------
DROP TABLE IF EXISTS `idc_domain_record`;
CREATE TABLE `idc_domain_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `zone_id` int(10) unsigned NOT NULL COMMENT 'domain_zone id',
  `zone_name` varchar(50) NOT NULL COMMENT '主域名',
  `name` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  `priority` int(10) unsigned NOT NULL DEFAULT '10',
  `active` int(4) unsigned NOT NULL DEFAULT '1',
  `description` text NOT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='DNS记录';

-- ----------------------------
-- Table structure for `idc_domain_zone`
-- ----------------------------
DROP TABLE IF EXISTS `idc_domain_zone`;
CREATE TABLE `idc_domain_zone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) NOT NULL COMMENT '域名',
  `ns` varchar(50) NOT NULL COMMENT 'NS地址',
  `server` varchar(50) NOT NULL COMMENT '服务器',
  `admin_username` varchar(20) NOT NULL COMMENT '管理员用户名',
  `admin_realname` varchar(20) NOT NULL COMMENT '管理员姓名',
  `parent` int(10) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='DNS域名';

-- ----------------------------
-- Table structure for `idc_graphviz_edge`
-- ----------------------------
DROP TABLE IF EXISTS `idc_graphviz_edge`;
CREATE TABLE `idc_graphviz_edge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gid` int(10) unsigned NOT NULL,
  `node1` varchar(100) NOT NULL COMMENT '节点名称',
  `node2` varchar(200) NOT NULL COMMENT '标签',
  `type` int(10) unsigned NOT NULL COMMENT '类型(1:1to1;2:1ton;3:link)',
  `label` varchar(255) NOT NULL COMMENT '标签',
  `attrs` text NOT NULL COMMENT '边属性',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='Graphviz_边';

-- ----------------------------
-- Table structure for `idc_graphviz_graph`
-- ----------------------------
DROP TABLE IF EXISTS `idc_graphviz_graph`;
CREATE TABLE `idc_graphviz_graph` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '图名',
  `label` varchar(200) NOT NULL COMMENT '标签',
  `type` varchar(20) NOT NULL COMMENT '类型',
  `directed` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '是否有向图',
  `strict` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '严格模式',
  `file_format` varchar(10) NOT NULL DEFAULT 'svg' COMMENT '图像格式',
  `attrs` text NOT NULL COMMENT '属性',
  `advanced` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '高级模式',
  `code` text NOT NULL COMMENT 'GraphViz源码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Graphviz_图';

-- ----------------------------
-- Table structure for `idc_graphviz_node`
-- ----------------------------
DROP TABLE IF EXISTS `idc_graphviz_node`;
CREATE TABLE `idc_graphviz_node` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gid` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '节点名称',
  `label` varchar(200) NOT NULL COMMENT '标签',
  `use_count` int(10) unsigned NOT NULL COMMENT '使用次数',
  `attrs` text NOT NULL COMMENT '属性',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8 COMMENT='Graphviz_节点';

-- ----------------------------
-- Table structure for `idc_group`
-- ----------------------------
DROP TABLE IF EXISTS `idc_group`;
CREATE TABLE `idc_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='分组';

-- ----------------------------
-- Table structure for `idc_group_detail`
-- ----------------------------
DROP TABLE IF EXISTS `idc_group_detail`;
CREATE TABLE `idc_group_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gid` int(10) unsigned NOT NULL,
  `gname` varchar(50) NOT NULL COMMENT '分组名称',
  `equipment_type` int(4) NOT NULL COMMENT '设备类型',
  `equipment_id` int(10) unsigned NOT NULL,
  `equipment_name` varchar(50) NOT NULL,
  `device_tag` varchar(50) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='分组详情';

-- ----------------------------
-- Table structure for `idc_idc`
-- ----------------------------
DROP TABLE IF EXISTS `idc_idc`;
CREATE TABLE `idc_idc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '机房名称',
  `address` varchar(200) NOT NULL COMMENT '地址',
  `contact` varchar(20) NOT NULL COMMENT '联系人',
  `tel` varchar(20) NOT NULL COMMENT '联系电话',
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='机房信息';

-- ----------------------------
-- Table structure for `idc_ip`
-- ----------------------------
DROP TABLE IF EXISTS `idc_ip`;
CREATE TABLE `idc_ip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `equipment_type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'machine类型',
  `equipment_id` int(10) unsigned NOT NULL COMMENT '设备表ID',
  `name` varchar(100) NOT NULL COMMENT 'hostname',
  `machine_id` varchar(20) NOT NULL COMMENT 'MachineId',
  `interface` varchar(20) NOT NULL COMMENT '网卡',
  `ip` int(10) unsigned NOT NULL COMMENT 'ip',
  `ip_string` char(15) CHARACTER SET latin1 NOT NULL COMMENT 'ip',
  `netmask` int(10) unsigned NOT NULL,
  `netmask_string` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=275 DEFAULT CHARSET=utf8 COMMENT='机器IP';

-- ----------------------------
-- Table structure for `idc_log`
-- ----------------------------
DROP TABLE IF EXISTS `idc_log`;
CREATE TABLE `idc_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `priority` int(10) unsigned NOT NULL DEFAULT '0',
  `type` int(10) unsigned NOT NULL COMMENT '类型(0:设备变更,1:服务器配置变更,2:网络变更)',
  `issystem` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否系统自动生成LOG',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `realname` varchar(20) NOT NULL,
  `log_time` int(10) unsigned NOT NULL COMMENT '时间',
  `equipment_type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '设备类型',
  `equipment_id` int(10) unsigned NOT NULL COMMENT '设备ID',
  `equipment_name` varchar(50) NOT NULL COMMENT '设备名称',
  `message` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=228 DEFAULT CHARSET=utf8 COMMENT='变更日志';

-- ----------------------------
-- Table structure for `idc_machine`
-- ----------------------------
DROP TABLE IF EXISTS `idc_machine`;
CREATE TABLE `idc_machine` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '设备类型（0:machine;1:cabinet,2:networkequipment）',
  `device_tag` varchar(50) NOT NULL COMMENT '设备标签',
  `machine_id` varchar(20) NOT NULL DEFAULT '0' COMMENT '设备标识',
  `name` varchar(100) NOT NULL COMMENT '主机名',
  `height` int(10) unsigned NOT NULL DEFAULT '2' COMMENT '服务器高度',
  `host_machine` varchar(20) NOT NULL COMMENT '宿主ID（仅VM）',
  `description` text NOT NULL COMMENT '机器描述',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态(0:正常，1：闲置，2：损坏)',
  `cpu` varchar(50) NOT NULL COMMENT 'cpu信息',
  `memory` varchar(20) NOT NULL COMMENT '内存',
  `harddisk` varchar(100) NOT NULL COMMENT '硬盘',
  `serial_number` varchar(50) NOT NULL COMMENT '序列号',
  `manufacturer` varchar(50) NOT NULL COMMENT '制造商',
  `product_name` varchar(50) NOT NULL COMMENT '产品型号',
  `os` varchar(50) NOT NULL,
  `plateform` varchar(20) NOT NULL COMMENT 'x86_64,x86',
  `version` varchar(20) NOT NULL COMMENT '内核版本',
  `accounts` text NOT NULL COMMENT '系统帐号（，隔开）',
  `network_interfaces` text NOT NULL COMMENT '网卡',
  `admin_dept` varchar(20) NOT NULL COMMENT '所在部门',
  `admin_username` varchar(20) NOT NULL COMMENT '负责人',
  `admin_realname` varchar(20) NOT NULL COMMENT '负责人',
  `place` varchar(20) NOT NULL COMMENT '所在机房',
  `cabinet_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所在机柜ID',
  `cabinet_name` varchar(20) NOT NULL COMMENT '机柜名称',
  `cabinet_position` int(10) unsigned NOT NULL COMMENT '所在机柜高度',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=138 DEFAULT CHARSET=utf8 COMMENT='机器信息';

-- ----------------------------
-- Table structure for `idc_machine_network_interface`
-- ----------------------------
DROP TABLE IF EXISTS `idc_machine_network_interface`;
CREATE TABLE `idc_machine_network_interface` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `machine_id` varchar(20) NOT NULL,
  `interface` varchar(20) NOT NULL,
  `speed` varchar(20) NOT NULL,
  `mac` varchar(20) NOT NULL,
  `ip` varchar(200) NOT NULL COMMENT 'IP',
  `netmask` int(10) NOT NULL COMMENT '子网掩码',
  `route` text NOT NULL COMMENT '路由表',
  PRIMARY KEY (`id`,`machine_id`)
) ENGINE=MyISAM AUTO_INCREMENT=289 DEFAULT CHARSET=utf8 COMMENT='机器网卡';

-- ----------------------------
-- Table structure for `idc_network`
-- ----------------------------
DROP TABLE IF EXISTS `idc_network`;
CREATE TABLE `idc_network` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `network` int(10) unsigned NOT NULL COMMENT 'network',
  `network_string` char(15) NOT NULL COMMENT 'network',
  `netmask` int(10) unsigned NOT NULL,
  `netmask_string` varchar(15) NOT NULL,
  `parent` int(10) unsigned NOT NULL,
  `description` text NOT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='网络';

-- ----------------------------
-- Table structure for `idc_network_equipment`
-- ----------------------------
DROP TABLE IF EXISTS `idc_network_equipment`;
CREATE TABLE `idc_network_equipment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(10) unsigned NOT NULL DEFAULT '2' COMMENT '设备类型（0:machine;1:cabinet,2:networkequipment）',
  `machine_id` varchar(50) NOT NULL,
  `device_tag` varchar(50) NOT NULL COMMENT '设备标签',
  `name` varchar(20) NOT NULL COMMENT '设备名称',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '状态(0:正常，1：闲置，2：损坏)',
  `description` text NOT NULL COMMENT '描述',
  `place` varchar(20) NOT NULL COMMENT '地点',
  `height` int(10) unsigned NOT NULL COMMENT '设备高度',
  `admin_dept` varchar(20) NOT NULL COMMENT '使用部门',
  `admin_username` varchar(20) NOT NULL COMMENT '负责人用户名',
  `admin_realname` varchar(20) NOT NULL,
  `manufacturer` varchar(20) NOT NULL COMMENT '制造商',
  `product_name` varchar(20) NOT NULL COMMENT '产品型号',
  `network_type_lan` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '内网',
  `network_type_tel` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '电信',
  `network_type_cnc` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '网通',
  `ip` varchar(20) NOT NULL COMMENT '管理IP',
  `cabinet_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机柜ID',
  `cabinet_name` varchar(20) NOT NULL COMMENT '机柜名称',
  `cabinet_position` int(10) unsigned NOT NULL COMMENT '所在机柜位置',
  `network_interfaces` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='网络设备表';

-- ----------------------------
-- Table structure for `idc_setting`
-- ----------------------------
DROP TABLE IF EXISTS `idc_setting`;
CREATE TABLE `idc_setting` (
  `skey` varchar(50) NOT NULL,
  `svalue` text NOT NULL,
  PRIMARY KEY (`skey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统设置';

-- ----------------------------
-- Table structure for `idc_user`
-- ----------------------------
DROP TABLE IF EXISTS `idc_user`;
CREATE TABLE `idc_user` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'UID',
  `username` char(20) NOT NULL COMMENT '用户名',
  `department` varchar(50) NOT NULL,
  `realname` varchar(20) NOT NULL,
  `password` char(100) NOT NULL COMMENT '密码',
  `mobilephone` char(20) NOT NULL COMMENT '手机',
  `email` char(50) NOT NULL COMMENT '电子邮件',
  `rtx` char(50) NOT NULL COMMENT 'RTX',
  `qq` int(20) NOT NULL COMMENT 'QQ',
  `status` tinyint(1) NOT NULL COMMENT '是否可以使用本系统',
  `issuperadmin` tinyint(1) NOT NULL COMMENT '是否是超级管理员',
  `allow_admin_user` tinyint(1) NOT NULL COMMENT '允许管理用户',
  `last_login_time` int(10) NOT NULL COMMENT '最后登录时间',
  `last_login_ip` char(20) NOT NULL COMMENT '最后登录IP',
  `logintimes` int(10) NOT NULL COMMENT '登录系统次数',
  `description` text NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统用户表';

-- ----------------------------
-- Records of idc_user
-- ----------------------------
INSERT INTO idc_user VALUES (NULL, 'admin', '管理员', 'admin', '21232f297a57a5a743894a0e4a801fc3', '', 'admin@admin.com', 'yaojungang', '0', '1', '0', '0', time(), '127.0.0.1', '8', '');
