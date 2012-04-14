<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * log
 *
 * @author y109
 */
class Idc_Model_Log extends Etao_Model_Base
{

    protected $_name = 'log';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'priority',
        'type',
        'issystem',
        'username',
        'realname',
        'message',
        'log_time',
        'equipment_type',
        'equipment_id',
        'equipment_name'
    );

    /**
     * 等级
     */
    /**
     * 类型
     */
    const TYPE_CHANGE = 0;
    const TYPE_CONFIG = 1;
    const TYPE_NETWORK = 2;
    const TYPE_SYSTEM = 3;
    const TYPE_OTHER = 99;

    public $TYPE = array(
        self::TYPE_CHANGE => '设备变更',
        self::TYPE_CONFIG => '配置变更',
        self::TYPE_NETWORK => '网络变更',
        self::TYPE_SYSTEM => '系统生成',
        self::TYPE_OTHER => '其它',
    );

    public function getLogsByEquipmentTypeAndEquipmentId($equipmentType, $equipmentId)
    {
        $select = $this->select();
        $select->where('equipment_type = ?', $equipmentType);
        $select->where('equipment_id = ?', $equipmentId);
        $select->order('id DESC');
        return $this->fetchAll($select)->toArray();
    }

    public function addLog($data)
    {
        return $this->add($data);
    }

    public function updateLog($data)
    {
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    public static function log($log)
    {
        $obj = new self();
        $data = array(
            'message' => $log['message'],
            'priority' => $log['priority'],
            'issystem' => $log['issystem'],
            'type' => $log['type'],
            'log_time' => time());
        if(isset($log['equipment_type']) && isset($log['equipment_id'])) {
            $data['equipment_type'] = $log['equipment_type'];
            $data['equipment_id'] = $log['equipment_id'];
            $data['equipment_name'] = $log['equipment_name'];
        }
        if(isset($log['username']) && isset($log['realname'])) {
            $data['username'] = $log['username'];
            $data['realname'] = $log['realname'];
        } else {
            $auth = Zend_Auth::getInstance();
            $auth->setStorage(new App_Auth_Storage());
            $user = $auth->getIdentity();
            $data['username'] = $user['username'];
            $data['realname'] = $user['realname'];
        }
        return $obj->addLog($data);
    }

}