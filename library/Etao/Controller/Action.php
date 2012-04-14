<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Action
 *
 * @author y109
 */
class Etao_Controller_Action extends Zend_Controller_Action
{

    //系统 log
    protected $logger;
    //当前用户
    protected $currentUser;
    protected $_redirector = null;

    public function init()
    {
        parent::init();
        //设置系统默认 title
        //$this->view->assign('title', 'System');
        $this->view->title = 'Welcome';
        //实例化 log
        $this->logger = Zend_Registry::get('logger');
        //向前台传递系统消息
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        //向前台传递模块名
        $this->view->moduleName = $this->_request->getModuleName();
        //向前台传递控制器名
        $this->view->controllerName = $this->_request->getControllerName();
        //向前台传递动作名
        $actionName = $this->view->actionName = $this->_request->getActionName();
        //如果动作名以json开始则禁用布局和视图
        if('json' == substr($actionName, 0, 4)) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
        }

        $this->_redirector = $this->_helper->getHelper('Redirector');
    }

    /**
     * 添加提示消息
     * @param type $message
     */
    public function addMessage($message)
    {
        $this->_helper->flashMessenger->addMessage($message);
    }

    public function showMessage($message='', $url='-1', $timeout=3)
    {
        $timeout > 1000 && $timeout = intval($timeout / 1000);
        $gourl = $url;
        if($url == -1) {
            $gourl = 'javascript:history.go(-1)';
        }

        if(!$timeout) { #立即跳转
            if(!preg_match('/^javascript/', $gourl)) {
                header('Location: ' . $gourl);
            }
            echo $meta = "<meta http-equiv=\"refresh\" content=\"{$time};url={$gourl}\" />";
        } else {
            $this->view->message = $message;
            $this->view->gourl = $gourl;
            $this->view->timeout = $timeout;
            $this->renderScript('/index/message.tpl');
        }
    }

    /**
     * 获取页面参数
     * @param String $paramName
     * @param string $default
     * @return string
     */
    public function getParam($paramName, $default=null)
    {
        return trim($this->_getParam($paramName, $default));
    }

    /**
     * 获取所有参数
     * @return array
     */
    public function getParams()
    {
        return $this->_request->getParams();
    }

    /**
     * 生成Url
     * @param type $action
     * @param type $controller
     * @param type $module
     * @param array $params
     * @return type
     */
    public function getUrl($action, $controller = null, $module = null, array $params = null)
    {
        return $this->_helper->_url->simple($action, $controller, $module, $params);
    }

    /**
     * 跳转到某Url,Url要写完整 /module/controller/action/key1/value1
     * @param string $url
     */
    public function gotoUrl($url)
    {
        $url = substr($url, 1);
        $urls = explode('/', $url);
        $_params = array_slice($urls, 3);
        $params = array();
        for($i = 0; $i < count($_params); $i++)
        {
            if($i % 2 == 0) {
                $tempKey = $_params[$i];
            } else {
                $params[$tempKey] = $_params[$i];
                unset($tempKey);
            }
        }
        $url = $this->getUrl($urls[2], $urls[1], $urls[0], $params);
        $this->_redirect($url);
        $this->_redirector->redirectAndExit();
    }

}