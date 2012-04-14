<?php
/*
 [Discuz!] (C)2001-2011 Comsenz Inc.
 This is NOT a freeware, use is subject to license terms
 y109
 ProfileLink.php
 2011-4-22
 */
/**
 * ProfileLink helper
 *
 * Call as $this->profileLink() in your layout script
 */
class Etao_View_Helper_ProfileLink {
	public $view;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

	public function profileLink()
	{

		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$username = $auth->getIdentity()->username;
			$realname = $auth->getIdentity()->realname;
			if ($realname) {
				return $realname .  ' 您好';
			} else {
				return $username .  ' 您好';
			}
		}

		return '<a href="'.$this->view->site_url.'/user/login">登录</a>';
	}
}


?>