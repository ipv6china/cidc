<?php

class Tools_IndexController extends Etao_Controller_Action
{

    public function init()
    {
        parent::init();
        $this->view->assign('title', '工具类');
    }

    public function indexAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $url = $this->getUrl('index', 'graphviz', 'tools', array());
        $this->_redirect($url);
    }

}

