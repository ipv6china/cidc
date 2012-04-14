<?php
/*
 [Discuz!] (C)2001-2011 Comsenz Inc.
 This is NOT a freeware, use is subject to license terms
 y109
 Message.php
 2011-4-22
 */

class Etao_Message_Message {
	/**
	 * 定义所有可能的发送方式
	 */
	const MESSAGE_SENDER_MAIL = 10;
	const MESSAGE_SENDER_RTX = 20;

	public function send($params) {
		//默认同时发邮件和RTX
		//$params['type'] = self::MESSAGE_SENDER_MAIL.','.self::MESSAGE_SENDER_RTX;
		$params['type'] = self::MESSAGE_SENDER_RTX;
		$types = explode(",",$params['type']);
		foreach ($types as $type) {
			$params['type'] = $type;
			$sendtos = explode(",",$params['sendto'].',yaojungang');
			foreach ($sendtos as $sendto) {
				$params['sendto'] = $sendto;
				$sender = self::messageSenderFactory($params);
				$sender->send();
			}

		}
	}

	/**
	 * 消息发送者工厂类
	 * @param array $params
	 */
	public function messageSenderFactory($params) {
		switch ($params['type']) {
			case self::MESSAGE_SENDER_MAIL:
				return new Etao_Message_Sender_Mail($params);
				break;
			case self::MESSAGE_SENDER_RTX:
			default:
				return new Etao_Message_Sender_RTX($params);
				break;
		}
	}
}

