<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Partial
 *
 * @author y109
 */
class Etao_View_Helper_Partial extends Zend_View_Helper_Partial
{

    public function cloneView()
    {
        $view = parent::cloneView();
        $view->assign('this', $this->view);
        $view->assign($this->view->getVars()); // 把原來的樣版變數傳給 Partial 樣版
        return $view;
    }

}