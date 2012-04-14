<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Equipment
 *
 * @author y109
 */
class Idc_Model_Equipment
{

    protected $machineObj;
    protected $networkEquipmentObj;
    protected $ipObj;

    /**
     * 类型
     */
    const TYPE_MACHINE = 0;
    const TYPE_CABINET = 1;
    const TYPE_NETWORK_EQUIPMENT = 2;
    const TYPE_IDC = 3;
    const TYPE_GROUP = 4;
    const TYPE_DOMAIN_ZONE = 5;
    const TYPE_DOMAIN_RECORD = 6;
    const TYPE_USER = 7;

    public $TYPE = array(
        self::TYPE_MACHINE => '服务器',
        self::TYPE_CABINET => '机柜',
        self::TYPE_NETWORK_EQUIPMENT => '网络设备',
        self::TYPE_IDC => 'IDC',
        self::TYPE_GROUP => '分组',
        self::TYPE_DOMAIN_ZONE => 'DNS 区域',
        self::TYPE_DOMAIN_RECORD => 'DNS 记录',
        self::TYPE_USER => '用户记录',
    );

    public function __construct()
    {
        $this->machineObj = new Idc_Model_Machine;
        $this->networkEquipmentObj = new Idc_Model_NetworkEquipment();
        $this->ipObj = new Idc_Model_Ip();
        $this->networkInterfaceObj = new Idc_Model_MachineNetworkInterface();
    }

    /**
     * 根据 EquipmentId 查找设备
     */
    public function findEquipmentByEquipmentId($equipmentType, $equipmentId)
    {
        $equipment = array();
        switch($equipmentType)
        {
            case self::TYPE_MACHINE:
                $equipment = $this->machineObj->findById($equipmentId);
                break;
            case self::TYPE_NETWORK_EQUIPMENT:
                $equipment = $this->networkEquipmentObj->findById($equipmentId);
                break;
            default:
                return;
                break;
        }
        $equipment && $equipment = $equipment->toArray();
        return $equipment;
    }

    public function updateEquipmentCabinet($equipmentType, $equipmentId, $cabinetData)
    {
        $equipment = array();
        switch($equipmentType)
        {
            case self::TYPE_MACHINE:
                $equipment = $this->machineObj->update(array('cabinet_id' => $cabinetData['id']
                    , 'cabinet_name' => $cabinetData['name']), 'id = ' . $equipmentId);
                $equipment = $this->machineObj->findById($equipmentId);
                break;
            case self::TYPE_NETWORK_EQUIPMENT:
                $equipment = $this->networkEquipmentObj->update(array('cabinet_id' => $cabinetData['id']
                    , 'cabinet_name' => $cabinetData['name']), 'id = ' . $equipmentId);
                $equipment = $this->networkEquipmentObj->findById($equipmentId);
                break;
            default:
                return;
                break;
        }
        $equipment && $equipment = $equipment->toArray();
        return $equipment;
    }

    /**
     * 获取所有设备列表
     */
    public function getAll($type=null)
    {
        $equipmentList = array();
        $id = 0;
        $machineList = $this->machineObj->fetchAll()->toArray();
        foreach($machineList as &$e)
        {
            $e['equipment_id'] = $e['id'];
            $e['equipment_type'] = $e['type'];
            $e['eid'] = $e['id'];
            $e['id'] = ++$id;
        }
        $equipmentList = array_merge($equipmentList, $machineList);
        $networkEquipmentList = $this->networkEquipmentObj->fetchAll()->toArray();
        foreach($networkEquipmentList as &$e)
        {
            $e['equipment_id'] = $e['id'];
            $e['equipment_name'] = $e['name'];
            $e['equipment_type'] = $e['type'];
            $e['eid'] = $e['id'];
            $e['id'] = ++$id;
        }
        $equipmentList = array_merge($equipmentList, $networkEquipmentList);
        foreach($equipmentList as &$e)
        {
            $e = (object)$e;
        }

        return $equipmentList;
    }

    /**
     * 获取所有没有机柜的设备
     */
    public function getFreeEquipment($queryType = '', $queryWord = '')
    {
        $select1 = $this->machineObj->getSelect();
        $select1->where('cabinet_id =:cabinet_id');
        $conditions1 = array();
        $conditions1['cabinet_id'] = 0;
        if(strlen($queryType) > 0 && strlen($queryWord) > 0) {
            $select1->where($queryType . ' LIKE :' . $queryType);
            $conditions1[$queryType] = $queryWord;
        }
        $equipmentList = array();
        $machineList = $this->machineObj->queryAll($select1, $conditions1);
        foreach($machineList as &$v)
        {
            $v = (array)$v;
            $v['equipment_type'] = self::TYPE_MACHINE;
            $v['equipment_id'] = $v['id'];
            $v['equipment_name'] = $v['name'];
            $v['equipment_status'] = $v['status'];
        }
        $equipmentList = array_merge($equipmentList, $machineList);

        $select2 = $this->networkEquipmentObj->getSelect();
        $conditions2 = array();
        $select2->where('cabinet_id =:cabinet_id');
        $conditions2['cabinet_id'] = 0;
        if(strlen($query) > 0) {
            $select2->where($queryType . ' LIKE :' . $queryType);
            $conditions2[$queryType] = $queryWord;
        }
        $networkEquipmentList = $this->networkEquipmentObj->queryAll($select2, $conditions2);
        foreach($networkEquipmentList as &$v)
        {
            $v = (array)$v;
            $v['equipment_type'] = self::TYPE_NETWORK_EQUIPMENT;
            $v['equipment_id'] = $v['id'];
            $v['equipment_name'] = $v['name'];
            $v['equipment_status'] = $v['status'];
        }
        $equipmentList = array_merge($equipmentList, $networkEquipmentList);
        foreach($equipmentList as &$e)
        {
            $e = (object)$e;
        }

        return $equipmentList;
    }

