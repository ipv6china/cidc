<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * MachineNetworkInterface
 */
class Idc_Model_MachineNetworkInterface extends Etao_Model_Base
{

    protected $_name = 'machine_network_interface';
    protected $_primary = 'id';
    protected $_fileds = array(
        'id',
        'machine_id',
        'interface',
        'speed',
        'mac',
        'ip',
        'route'
    );

    /**
     * 添加
     * @param type $data
     * @return type
     */
    public function addNetworkInterface($data)
    {
        $ni = array();
        $ni['machine_id'] = $data['machine_id'];
        $ni['interface'] = $data['interface'];
        $ni['speed'] = $data['speed'];
        $ni['mac'] = $data['mac'];
        $ni['ip'] = $data['ip'];
        $ni['route'] = $data['route'];
        $networkInterface = self::isExist(
                        array('machine_id' => $data['machine_id']
                            , 'interface' => $data['interface']));
        if($networkInterface) {
            $id = $networkInterface['id'];
            $this->update($ni, 'id = "' . $networkInterface['id'] . '"');
            return $id;
        } else {
            $id = $this->add($ni);
            return $id;
        }
    }

    /**
     * 判断是否已经存在
     * @param array $data
     * @return int id
     */
    public function isExist($data)
    {
        return $this->fetchRow(
                        array(
                            'machine_id=?' => $data['machine_id']
                            , 'interface=?' => $data['interface']
                        )
        );
    }

    /**
     * 删除不在 $interfaces 中列出的记录
     */
    public function deleteOldData($machineId, $interfaces)
    {
        $_interfaces = $this->fetchAll(array('machine_id=?' => $machineId));
        $dbInterfaces = array();
        foreach($_interfaces as $key => $a)
        {
            $dbInterfaces[] = $a['id'];
        }
        $delIds = array_diff($dbInterfaces, $interfaces);
        foreach($delIds as $del_id)
        {
            $this->delete(array('id=?' => $del_id));
        }
        return count($delIds);
    }

    /**
     * 根据 machineId 获取数据
     * @param type $machineId
     * @return type
     */
    public function getNetworkInterfaceByMachineId($machineId)
    {
        return $this->findByFieldAndValue('machine_id', $machineId);
    }

    /**
     * @todo 根据mac地址获取
     * @param type $mac
     */
    public function getByMac($mac)
    {
        return $this->findByFieldAndValue('mac', $mac);
    }

}