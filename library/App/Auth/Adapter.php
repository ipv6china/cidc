<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Adapter
 *
 * @author y109
 */
class App_Auth_Adapter implements Zend_Auth_Adapter_Interface
{

    private $_logger;
    protected $userObj;
    protected $username;
    protected $password;
    protected $ucConfig;

    public function __construct($username, $password, $uc_config_file)
    {

        $this->_logger = Zend_Registry::get('logger');
        $this->userObj = new User_Model_User();
        $this->username = $username;
        $this->password = $password;
        $this->ucConfig = $uc_config_file;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        include_once $this->ucConfig;
        //通过接口判断登录帐号的正确性，返回值为数组
        $result = uc_user_login($this->username, $this->password);
        list($uid, $username, $password, $email, $realname, $department) = $result;
        $user_ucenter = array('uid' => $uid, 'username' => $username, 'email' => $email, 'realname' => $realname, 'department' => $department);

        $this->_logger->debug('loging UC认证成功 uid = ' . $uid);
        if($uid > 0) {
            //UC用户登陆成功
            $user = $this->userObj->fetchRow('uid = ' . $uid);
            //echo 'UC用户登陆成功'.print_r($user);
            if(!intval($user['uid']) > 0) {
                //判断用户是否存在于用户表，不存在则添加
                $newUser = array('uid' => $uid,
                    'username' => $username,
                    'email' => $email,
                    'rtx' => $username,
                    'realname' => $realname,
                    'department' => $department,
                    'last_login_time' => time(),
                    'logintimes' => 1,
                    'last_login_ip' => $_SERVER["REMOTE_ADDR"],
                    'status' => User_Model_User::STATUS_NOMAL);
                $this->userObj->insert($newUser);
                $user = $this->userObj->fetchRow('uid = ' . $uid);
                $result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user, array('message' => $user['username'] . '欢迎您!系统已自动激活您的帐号'));
            } else {
                //生成同步登录的代码
                $ucsynlogin = uc_user_synlogin($uid);
                $user_update = array('username' => $username,
                    'email' => $email,
                    'realname' => $realname,
                    'department' => $department,
                    'last_login_time' => time(),
                    'logintimes' => intval($user->logintimes) + 1,
                    'last_login_ip' => $_SERVER["REMOTE_ADDR"],
                    'rtx' => $username);
                $this->userObj->update($user_update, 'uid = "' . $uid . '"');
                $result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user, array('message' => '用户【' . $user['username'] . '】认证通过', 'ucsynlogin' => $ucsynlogin));
            }
        } elseif($uid == - 1) {
            $result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $uid, array('message' => '用户不存在,或者被删除'));
        } elseif($uid == - 2) {
            $result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $uid, array('message' => '密码错误'));
        } else {
            $result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_UNCATEGORIZED, $uid, array('message' => '未定义'));
        }
        $message = $result->getMessages();
        $getIdentity = $result->getIdentity();
        $this->_logger->info('loging result = ' . $message['message']);
        $this->_logger->debug('loging getIdentity = ' . $getIdentity['username']);
        return $result;
    }

}