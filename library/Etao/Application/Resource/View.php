<?php

/**
 * @see Zend_Application_Resource_View
 */
require_once 'Zend/Application/Resource/View.php';

/**
 * 视图引擎
 *
 */
class Etao_Application_Resource_View extends Zend_Application_Resource_View
{

    /**
     * 视图配置
     *
     * @var array
     */
    protected $_options = array();

    /**
     * 视图对象
     *
     * @var Zend_View
     */
    protected $_view = null;

    /**
     * 初始化
     *
     */
    public function init()
    {
        parent::init();
        $this->_setControllerPlugin();
        $this->_setViewRenderer();
        $this->_setLayout();
        return $this->getView();
    }

    /**
     * 取得视图引擎的名称
     *
     * @return string
     */
    protected function _getEngineName()
    {
        return isset($this->_options['engine']) ? ucfirst(strtolower(trim($this->_options['engine']))) : null;
    }

    /**
     * Controller Plugin
     *
     */
    protected function _setControllerPlugin()
    {
        $engineName = $this->_getEngineName();
        if($engineName) {
            $pluginName = 'Etao_Controller_Plugin_View_' . $this->_getEngineName();
            Zend_Controller_Front::getInstance()->registerPlugin(new $pluginName());
        }
    }

    /**
     * 將 View 置入到 ViewRenderer
     *
     */
    protected function _setViewRenderer()
    {
        parent::init(); // Set View into ViewRenderer
       
        if(isset($this->_options['viewSuffix'])) {
            Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')
                    ->setViewSuffix($this->_options['viewSuffix']);
        }
    }

    /**
     * 將 View 置入到 Layout
     *
     */
    protected function _setLayout()
    {
        $bootstrap = $this->getBootstrap();
        if($bootstrap->hasPluginResource('layout')) {
            $bootstrap->bootstrap('layout');
            $bootstrap->getPluginResource('layout')->getLayout()->setView($this->getView());
        }
    }

    /**
     * 获得视图对象
     *
     * @return Zend_View
     */
    public function getView()
    {
        if(null == $this->_view) {
            $engineName = $this->_getEngineName();
            if(null === $engineName) {
                $this->_view = parent::getView();
            } else {
                $viewName = 'Etao_View_' . $this->_getEngineName();
                $options = $this->getOptions();
                $this->_view = new $viewName($options);
                if(isset($options['doctype'])) {
                    $this->_view->doctype()->setDoctype(strtoupper($options['doctype']));
                }
            }
        }
        return $this->_view;
    }

}