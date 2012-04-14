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

class Etao_Message_Sender_RTX implements Etao_Message_Sender_ISender {
	protected $_params;
	public function __construct($parmas) {
		$this->_params = $parmas;
	}
	/**
	 * 发RTX消息
	 * $params['sendto']
	 * $params['content']
	 */
	public function send() {
		$logger = Zend_Registry::get('logger');
		$logger->debug('Send a RTX message to:'.$this->_params['sendto']);
		self::sendRTXMessage($this->_params);
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
	private function sendRTXMessage($params) {
		$config = Zend_Registry::get('config');
		// 从配置文件中中取得邮件配置信息
		if (isset($params['sendmessage_url'])) {
			$sendmessage_url = $params['sendmessage_url'];
		} elseif (isset($config->rtx->sendmessage_url)) {
			$sendmessage_url = $config->rtx->sendmessage_url;
		} else {
			$sendmessage_url = 'http://rtx.comsenz.com:8012/sendnotify.cgi';
		}
		$user = $params['sendto'];
        //去掉空格
       // $params['content'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/"," ",$params['content']);
       $params['content'] = str_ireplace('&nbsp;', ' ', $params['content']);
		$msg=iconv("UTF-8","GBK",  strip_tags($params['content']));
		$msg=rawurlencode($msg);
		$title=iconv("UTF-8","GBK",  strip_tags($params['subject']));
		$title=rawurlencode($title);
		$url = $sendmessage_url.'?msg='.$msg.'&receiver='.$user.'&title='.$title;
		file_get_contents($url);
		//$cmd = "curl \"".$url."\"";
		//Etao_Console_Exec::runCommand($cmd);
	}
}
?>