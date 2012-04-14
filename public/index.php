<?php

$d = dirname(__FILE__);
define('ROOT', $d == '' ? '/' : $d . '/');
if(!file_exists(ROOT . 'version.php')) {
    header('Location: install/index.php');
    exit();
}


// 把错误变成异常
set_error_handler('errorExceptionHandler');
function errorExceptionHandler($errNo, $errMsg, $errFile, $errLine)
{
    if (in_array($errNo, array(E_USER_ERROR, E_ERROR))) {
        throw new ErrorException($errMsg, 0, $errNo, $errFile, $errLine);
    }
    return false;
}

/**
 * 定义APPLICATION路径常量
 */
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
/**
 * 定义环境常量
 * apache虚拟主机里加上
 * SetEnv APPLICATION_ENV "y109_virtualbox"
 */
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV','release');
/**
 * 定义库文件包含位置
 */
set_include_path(implode(PATH_SEPARATOR, array(
realpath(APPLICATION_PATH . '/../library'),
get_include_path(),
)));
/**
 *  Zend_Application初始化
 */
require_once 'Zend/Application.php';
//echo APPLICATION_ENV;exit;
$application = new Zend_Application(
APPLICATION_ENV,
APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()->run();