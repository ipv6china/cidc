<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * User
 */
class User_Model_User extends Etao_Model_Base
{

    protected $_name = 'user';
    protected $_primary = 'uid';
    protected $uid;
    protected $username;
    protected $department;
    protected $realname;
    protected $password;
    protected $mobilephone;
    protected $email;
    protected $rtx;
    protected $qq;
    // protected $status;
    protected $issuperadmin;
    protected $allow_admin_user;
    protected $last_login_time;
    protected $last_login_ip;
    protected $logintimes;

    /**
     * 定义所有可用的用户状态
     */
    const STATUS_NOMAL = 1;
    const STATUS_LOCKED = 0;

    public $status = array(
        self::STATUS_NOMAL => '正常',
        self::STATUS_LOCKED => '锁定'
    );

    /**
     * 增加
     * @param type $data
     * @return type
     */
    public function addUser($data)
    {
        $uid = $this->add($data);
        return $uid;
    }

    /**
     * 更新
     * @param type $data
     * @return type
     */
    public function updateUser($data)
    {
        return $this->update($data, 'uid = "' . $data['uid'] . '"');
    }

    /**
     * 根据 用户名 获取用户
     * @param type $username
     * @return type
     */
    public function findUserByUsername($username)
    {
        return $this->findByFieldAndValue('username', $username);
    }

}