<?php

class ErrorController extends Etao_Controller_Action
{
	public function init()
	{
		//$this->_helper->layout()->disableLayout();
	}

	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');

		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				// 404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(404);
				$this->view->message = '页面未找到';
				break;
			default:
				// application error
				$this->getResponse()->setHttpResponseCode(500);
				$this->view->message = '程序错误';
				break;
		}

		// Log exception, if logger available
        $log = $this->getLog();
		if ($log) {
			$log->crit($this->view->message);
		}

		// conditionally display exceptions
		if ($this->getInvokeArg('displayExceptions') == true) {
			$this->view->exception = $errors->exception;
		}

		$this->view->request   = $errors->request;
	}

	public function getLog()
	{
		$bootstrap = $this->getInvokeArg('bootstrap');
        $log = $bootstrap->getResource('Log');
		if (!$log) {
			return false;
		}
		return $log;
	}


}

