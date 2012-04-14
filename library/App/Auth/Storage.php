<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Storage
 *
 * @author y109
 */
include_once 'uc/uc_client/client.php';
class App_Auth_Storage implements Zend_Auth_Storage_Interface
{

    private $_logger;

    public function __construct()
    {
        $this->_logger = Zend_Registry::get('logger');
    }

    public function isEmpty()
    {
        if(isset($_COOKIE['Zend_Idc_Auth'])) {
            return false;
        } else {
            return true;
        }
    }

    public function read()
    {
        $user_cookie = $_COOKIE['Zend_Idc_Auth'];
        if(isset($user_cookie)) {
            $user = unserialize(urldecode($user_cookie));
            $this->_logger->debug(__METHOD__ . ' ' . $user['username']);
            //从UC的其它系统登录进来的用户没有RTX属性,所以要重新写下cookie
            if(!isset($user['rtx']) && intval($user['uid']) > 0) {
                $userObj = new User_Model_User();
                $user = $useObj->fetchRow('uid = ' . $user['uid']);
                $this->write($user);
            }
            return $user;
        }
    }

    public function write($user)
    {
        $this->_logger->debug(__METHOD__ . ' ' . $user['username']);
        setcookie('Zend_Idc_Auth', urlencode(serialize($user->toArray())), time() + 60 * 60 * 24 * 30, '/');
    }

    public function clear()
    {
        setcookie('Zend_Idc_Auth', '', time() - 3600 * 3600, '/');
    }

}