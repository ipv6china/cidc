<?php

/**
 * 安装
 *
 * @author Gavin <yaojungang@comsenz.com>
 */
//强制编码转换
@header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
set_magic_quotes_runtime(0);

define('IN_MB', TRUE);
define('ROOT_PATH', dirname(__FILE__) . '/../');

require ROOT_PATH . './install/var.inc.php';
require ROOT_PATH . './install/lang.inc.php';
require ROOT_PATH . './install/db.class.php';
require ROOT_PATH . './install/func.inc.php';

file_exists(ROOT_PATH . './install/extvar.inc.php') && require ROOT_PATH . './install/extvar.inc.php';

//note 是否需要输出界面
$view_off = getgpc('view_off');
define('VIEW_OFF', $view_off ? TRUE : FALSE);

//安装执行顺序
$allow_method = array('show_license', 'env_check', 'db_init', 'ext_info', 'install_check', 'tablepre_check');

$step = intval(getgpc('step', 'R')) ? intval(getgpc('step', 'R')) : 0;
$method = getgpc('method');
if (empty($method) || !in_array($method, $allow_method)) {
    $method = isset($allow_method[$step]) ? $allow_method[$step] : '';
}

if (empty($method)) {
    show_msg('method_undefined', $method, 0);
}

//@todo 临时检测是否安装在根目录
$currentUrl = $_SERVER['REQUEST_URI'];
if (0 != strcmp('/install/index.php', $currentUrl)) {
    $ret = '<h1>Notice</h1>';
    $ret .= '本程序目前只能安装在网站跟目录下，且 public 要设置成网站根目录<br />';
    $ret .='<h2>Apache 配置示例</h2>';
    $ret .= '<pre>';
    $ret .= htmlspecialchars(
	    '<VirtualHost *:80>
    ServerAdmin cidc@cidc.com
    DocumentRoot "/www/cidcomsenz.com/public"
    ServerName cidc.comsenz.com
<Directory "/www/cidc.comsenz.com/public"> 
    Options Indexes FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
</Directory>
</VirtualHost>');
    $ret .= '</pre>';
    $ret .='<h2>Nginx 配置示例</h2>';
    $ret .= '<pre>';
    $ret .= htmlspecialchars('server {
        listen       80;
        server_name  cidc.comsenz.com;
	
        location / {
            root   /www/idc.comsenz.com/public;
            index  index.php index.html index.htm;
             if (!-f $request_filename)
               {
                  rewrite (.*) /index.php;
               }
          }
 
        location ~ \.php$ {
            root           /www/idc.comsenz.com/public;
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME  /www/idc.comsenz.com/public/$fastcgi_script_name;
            include        fastcgi_params;
        }

    }');
    $ret .= '</pre>';
    $ret .= '更多帮助请参考： <a href="http://code.google.com/p/cidc/">官方文档</a>';
    $ret .= '<br />';
    echo $ret;
    exit;
}

//note 基本检测
if (file_exists($lockfile)) {//note 安装锁定检测
    show_msg('install_locked', '', 0);
} elseif (!class_exists('mysqlDb')) {//note 数据库类是否加载检测
    show_msg('database_nonexistence', '', 0);
}

if ($method == 'show_license') {//note 显示协议
    show_license();
} elseif ($method == 'env_check') {//note 服务器环境检测
    //pdo_mysql 检测
    $isPdoMysqlInstalled = check_pdo_mysql();
    if (!$isPdoMysqlInstalled) {
	show_msg('PDO_mysql', '必须安装 pdo_mysql 扩展才能继续', false, true);
    }
    //note 提前给接口单独检测函数
    VIEW_OFF && function_check($func_items);


    //note 环境检测
    env_check($env_items);

    //note 文件目录检测
    dirfile_check($dirfile_items);

    //note 显示检测结果
    show_env_result($env_items, $dirfile_items, $func_items);
} elseif ($method == 'db_init') {//note 安装数据库
    @include CONFIG;

    $submit = true;
    $error_msg = array();

    if (isset($form_db_init_items) && is_array($form_db_init_items)) {
	foreach ($form_db_init_items as $key => $items) {
	    $$key = getgpc($key, 'p');
	    if (!isset($$key) || !is_array($$key)) {
		$submit = false;
		break;
	    }
	    foreach ($items as $k => $v) {
		$tmp = $$key;
		$$k = $tmp[$k];
		if (empty($$k) || !preg_match($v['reg'], $$k)) {
		    if (empty($$k) && !$v['required']) {
			continue;
		    }
		    $submit = false;
		    VIEW_OFF or $error_msg[$key][$k] = 1;
		}
	    }
	}
    } else {
	$submit = false;
    }


    if (!VIEW_OFF && $_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($adminpw != $adminpw2) {
	    $error_msg['siteinfo']['adminpw2'] = 1;
	    $submit = false;
	}

	//是否强制安装
	$forceinstall = isset($_POST['dbinfo']['forceinstall']) ? $_POST['dbinfo']['forceinstall'] : '';
	$dbname_not_exists = true;

	if (!empty($dbhost) && empty($forceinstall)) {
	    $dbname_not_exists = check_db($dbhost, $dbuser, $dbpw, $dbname, $tablepre);
	    if (!$dbname_not_exists) {
		$form_db_init_items['dbinfo']['forceinstall'] = array('type' => 'checkbox', 'required' => 0, 'reg' => '/^.*+/');
		$error_msg['dbinfo']['forceinstall'] = 1;
		$submit = false;
		$dbname_not_exists = false;
	    }
	}
    }

    if ($submit) {//note 处理提交上来的数据
	if (get_magic_quotes_gpc()) {
	    $_COOKIE = _stripslashes($_COOKIE);
	}
	$use_uc = $_COOKIE['use_uc'];
	if ($use_uc) {
	    //uc检查
	    $adminuser = check_adminuser($adminusername, $adminpw, $adminemail);
	    if ($adminuser['uid'] < 1) {
		show_msg($adminuser['error'], '', 0);
	    } else {
		$adminuseruid = $adminuser['uid'];
	    }
	}

	$step = $step + 1;
	if (empty($dbname)) {
	    show_msg('dbname_invalid', $dbname, 0);
	} else {
	    if (!@mysql_connect($dbhost, $dbuser, $dbpw)) {
		$errno = mysql_errno();
		$error = mysql_error();
		if ($errno == 1045) {
		    show_msg('database_errno_1045', $error, 0);
		} elseif ($errno == 2003) {
		    show_msg('database_errno_2003', $error, 0);
		} else {
		    show_msg('database_connect_error', $error, 0);
		}
	    }
	    if (mysql_get_server_info() > '4.1') {
		mysql_query("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET " . DBCHARSET);
	    } else {
		mysql_query("CREATE DATABASE IF NOT EXISTS `$dbname`");
	    }

	    if (mysql_errno()) {
		show_msg('database_errno_1044', mysql_error(), 0);
	    }
	    mysql_close();
	}

	if (strpos($tablepre, '.') !== false || intval($tablepre[0])) {
	    show_msg('tablepre_invalid', $tablepre, 0);
	}

	$_config = array(
	    'dbhost' => $dbhost, 'dbuser' => $dbuser, 'dbpw' => $dbpw, 'dbname' => $dbname, 'tablepre' => $tablepre, 'dotpath' => $dotpath
	);

	//config_edit($_config);

	$db = new mysqlDb;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, 1, DBCHARSET);

	$sql = file_get_contents($sqlfile);
	$sql = str_replace("\r\n", "\n", $sql);

	if (!VIEW_OFF) {
	    show_header();
	    show_install();
	}
	$pwd = md5(random(10));


	//建数据库
	runquery($sql);
	//管理员
	$sql_admin = "INSERT INTO " . $tablepre . "user VALUES (NULL, '$username', 'IDC', '$username', '" . md5($password) . "', '', '$email', '$username', '0', '1', '0', '0', " . time() . ", '127.0.0.1', '8', '')";
	runquery($sql_admin);

	$configData = file_get_contents(ROOT_PATH . '../application/configs/application_default.ini');

	$configData .= "\n\n" . '[release : production]' . "\n" .
		'resources.db.params.host = ' . '"' . $dbhost . '"' . "\n" .
		'resources.db.params.username = ' . '"' . $dbuser . '"' . "\n" .
		'resources.db.params.password = ' . '"' . $dbpw . '"' . "\n" .
		'resources.db.params.dbname = ' . '"' . $dbname . '"' . "\n";

	file_put_contents(ROOT_PATH . '../application/configs/application.ini', $configData);


	VIEW_OFF && show_msg('initdbresult_succ');
	if (!VIEW_OFF) {
	    echo '<script type="text/javascript">document.getElementById("laststep").disabled=false;document.getElementById("laststep").value = \'' . lang('install_succeed') . '\';</script>' . "\r\n";
	    show_footer();
	}
    }
    if (VIEW_OFF) {

	show_msg('missing_parameter', '', 0);
    } else {

	show_form($form_db_init_items, $error_msg);
    }
} elseif ($method == 'ext_info') {//note 配置附加信息
    //LOCK防止重复安装
    @write_verson($lockfile);
    //清除缓存
    if (VIEW_OFF) {
	//note 接口用
	show_msg('ext_info_succ');
    } else {
	show_header();
	echo '<div class="desc">';
	echo '<a href="../">' . lang('install_complete') . '</a><br>';
	//登录完跳转
	echo '<script>setTimeout(function(){window.location=\'../\'}, 2000);</script>' . lang('redirect') . '';
	echo '</div><iframe src="../index.php" width="0" height="0" style="display: none;"></iframe>';
	show_footer();
	echo '</div>';
    }
} elseif ($method == 'install_check') {//note 检测是否安装完成
    if (file_exists($lockfile)) {
	show_msg('installstate_succ');
    } else {
	show_msg('lock_file_not_touch', $lockfile, 0);
    }
} elseif ($method == 'tablepre_check') {//note 检测表前缀
    $dbinfo = getgpc('dbinfo');
    extract($dbinfo);
    if (check_db($dbhost, $dbuser, $dbpw, $dbname, $tablepre)) {
	show_msg('tablepre_not_exists', 0);
    } else {
	show_msg('tablepre_exists', $tablepre, 0);
    }
}