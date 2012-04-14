<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * NetworkEquipment
 *
 * @author y109
 */
class Idc_Model_NetworkEquipment extends Etao_Model_Base
{

    protected $_name = 'network_equipment';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'type',
        'name',
        'place',
        'height',
        'admin_dept',
        'admin_username',
        'admin_realname',
        'manufacturer',
        'product_name',
        'network_type_lan',
        'network_type_tel',
        'network_type_cnc',
        'ip',
        'cabinet_id',
        'cabinet_name',
        'cabinet_position',
        'description',
        'status'
    );

    /**
     * 添加
     * @param array $data
     * @return int
     */
    public function addNetworkEquipment($data)
    {
        $id = $this->add($data);
        $this->addLog('添加网络设备', $id, $data['name']);
        return $id;
    }

    public function updateNetworkEquipment($data)
    {
        $arr_old = $this->findById($data['id'])->toArray();
        $updateLog = Etao_Common_Util::arrayUpdateToString($data, $arr_old);
        $updateLog & $this->addLog('修改网络设备:' . $updateLog, $data['id'], $arr_old['name']);
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    public function findNetworkEquipmentsByIds($ids)
    {
        $select = $this->select();
        $select->where('id IN(?)', $ids);
        $r = $this->fetchAll($select)->toArray();
        return $r;
    }

    /**
     * 记录日志
     * @param type $message
     * @param type $equipmentId
     */
    public function addLog($message, $equipmentId, $equipmentName=null)
    {
        Idc_Model_Log::log(array('message' => $message,
            'priority' => LOG_INFO,
            'equipment_type' => Idc_Model_Equipment::TYPE_NETWORK_EQUIPMENT,
            'equipment_id' => $equipmentId,
            'equipment_name' => $equipmentName,
            'type' => Idc_Model_Log::TYPE_SYSTEM,
            'issystem' => true
        ));
    }

}