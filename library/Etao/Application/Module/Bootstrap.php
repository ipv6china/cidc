<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Bootstrap
 *
 * @author y109
 */
class Etao_Application_Module_Bootstrap extends Zend_Application_Module_Bootstrap
{

    protected $logger;

    public function __construct($application)
    {
        parent::__construct($application);
        $this->logger = Zend_Registry::get('logger');
    }

    protected function _initModule()
    {
        $this->logger->debug('init ' . $this->getAppNamespace() . 'Module');
        /*
        $autoloader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => $this->getAppNamespace(),
                    'basePath' => dirname(__FILE__),
                ));
        return $autoloader;
         */
    }

}