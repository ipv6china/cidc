<?php
/*
	[Discuz!] (C)2001-2011 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms
	y109
	UCenter.php
	2011-4-17
*/
include_once 'uc/config.inc.php';
include_once 'uc/uc_client/client.php';
class Etao_Auth_Adapter_UCenter implements Zend_Auth_Adapter_Interface {
	private $_logger;
	protected $_username;
	protected $_password;
	protected $_uc_config_file;

	public function __construct($username , $password, $uc_config_file) {

		$this->_logger = Zend_Registry::get('logger');

		$this->_username = $username;
		$this->_password = $password;
		$this->_uc_config_file = $uc_config_file;
	}
	/**
	 * Performs an authentication attempt
	 *
	 * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
	 * @return Zend_Auth_Result
	 */
	public function authenticate() {
		include_once $this->_uc_config_file;
		//include_once 'uc/config.inc.php';

		//通过接口判断登录帐号的正确性，返回值为数组
		list($uid, $username, $password, $email) = uc_user_login($this->_username, $this->_password);
		$user_ucenter = array('uid' => $uid,'username'=>$username,'email'=>$email);

		$this->_logger->debug('loging UC认证成功 uid = '.$uid);
		//return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user,array('message' => 'UC认证成功,自动激活'));
		if ($uid > 0) {
			//UC用户登陆成功
			$user_obj = new Model_User();
			$user = $user_obj->fetchRow('uid = '.$uid);
			//echo 'UC用户登陆成功'.print_r($user);
			if (!intval($user['uid']) >0 ) {
				//判断用户是否存在于用户表，不存在激活
				$user_insert = array('uid'=>$uid,
									'username' => $username,
									'email' => $email,
									'rtx' => $username,
									'status' => Model_User::STATUS_NOMAL);
				$user_obj->insert($user_insert);
				$user = $user_obj->fetchRow('uid = '.$uid);
				//$auth = rawurlencode(uc_authcode("$username\t" . time(), 'ENCODE'));
				//echo '您需要需要激活该帐号，才能进入本应用程序<br><a href="' .	$_SERVER['PHP_SELF'] .'?example=register&action=activation&auth=' . $auth .'">继续</a>';
				//exit();
				$result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user,array('message' => $user['username'].'欢迎您!系统已自动激活您的帐号'));
			} else {
				//生成同步登录的代码
				$ucsynlogin = uc_user_synlogin($uid);
				$user_update = array('username' => $username,
									'email' => $email,
									'rtx' => $username);
				$user_obj->update($user_update, 'uid = "'.$uid.'"');
				$result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user,array('message' => '用户【'.$user['username'].'】认证通过','ucsynlogin' => $ucsynlogin));
			}
		} elseif ($uid == - 1) {
			$result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $uid,array('message' => '用户不存在,或者被删除'));
		} elseif ($uid == - 2) {
			$result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $uid,array('message' => '密码错误'));
		} else {
			$result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_UNCATEGORIZED, $uid,array('message' => '未定义'));
		}
		$message = $result->getMessages();
		$getIdentity = $result->getIdentity();
		$this->_logger->info('loging result = '.$message['message']);
		$this->_logger->debug('loging getIdentity = '.$getIdentity['username']);
		return $result;

	}
}

?>