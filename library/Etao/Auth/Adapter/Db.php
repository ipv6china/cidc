<?php

/*
  [Discuz!] (C)2001-2011 Comsenz Inc.
  This is NOT a freeware, use is subject to license terms
  y109
 */

class Etao_Auth_Adapter_Db implements Zend_Auth_Adapter_Interface {

    private $_logger;
    protected $_tableName;
    protected $_username;
    protected $_password;

    public function __construct($tableName, $username, $password) {
        $this->_logger = Zend_Registry::get('logger');

        $this->_tableName = $tableName;
        $this->_username = $username;
        $this->_password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter,
                        $this->_tableName,
                        'username',
                        'password',
                        'MD5(?) AND `status` = 1'
        );
        $authAdapter->setIdentity($this->_username)
                ->setCredential($this->_password);
        $result = $authAdapter->authenticate();   
        return $result;
    }

}
