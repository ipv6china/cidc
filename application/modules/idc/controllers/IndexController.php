<?php

class IndexController extends App_CommonController
{
     public function init()
    {
        parent::init();
        $this->view->assign('title', 'IDC 管理');
    }
    public function indexAction()
    {
    }

}

