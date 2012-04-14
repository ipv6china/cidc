<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * LoginController
 *
 * @author y109
 */
class User_LoginController extends Etao_Controller_Action
{

     public function loginAction()
    {
        if($this->currentUser) {
            $url = $this->getUrl('index', 'index', 'idc', array());
            $this->_redirect($url);
        }
        $this->view->title = '登录';
        $form = new User_Form_Login();
        if($this->getRequest()->isPost()) {
            if($form->isValid($this->_request->getPost())) {
                $username = $this->_getParam('username');
                $password = $this->_getParam('password');
                $auth = Zend_Auth::getInstance();
                $auth->setStorage(new Etao_Auth_Storage_Db());
                $adapter = new Etao_Auth_Adapter_Db('idc_user',$username, $password);
                $result = $auth->authenticate($adapter);
                $message = $result->getMessages();
                $this->addMessage($message[0]);
                if($result->isValid()) {
                    $user = $auth->getIdentity();
                    if(isset($user['status']) && (int)$user['status'] == User_Model_User::STATUS_LOCKED) {
                        $this->addMessage('您的帐号被锁定,请联系管理员解锁');
                        $this->gotoUrl('/user/login/login');
                        exit;
                    }
                    if($_COOKIE['last_url']) {
                        $last_url = urldecode($_COOKIE['last_url']);
                        setcookie('last_url', '', time() - 3600 * 3600);
                        $this->gotoUrl($last_url);
                    } else {
                        $this->gotoUrl('/idc/index/index');
                        exit;
                    }
                } else {
                    $this->gotoUrl('/user/login/login');
                    exit;
                }
            }
        }

        $this->view->form = $form;
    }
    

    public function logoutAction()
    {
        Zend_Auth::getInstance()->setStorage(new App_Auth_Storage())->clearIdentity();
        $this->_helper->flashMessenger->addMessage('您已成功退出，谢谢使用');
        $this->gotoUrl('/user/login/login');
    }

}