<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Ip
 *
 * @author y109
 */
class Idc_Model_Ip extends Etao_Model_Base
{

    protected $_name = 'ip';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'machine_id',
        'interface',
        'server',
        'ip',
        'ip_string',
        'netmask',
        'netmask_string'
    );

    public function addIp($ip, $data)
    {
        $ipData = array();
        $ips = explode('/', $ip);
        $ipData['name'] = $data['name'];
        $ipData['equipment_type'] = $data['type'];
        $ipData['equipment_id'] = $data['id'];
        $ipData['machine_id'] = $data['machine_id'];
        $ipData['interface'] = $data['interface'];
        $ipData['ip'] = sprintf("%u", ip2long($ips[0]));
        $ipData['ip_string'] = $ips[0];
        $ipData['netmask'] = $ips[1];
        $ipData['netmask_string'] = self::getNetmaskByNum($ips[1]);
        $_ip = $this->isExist(array(
            'machine_id' => $data['machine_id']
            , 'ip' => $ipData['ip']
                ));
        if($_ip) {
            $id = $_ip['id'];
            $this->update($ipData, 'id = "' . $_ip['id'] . '"');
            return $id;
        } else {
            $id = $this->add($ipData);
            return $id;
        }
    }

    public function updateIp($data)
    {
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    /**
     * 判断是否已经存在
     * @param array $data
     * @return int id
     */
    public function isExist($data)
    {
        $r = $this->fetchRow(array('machine_id=?' => $data['machine_id'], 'ip=?' => $data['ip']));
        return $r;
    }

    /**
     * 删除不在 $ips 中列出的记录
     */
    public function deleteOldData($machineId, $ips)
    {
        $_ips = $this->fetchAll(array('machine_id=?' => $machineId));
        $dbips = array();
        foreach($_ips as $key => $a)
        {
            $dbips[] = $a['id'];
        }
        $delIds = array_diff($dbips, $ips);
        foreach($delIds as $del_id)
        {
            $this->delete(array('id=?' => $del_id));
        }
        return count($delIds);
    }

    /**
     * 获取 netmask
     * @param type $num
     * @return type
     */
    private function getNetmaskByNum($num)
    {
        return (long2ip(ip2long("255.255.255.255")
                        << (32 - $num)));
    }

    /**
     * 获取某网段的所有机器
     * @param type $network
     */
    public function getAll($network = null)
    {
        $select = $this->getSelect();
        $conditions = array();
        if(isset($network) && strlen($network) > 0) {
            $subnetwork = new Etao_Common_SubNetwork();
            $subnetwork->setAddressString($network);
            $conditions['start'] = $subnetwork->getNetwork();
            $conditions['end'] = $subnetwork->getBroadcast();
            $select->where('ip > :start');
            $select->where('ip < :end');
        }
        $select->order('ip');
        return $this->queryAll($select, $conditions);
    }

    public function getIpGraphic($network)
    {
        $ipList = $this->getAll($network);
        $ipArray = array();
        if(count($ipList) > 0) {
            foreach($ipList as $ip)
            {
                $ipArray[] = $ip->ip;
            }
        }
        $subnetwork = new Etao_Common_SubNetwork();
        $subnetwork->setAddressString($network);
        $start = intval($subnetwork->getNetwork()) + 1;
        $end = intval($subnetwork->getBroadcast());
        $graphicArray = array();
        while($start < $end)
        {
            $color = in_array($start, $ipArray) ? 'red' : 'green';
            //$graphicArray[] = '[ip:"' . long2ip($start) . '",color:"'.$color.'"]';
            $graphicArray[] = array('ip' => long2ip($start), 'color' => $color);
            $start++;
        }
        return $graphicArray;
    }

    /**
     * 根据IP地址获取设备
     * @param type $ip_string
     * @return type
     */
    public function getEquipmentsByIpString($ip_string)
    {
        $select = $this->select();
        $select->where('ip_string LIKE ?', $ip_string);
        $select->order('ip ASC');
        $cl = $this->fetchAll($select)->toArray();
        return $cl;
    }

    /**
     * 根据设备类型和ID查找设备
     * @param type $equipmentType
     * @param type $equipmentId
     * @return type
     */
    public function getByEquipmentTypeAndEquipmentId($equipmentType, $equipmentId)
    {
        $select = $this->select();
        $select->where('equipment_type = ?', $equipmentType);
        $select->where('equipment_id =?', $equipmentId);
        $r = $this->fetchAll($select);
        return $r;
    }

    /**
     * 更新 machine 时，同步更新本表
     * @param type $machine
     */
    public function updateMachine($machine)
    {
        $details = $this->getByEquipmentTypeAndEquipmentId(Idc_Model_Equipment::TYPE_MACHINE, $machine['id']);
        foreach($details as $detail)
        {
            $detailData = array(
                'id' => $detail['id'],
                'name' => $machine['name'],
                'machine_id' => $machine['machine_id'],
            );
            $this->updateIp($detailData);
            unset($detailData);
        }
    }

}