    /**
     * 根据 name query 机柜的设备
     */
    public function getEquipmentByQuery($queryType, $queryWord)
    {
        $equipmentList = array();
        $machineList = $this->machineObj->queryByFieldAndValue($queryType, $queryWord, 'LIKE');
        foreach($machineList as &$v)
        {
            $v = (array)$v;
            $v['equipment_type'] = self::TYPE_MACHINE;
            $v['equipment_id'] = $v['id'];
            $v['equipment_name'] = $v['name'];
            $v['equipment_status'] = $v['status'];
        }
        $equipmentList = array_merge($equipmentList, $machineList);
        $networkEquipmentList = $this->networkEquipmentObj->queryByFieldAndValue($queryType, $queryWord, 'LIKE');
        foreach($networkEquipmentList as &$v)
        {
            $v = (array)$v;
            $v['equipment_type'] = self::TYPE_NETWORK_EQUIPMENT;
            $v['equipment_id'] = $v['id'];
            $v['equipment_name'] = $v['name'];
            $v['equipment_status'] = $v['status'];
        }
        $equipmentList = array_merge($equipmentList, $networkEquipmentList);
        foreach($equipmentList as &$e)
        {
            $e = (object)$e;
        }
        return $equipmentList;
    }

    /**
     * 根据Ip查询
     * @param type $queryIP
     */
    public function getEquipmentByQueryIp($queryIP)
    {
        $ips = $this->ipObj->getEquipmentsByIpString($queryIP);
        $machineIds = array();
        foreach($ips as $ip)
        {
            $machineIds[$ip['equipment_type']][] = $ip['equipment_id'];
        }
        $equipmentList = array();
        if(count($machineIds[self::TYPE_MACHINE]) > 0) {
            //machine
            $machineList = $this->machineObj->findMachinesByIds($machineIds[self::TYPE_MACHINE]);
            foreach($machineList as &$v)
            {
                $v = (array)$v;
                $v['equipment_type'] = self::TYPE_MACHINE;
                $v['equipment_id'] = $v['id'];
                $v['equipment_name'] = $v['name'];
                $v['equipment_status'] = $v['status'];
            }
            $equipmentList = array_merge($equipmentList, $machineList);
        }
        if(count($machineIds[self::TYPE_NETWORK_EQUIPMENT]) > 0) {
            //$networkEquipmentList
            $networkEquipmentList = $this->networkEquipmentObj->findNetworkEquipmentsByIds($machineIds[self::TYPE_NETWORK_EQUIPMENT]);
            foreach($networkEquipmentList as &$v)
            {
                $v = (array)$v;
                $v['equipment_type'] = self::TYPE_NETWORK_EQUIPMENT;
                $v['equipment_id'] = $v['id'];
                $v['equipment_name'] = $v['name'];
                $v['equipment_status'] = $v['status'];
            }
            $equipmentList = array_merge($equipmentList, $networkEquipmentList);
        }
        return $equipmentList;
    }

    /**
     * 根据 MAC 查询
     * @param type $queryIP
     */
    public function getEquipmentByQueryMac($mac)
    {
        $mac = Etao_Common_Util::formatMacAddress($mac);
        if($mac) {
            $ni = $this->networkInterfaceObj->getByMac($mac);
            $machine = '';
            $ni && $machine = $this->machineObj->findByFieldAndValue('machine_id', $ni->machine_id);
            if($machine) {
                return array($machine->toArray());
            }
        }
    }

    /**
     * 记录系统日志
     * @param type $message
     * @param type $equipmentType
     * @param type $equipmentId
     * @return type
     */
    public function addLog($message, $equipmentType, $equipmentId, $equipmentName=null)
    {
        if(self::TYPE_MACHINE == intval($equipmentType)) {
            return $this->machineObj->addLog($message, $equipmentId, $equipmentName);
        }
        if(self::TYPE_NETWORK_EQUIPMENT == intval($equipmentType)) {
            return $this->networkEquipmentObj->addLog($message, $equipmentId, $equipmentName);
        }
    }

    /**
     * 从机柜移除设备
     * @param type $cabinet
     * @param type $equipmentType
     * @param type $equipmentId
     * @param type $equipmentName
     * @return type
     */
    public function removeFromCabinet($cabinetName, $equipmentType, $equipmentId, $equipmentName)
    {
        $this->addLog('从机柜 [' . $cabinetName . '] 移除', $equipmentType, $equipmentId, $equipmentName);
        if(self::TYPE_MACHINE == intval($equipmentType)) {
            return $this->machineObj->update(array('cabinet_id' => 0, 'cabinet_name' => ''), 'id = "' . $equipmentId . '"');
        }
        if(self::TYPE_NETWORK_EQUIPMENT == intval($equipmentType)) {
            return $this->networkEquipmentObj->update(array('cabinet_id' => 0, 'cabinet_name' => ''), 'id = "' . $equipmentId . '"');
        }
    }

    /**
     * 从分组移除设备
     * @param type $groupName
     * @param type $equipmentType
     * @param type $equipmentId
     * @param type $equipmentName
     */
    public function removeFromGroup($groupName, $equipmentType, $equipmentId, $equipmentName)
    {
        $this->addLog('从分组 [' . $groupName . '] 移除', $equipmentType, $equipmentId, $equipmentName);
    }

}