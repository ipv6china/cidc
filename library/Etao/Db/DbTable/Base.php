<?php
class Etao_Db_Table_Base extends Zend_Db_Table
{
	public function __construct()
	{
		$config =  Zend_Registry::get('config');
		$dbprefix = $config->resources->db->params->prefix;
		$this->_name = $dbprefix.$this->_name;
		parent::__construct();
	}
}