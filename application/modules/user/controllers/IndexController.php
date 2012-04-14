<?php

class User_IndexController extends App_CommonController
{

    public function init()
    {
        parent::init();
        $this->view->assign('title', '用户管理');
    }

    public function indexAction()
    {
        //$this->showMessage('您的帐号被锁定，请联系管理员', '/user/user/login/', 3);
        $url = $this->getUrl('index', 'user', 'user', array());
        $this->_redirect($url);
    }


    public function messageAction()
    {
        $this->view->assign('title', '系统消息');
        $this->view->message = $this->_getParam('message');
    }

}

