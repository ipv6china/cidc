<?php
/*
 [Discuz!] (C)2001-2011 Comsenz Inc.
 This is NOT a freeware, use is subject to license terms
 y109
 Mail.php
 2011-4-21
 调用方法
 $mail = new Etao_Message_Mail();
 //$params['smtp_host'] = 'mail.comsenz.com';
 //$params['smtp_port'] = '21';
 //$params['smtp_username'] = 'yaojungang@comsenz.com';
 //$params['smtp_password'] = 'xxxxx';
 //$params['sender_name'] ='姚俊刚';
 //$params['sender_address'] ='yaojungang@comsenz.com';
 $params['mailto'] = 'y109@csnet1.cs.tsinghua.edu.cn';
 $params['subject'] = 'test标题abd';
 $params['content'] = '内容'.time();
 try {
 $mail->send($params);
 } catch (Exception $e) {
 echo $e;
 }
 */
class Etao_Message_Sender_Mail implements Etao_Message_Sender_ISender {
	protected $_params;
	public function __construct($params) {
		if (!strpos($params['sendto'], '@')){
			$config = Zend_Registry::get('config');
			if (isset($config->mail->smtp->default->domain)) {
				$params['sendto'] = $params['sendto'].'@'.$config->mail->smtp->default->domain;
			} else {
				$params['sendto'] = $params['sendto'].'@'.'comsenz.com';
			}
		}
		$this->_params = $params;
	}
	/**
	 * 发邮件
	 * $params['smtp_host']
	 * $params['smtp_port']
	 * $params['smtp_username']
	 * $params['smtp_password']
	 * $params['sender_name']
	 * $params['sender_address']
	 * $params['mailto']
	 * $params['subject']
	 * $params['content']
	 */
	public function send() {
		$logger = Zend_Registry::get('logger');
		$logger->debug('Send a mail to:'.$this->_params['sendto']);
		self::sendMail($this->_params);
	}
	/**
	 * 发送邮件
	 * $params['smtp_host']
	 * $params['smtp_port']
	 * $params['smtp_username']
	 * $params['smtp_password']
	 * $params['sender_name']
	 * $params['sender_address']
	 * $params['mailto']
	 * $params['subject']
	 * $params['content']
	 */
	private function sendMail($params) {
		$config = Zend_Registry::get('config');
		// 从配置文件中中取得邮件配置信息
		if (isset($params['smtp_host'])) {
			$smtp_host = $params['smtp_host'];
		} elseif (isset($config->mail->smtp->host)) {
			$smtp_host = $config->mail->smtp->host;
		} else {
			$smtp_host = 'localhost';
		}
		if (isset($params['smtp_port'])) {
			$port = $params['smtp_port'];
		} elseif ($config->mail->smtp->port) {
			$port = $config->mail->smtp->port;
		} else {
			$port = 25;
		}
		if (isset($params['smtp_username'])) {
			$smtp_user = $params['smtp_username'];
		} elseif (isset($config->mail->smtp->username)) {
			$smtp_user = $config->mail->smtp->username;
		}
		if (isset($params['smtp_password'])) {
			$smtp_password = $params['smtp_password'];
		} elseif (isset($config->mail->smtp->password)) {
			$smtp_password = $config->mail->smtp->password;
		}
		if (isset($params['sender_name'])) {
			$params['sender_name'] = $params['sender_name'];
		} elseif (isset($config->mail->smtp->default->sender)) {
			$params['sender_name'] = $config->mail->smtp->default->sender;
		} else {
			$params['sender_name'] = $smtp_user;
		}
		if (isset($params['sender_address'])) {
			$params['sender_address'] = $params['sender_address'];
		} else {
			$params['sender_address'] = $smtp_user;
		}

		// 写入邮件smtp配置
		$mail_config = array('auth' => 'login', 'port' => $port, 'username' => $smtp_user, 'password' => $smtp_password);
		// 初始化smtp协议
		$transport = new Zend_Mail_Transport_Smtp($smtp_host, $mail_config);
		// 初始化mail对象
		$mail = new Zend_Mail('utf-8');

		$mail->setFrom($params['sender_address'], $params['sender_name']);

		$mail->addTo($params['sendto'], $params['sendto']);
		$mail->setSubject($params['subject']);
		$mail->setBodyText($params['content']);
		$mail->send($transport);
		return true;
	}
}
