<?php

class Etao_Auth_Storage_Db implements Zend_Auth_Storage_Interface {

    private $_logger;

    public function __construct() {
        $this->_logger = Zend_Registry::get('logger');
    }

    public function isEmpty() {
        if (isset($_COOKIE['Zend_Idc_Auth'])) {
            return false;
        } else {
            return true;
        }
    }

    public function read() {
        $user_cookie = $_COOKIE['Zend_Idc_Auth'];
        if ($user_cookie) {
            $user = unserialize(urldecode($user_cookie));
            $this->_logger->debug(__METHOD__ . ' ' . $user['username']);
            //从UC的其它系统登录进来的用户没有RTX属性,所以要重新写下cookie
            if (intval($user['uid']) == 0) {
                $userObj = new User_Model_User();
                $user = $userObj->fetchRow('uid = ' . $user['uid']);
                $this->write($user['username']);
            }
            return $user;
        }
    }

    public function write($username) {
        $this->_logger->debug(__METHOD__ . ' ' . $username);
        $userObj = new User_Model_User();
        $user = $userObj->fetchRow('`username` = "' . $username.'"');
        setcookie('Zend_Idc_Auth', urlencode(serialize($user->toArray())), time() + 60 * 60 * 24 * 30, '/');
    }

    public function clear() {
        setcookie('Zend_Idc_Auth', '', time() - 3600 * 3600, '/');
    }

}