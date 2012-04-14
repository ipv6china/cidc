<?php
/*
define('UC_CONNECT', 'mysql');
define('UC_DBHOST', 'localhost');
define('UC_DBUSER', 'test');
define('UC_DBPW', 'comsenz');
define('UC_DBNAME', 'test_ucenter');
define('UC_DBCHARSET', 'utf8');
define('UC_DBTABLEPRE', '`test_ucenter`.uc_');
define('UC_DBCONNECT', '0');
define('UC_KEY', '9lcl5S7VaA3a5p0m9MfHd88y6721f6cr9cfp4t8eaY6D1X0nfAao3Edl812ZdX7L');
define('UC_API', 'http://vb.jzland.com/ucenter');
define('UC_CHARSET', 'utf-8');
define('UC_IP', '');
define('UC_APPID', '2');
define('UC_PPP', '20');
*/

/**
 * crm2的UCenter
 */

define('UC_CONNECT', 'post');
define('UC_KEY', 'sdslkxiwSFCSFDDSR2309sdfsdfS');
define('UC_API', 'http://crm2.comsenz.com/uc_server');
define('UC_CHARSET', 'utf-8');
define('UC_IP', '124.238.249.135');
define('UC_APPID', '2');
define('UC_PPP', '20');


//ucexample_2.php 用到的应用程序数据库连接参数
$dbhost = 'localhost';			// 数据库服务器
$dbuser = 'test';			// 数据库用户名
$dbpw = 'comsenz';				// 数据库密码
$dbname = 'test_uc';			// 数据库名
$pconnect = 0;				// 数据库持久连接 0=关闭, 1=打开
$tablepre = 'uc_';   		// 表名前缀, 同一数据库安装多个论坛请修改此处
$dbcharset = 'utf8';			// MySQL 字符集, 可选 'gbk', 'big5', 'utf8', 'latin1', 留空为按照论坛字符集设定

//同步登录 Cookie 设置
$cookiedomain = ''; 			// cookie 作用域
$cookiepath = '/ZEND_IDC_UC';			// cookie 作用路径

