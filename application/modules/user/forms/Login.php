<?php

class User_Form_Login extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add an email element
        $this->addElement('text', 'username', array(
            'label'      => '用户名:',
            'required'   => true,
            'filters'    => array('StringTrim')
        ));

        // Add the comment element
        $this->addElement('password', 'password', array(
            'label'      => '密码:',
            'required'   => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));
/*
        // Add a captcha
        $this->addElement('captcha', 'captcha', array(
            'label'      => '验证码:',
            'required'   => true,
            'captcha'    => array(
                'captcha' => 'Figlet',
                'wordLen' => 5,
                'timeout' => 300
            )
        ));
        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
*/
        // Add the submit button
        $this->addElement('submit', 'loginsubmit', array(
            'ignore'   => true,
            'label'    => '登录',
        ));


    }
}
