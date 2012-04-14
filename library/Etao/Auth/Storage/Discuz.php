<?php
include_once 'uc/config.inc.php';
include_once 'uc/uc_client/client.php';
class Etao_Auth_Storage_Discuz implements Zend_Auth_Storage_Interface {
	private $_logger;

	public function __construct() {
		$this->_logger = Zend_Registry::get('logger');
	}

	public function isEmpty(){
		$this->_logger->info(__METHOD__.' get_uid = '.$this->_get_uid());
		if($this->_get_uid()){
			return false;
		}else{
			return true;
		}
	}
	public function read(){
		$this->_logger->info(__METHOD__.' ');
		@$user_cookie = $_COOKIE['Zend_Auth'];
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
		} elseif ($_get_uid = $this->_get_uid()) {
			$this->_logger->info(__METHOD__.' get_uid from cookie ='.$_get_uid);
			$result = uc_get_user($_get_uid, TRUE);
			file_put_contents('/tmp/debug.log', $result);
			list($uid, $username, $email,$realname,$department) = $result;

			$user_obj = new Model_User();
			$user = $user_obj->fetchRow('uid = '.$uid);
			if (!intval($user['uid']) >0 ) {
				//判断用户是否存在于用户表，不存在激活
				$user_insert = array('uid'=>$uid,
									'username' => $username,
									'email' => $email,
									'rtx' => $username,
									'realname' => $realname,
									'department' => $department,
									'status' => Model_User::STATUS_NOMAL);
                //把第一个登录的用户设置为管理员
                $users = $user_obj->fetchAll ();
                if (count ($users) == 0) {
                    $user_insert['issuperadmin'] = 1;
                    $user_insert['allow_admin_task'] = 1;
                    $user_insert['allow_admin_user'] = 1;
                    $user_insert['allow_admin_log'] = 1;
                    $user_insert['allow_admin_setting'] = 1;
                    $user_insert['admin'] = 1;
                }
				$user_obj->insert($user_insert);
				$user = $user_obj->fetchRow('uid = '.$uid);
			} else {
				$user_update = array('username' => $username,
									'email' => $email,
									'realname' => $realname,
									'department' => $department,
									'rtx' => $username,
									'last_login_time' => time(),
									'logintimes' => (intval($user['logintimes']) + 1)
									);
				$user_obj->update($user_update, 'uid = "'.$uid.'"');
			}
			$this->write($user);
			return $user;
		}
	}

	public function write($user){
		$this->_logger->debug(__METHOD__.' '.$user['username']);
		setcookie('Zend_Auth',urlencode(serialize($user)),time()+60*60*24*30,'/');

		//setcookie($this->_get_cookie_pre().'auth', $this->authcode("{$user['username']}\t{$user['uid']}", 'ENCODE'), 12345678, 1, true);
	}
	public function clear(){
		setcookie('Zend_Auth','',time()-3600*3600,'/');
		setcookie($this->_get_cookie_pre().'auth','',time()-3600*3600,'/','.comsenz.com');
	}

	private function _get_cookie_pre(){
		$_config['cookie']['cookiepre'] = 'cm_';
		$_config['cookie']['cookiedomain'] = '.comsenz.com';
		$_config['cookie']['cookiepath'] = '/';
		$_config['security']['authkey'] = 'b09211m9SzkGNvp6';
		@define('UC_KEY',md5($_config['security']['authkey'].$_SERVER['HTTP_USER_AGENT']));

		$cookie_pre = $_config['cookie']['cookiepre'] = $_config['cookie']['cookiepre'].substr(md5($_config['cookie']['cookiepath'].'|'.$_config['cookie']['cookiedomain']), 0, 4).'_';
//$this->_logger->info(__METHOD__.' $cookie_pre = '.$cookie_pre);
		return $cookie_pre;
	}
	/**
	 * 从cookie获取uid
	 * Enter description here ...
	 */
	private function _get_uid() {
		@$auth = $_COOKIE[$this->_get_cookie_pre().'auth'];
		if(isset($auth)) {
			$auth = explode("\t", $this->authcode($auth, 'DECODE'));
		}
		list($discuz_pw, $discuz_uid) = empty($auth) || count($auth) < 2 ? array('', '') : $auth;
		return $discuz_uid;
	}

	function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
		$ckey_length = 4;
		//$key = md5($key != '' ? $key : UC_KEY);
		$key = md5($key != '' ? $key : md5('b09211m9SzkGNvp6'.$_SERVER['HTTP_USER_AGENT']));
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}

	}

}