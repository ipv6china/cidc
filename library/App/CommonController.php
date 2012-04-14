<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Commoncontroller
 *
 * @author y109
 */
class App_CommonController extends Etao_Controller_Action
{

    protected $currentUser;
    protected $noLogin = false;

    /**
     * 初始化当前登录用户
     */
    public function init()
    {
        parent::init();
        //白名单Ip内的机器不用登录验证
        $ip = $_SERVER["REMOTE_ADDR"];
        $whiteIps = array('127.10.0.1','10.0.4.71');
        if(in_array($ip, $whiteIps)) {
            $this->noLogin = true;
        }

        if(!$this->noLogin) {
            $auth = Zend_Auth::getInstance();
            $auth->setStorage(new App_Auth_Storage());
            if($auth->hasIdentity()) {
                $this->view->currentUser = $this->currentUser = (object)$auth->getIdentity();
            } else {
                $this->gotoUrl('/user/login/login');
            }
        }
    }

    /**
     * 检查权限，不跳转
     */
    public function checkAllow($hasPrivilege='')
    {
        //超级管理员直接放行
        if($this->currentUser['issuperadmin']) {
            return TRUE;
        }
        //被锁定用户直接拒绝
        if(User_Model_User::STATUS_LOCKED == intval($this->currentUser['status'])) {
            return FALSE;
        }
        //检查是否有相应权限
        if(strlen($hasPrivilege) > 0) {
            return $this->currentUser[$hasPrivilege];
        }
    }

    /**
     * 检查权限，无权限则跳转
     */
    public function isAllow($hasPrivilege='')
    {
        setcookie('last_url', urlencode($this->_request->getRequestUri()), time() + 300);
        if(!$this->currentUser) {
            $this->addMessage('请登录' . $this->_last_uri);
            $this->_helper->redirector('login', 'user');
        }
        if(User_Model_User::STATUS_LOCKED == intval($this->currentUser['status'])) {
            $this->gotoUrl('/user/index/message/message/' . '您的帐号被锁定，请联系管理员/url/' . urlencode('/user/user/login/') . '/timeout/10');
            return;
        }

        if(!checkAllow($hasPrivilege) > 0) {
            $this->addMessage('权限不足,请重新登录');
            $this->gotoUrl('/user/user/login');
        }
    }

}