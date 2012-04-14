<?php
class Etao_Auth_Storage_UCenter implements Zend_Auth_Storage_Interface {
	private $_logger;

	public function __construct() {
		$this->_logger = Zend_Registry::get('logger');
	}

	public function isEmpty(){
		if(isset($_COOKIE['Zend_Auth'])){
			return false;
		}else{
			return true;
		}
	}
	public function read(){
		$user_cookie = $_COOKIE['Zend_Auth'];
		if(isset($user_cookie)){
			$user = unserialize(urldecode($user_cookie));
			$this->_logger->debug(__METHOD__.' '.$user['username']);
			//从UC的其它系统登录进来的用户没有RTX属性,所以要重新写下cookie
			if (!isset($user['rtx']) && intval($user['uid']) > 0) {
				$user_obj = new Model_User();
				$user = $user_obj->fetchRow('uid = '.$user['uid']);
				$this->write($user);
			}
			return $user;
		}
	}
	public function write($user){
		$this->_logger->debug(__METHOD__.' '.$user['username']);
		setcookie('Zend_Auth',urlencode(serialize($user)),time()+60*60*24*30,'/');
	}
	public function clear(){
		setcookie('Zend_Auth','',time()-3600*3600,'/');
	}

}