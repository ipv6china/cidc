<?php

require_once 'Zend/Controller/Action.php';

class Etao_Controller_Base extends Zend_Controller_Action
{

    protected $_logger;
    protected $_flashMessenger;
    protected $_redirector;
    protected $_current_user;
    protected $_site_url;

    /**
     * Action 的构造方法的最后一步调用
     */
    public function init ()
    {
        parent::init ();

        //初始化当前登录用户
        $auth = Zend_Auth::getInstance ();
        $auth->setStorage (new Etao_Auth_Storage_UCenter ());
        $current_user = null;
        if ($auth->hasIdentity ()) {
            $current_user = Zend_Auth::getInstance ()->getIdentity ();
            $this->_current_user = $current_user;
        }
        $this->view->current_user = $current_user;

        $this->_redirector = $this->_helper->getHelper ('Redirector');
        $this->_flashMessenger = $this->_helper->getHelper ('FlashMessenger');
        $this->_logger = Zend_Registry::get ('logger');
        $this->_site_url = $this->getUrl ();
        Zend_Registry::set ('site_url', $this->getUrl ());

        $this->view->site_url = $this->getUrl ();
        $this->view->request = $this->_request;
        $this->view->controllerName = $this->_request->getControllerName ();
        $this->view->actionName = $this->_request->getActionName ();
        $this->view->messages = $this->_helper->flashMessenger->getMessages ();
    }

    /**
     * Action的每个方法调用前调用
     */
    public function preDispatch ()
    {
        parent::preDispatch ();
    }

    /**
     * Action的每个方法调用后调用
     */
    public function postDispatch ()
    {
        parent::postDispatch ();
    }

    private function getUrl ($url='')
    {
        $host = (isset ($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        $proto = (isset ($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") ? 'https' : 'http';
        $port = (isset ($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80);
        $uri = $proto . '://' . $host;
        if ((('http' == $proto) && (80 != $port)) || (('https' == $proto) && (443 != $port))) {
            if (strrchr ($host, ':') === false) {
                $uri .= ':' . $port;
            }
        }
        $url = $uri . ltrim ($url, '/');
        return $url;
    }

    public function checkAllow ($allow_admin_object)
    {
        if (Model_User::STATUS_LOCKED == intval ($this->_current_user['status'])) {
            return FALSE;
        }
        if ($this->_current_user['issuperadmin']) {
            return TRUE;
        }
        return $this->_current_user[$allow_admin_object];
    }

    public function isAllow ($allow_admin_object='')
    {
        setcookie ('last_url', urlencode ($this->_request->getRequestUri ()), time () + 300);
        if (!$this->_current_user) {
            $this->_helper->flashMessenger->addMessage ('请登录' . $this->_last_uri);
            $this->_helper->redirector ('login', 'user');
        }
        if (Model_User::STATUS_LOCKED == intval ($this->_current_user['status'])) {
            $this->_redirect ('/index/message/message/' . '您的帐号被锁定，请联系管理员');
            return;
        }
        if ($this->_current_user['issuperadmin']) {
            return TRUE;
        }
        if (strlen ($allow_admin_object) > 0) {
            if (!$this->_current_user[$allow_admin_object]) {
                $this->_helper->flashMessenger->addMessage ('权限不足,请重新登录');
                $this->_helper->redirector ('login', 'user');
            }
        }
    }

}
