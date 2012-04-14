<?php

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

class Etao_Controller_Plugin_Initializer extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * @var Zend_Controller_Request
     */
    protected $_request;

    /**
     * Constructor
     *
     * Initialize environment, root path, and configuration.
     *
     * @param  string      $env
     * @param  string|null $root
     * @return void
     */
    public function __construct()
    {
        // Get front controller instance
        $this->_front = Zend_Controller_Front::getInstance();

        // Get request object
        $this->_request = $this->_front->getRequest();
    }

    /**
     * Pre dispatch
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
    }
}