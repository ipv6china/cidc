<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected $logger;

    /**
     * 初始化日志
     */
    protected function _initLog()
    {
        $this->bootstrap('frontController');
        $logger = new Zend_Log();
        $server = $this->getEnvironment();
        switch($server)
        {
            case 'y109_x200t':
                //$writer = new Zend_Log_Writer_Firebug();;
                $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../logs/app.log');
                $filter = new Zend_Log_Filter_Priority(Zend_Log::DEBUG);
                break;
            default:
                $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../logs/app.log');
                $filter = new Zend_Log_Filter_Priority(Zend_Log::CRIT);
                break;
        }
        $logger->addWriter($writer);
        $logger->addFilter($filter);
        $this->logger = $logger;
        Zend_Registry::set('logger', $logger);
        return $logger;
    }

    /**
     * 初始化FireBug log 的 DB配置
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
     * 初始化分页
     */
    public function _initPagination()
    {
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(
                'common/pagination_control_item.tpl'
        );
    }

}

