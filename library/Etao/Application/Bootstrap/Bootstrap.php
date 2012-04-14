<?php

/*
  [Discuz!] (C)2001-2011 Comsenz Inc.
  This is NOT a freeware, use is subject to license terms
  y109
  Bootstrap.php
  2011-4-21
 */

class Etao_Application_Bootstrap_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * log
     * @var Zend_Log
     */
    protected $logger;

    /**
     * 初始化日志
     */
    protected function _initLogging()
    {
        $this->bootstrap('frontController');
        $logger = new Zend_Log();
        $writer = 'production' == $this->getEnvironment() ?
                new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../data/logs/app.log') :
                new Zend_Log_Writer_Firebug();
        $logger->addWriter($writer);

        if('production' == $this->getEnvironment()) {
            $filter = new Zend_Log_Filter_Priority(Zend_Log::CRIT);
            $logger->addFilter($filter);
        }

        $this->logger = $logger;
        Zend_Registry::set('logger', $logger);
    }

    /**
     * 初始化DB配置
     */
    protected function _initDbProfiler()
    {
        $this->logger->debug('Bootstrap ' . __METHOD__);
        if('production' !== $this->getEnvironment()) {
            $this->bootstrap('db');
            $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
            $profiler->setEnabled(true);
            $this->getPluginResource('db')->getDbAdapter()->setProfiler($profiler);
        }
    }

    /**
     * 初始化autoloader
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload()
    {
        $this->logger->debug('Bootstrap ' . __METHOD__);
        // Ensure front controller instance is present
        $this->bootstrap('frontController');
        // Get frontController resource
        $this->_front = $this->getResource('frontController');
        // Add autoloader empty namespace
        $autoLoader = new Zend_Loader_Autoloader_Resource(array(
                    'basePath' => APPLICATION_PATH,
                    'namespace' => '',
                    'resourceTypes' => array(
                        'model' => array(
                            'path' => 'models/',
                            'namespace' => 'Model_')
                    ), 'document' => array(
                        'path' => 'models/document',
                        'namespace' => 'Model_Document'
                    )
                ));

        return $autoLoader;
    }

    /**
     * 初始化配置
     */
    protected function _initConfig()
    {
        $this->logger->debug('Bootstrap ' . __METHOD__);
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $registry = Zend_Registry::getInstance();
        $registry->set('config', $config);
        return $config;
    }

    /**
     * 初始化DB
     */
    protected function _initDb()
    {
        $this->logger->debug('Bootstrap ' . __METHOD__);
        $this->bootstrap('config');
        // Get config resource
        $config = $this->getResource('config');
        $db = $config->resources->db;
        $db = Zend_Db::factory($db->adapter, $db->params->toArray());
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $db->query("SET NAMES 'utf8'");
        $db->query("SET CHARACTER SET 'utf8'");
        Zend_Db_Table::setDefaultAdapter($db);
        // Return it, so that it can be stored by the bootstrap
        return $db;
    }

    /**
     * 初始化
     */
    protected function _initView()
    {
        $this->logger->debug('Bootstrap ' . __METHOD__);
        $view = new Zend_View();

        // add global helpers
        $view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Zend_View_Helper');

        // set encoding and doctype
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');

        // set the content type and language
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        $view->headMeta()->appendHttpEquiv('Content-Language', 'zh-CN');

        // Add the view to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

        // Load digitalus helpers
        // base helpers
        $view->addHelperPath('Etao/View/Helper', 'Etao_View_Helper');

        $helperDirs = Etao_File_Dir::getDirectories(APPLICATION_PATH . '/../library/Etao/View/Helper');
        if(is_array($helperDirs)) {
            foreach($helperDirs as $dir)
            {
                $view->addHelperPath(APPLICATION_PATH . '/../library/Etao/View/Helper/' . $dir, 'Etao_View_Helper_' . ucfirst($dir));
            }
        }
        $view->baseUrl = $this->_front->getBaseUrl();


        //$request->getParm("controller")
//$front = Zend_Controller_Front::getInstance();
//$controllerName = $this->_front->getRequest()->getControllerName();
//Zend_Registry::set('controllerName', $controllerName);
        // Return it, so that it can be stored by the bootstrap
        return $view;
    }

    /**
     * 初始化日志
     */
    protected function _bootstrap($resource = null)
    {
        $errorHandling = $this->getOption('errorhandling');
        try
        {
            parent::_bootstrap($resource);
        } catch(Exception $e)
        {
            if(true == (bool)$errorHandling['graceful']) {
                $this->__handleErrors($e, $errorHandling['email']);
            } else {
                echo nl2br($e);
                throw $e;
            }
        }
    }

    /**
     * Add graceful error handling to the dispatch, this will handle
     * errors during Front Controller dispatch.
     */
    public function run()
    {
        $errorHandling = $this->getOption('errorhandling');
        try
        {
            parent::run();
        } catch(Exception $e)
        {
            echo $e;
            if(true == (bool)$errorHandling['graceful']) {
                $this->__handleErrors($e, $errorHandling['email']);
            } else {
                echo nl2br($e);
                throw $e;
            }
        }
    }

    /**
     * Handle errors gracefully, this will work as long as the views,
     * and the Zend classes are available
     *
     * @param Exception $e
     * @param string $email
     */
    protected function __handleErrors(Exception $e, $email)
    {
        header('HTTP/1.1 500 Internal Server Error');
        $view = new Zend_View();
        $view->addScriptPath(dirname(__FILE__) . '/../views/scripts');
        //@TODO 错误处理
        echo '程序发生致命错误<br>';
        //echo $view->render('fatalError.phtml');
        var_dump($e);

        if('' != $email) {
            $mail = new Zend_Mail();
            $mail->setSubject('Fatal error in application Storefront');
            $mail->addTo($email);
            $mail->setBodyText(
                    $e->getFile() . "\n" .
                    $e->getMessage() . "\n" .
                    $e->getTraceAsString() . "\n"
            );
            @$mail->send();
        }
    }

    /**
     * 设置工作目录
     */
    public function _initWorkDir()
    {
        $this->bootstrap('config');
        $config = $this->getResource('config');
        $work_dir = $config->crs->work_dir;
        if(!defined("CRS_WORK_DIR")) {
            define("CRS_WORK_DIR", $config->crs->work_dir);
        }
        if(!is_dir(CRS_WORK_DIR)){
            mkdir(CRS_WORK_DIR);
        }
    }

    /**
     * 初始化分页
     */
    public function _initPagination()
    {
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(
                'common/pagination_control_item.phtml'
        );
        //$paginator->setView ($view);
    }

    /**
     * 初始化 ZFDebug
     */
    protected function _initZFDebug()
    {
        $this->bootstrap('config');
        $config = $this->getResource('config');
        $zfdebug_open = $config->zfdebug->open;
        // Only enable zfdebug if options have been specified for it
        if(!$zfdebug_open) {
            return;
        }
        // Setup autoloader with namespace
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('ZFDebug');

        // Ensure the front controller is initialized
        $this->bootstrap('FrontController');

        // Retrieve the front controller from the bootstrap registry
        $front = $this->getResource('FrontController');


        // Create ZFDebug instance
        $zfdebug = new ZFDebug_Controller_Plugin_Debug($this->getOption('zfdebug'));

        // Register ZFDebug with the front controller
        // $front->registerPlugin($zfdebug);



        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $zfdebug->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Database());

        // Alternative configuration without application.ini
        $options = array(
            'plugins' => array(
                'ZFDebug_Controller_Plugin_Debug_Plugin_Variables',
                'ZFDebug_Controller_Plugin_Debug_Plugin_Database',
                'ZFDebug_Controller_Plugin_Debug_Plugin_Html',
                'ZFDebug_Controller_Plugin_Debug_Plugin_File',
                'ZFDebug_Controller_Plugin_Debug_Plugin_Registry',
                //'ZFDebug_Controller_Plugin_Debug_Plugin_Exception',
                'ZFDebug_Controller_Plugin_Debug_Plugin_Text',
                'ZFDebug_Controller_Plugin_Debug_Plugin_Memory',
                'ZFDebug_Controller_Plugin_Debug_Plugin_Time',
            )
        );
        $zfdebug = new ZFDebug_Controller_Plugin_Debug($options);
        // Register ZFDebug with the front controller
        $front->registerPlugin($zfdebug);
    }

}

?>