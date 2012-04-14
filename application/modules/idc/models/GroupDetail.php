<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Idc 机房资料
 *
 * @author y109
 */
class Idc_Model_GroupDetail extends Etao_Model_Base
{

    protected $_name = 'group_detail';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'gid',
        'equipment_type',
        'equipment_id',
        'equipment_name',
        'description'
    );

    /**
     * 根据 组ID 获取组内成员
     * @param type $groupId
     * @return type
     */
    public function getDetailByGroupId($groupId)
    {
        return $this->queryByFieldAndValue('gid', $groupId);
    }

    /**
     * 添加
     * @param type $group
     * @return type
     */
    public function addGroupDetail($groupdetail)
    {
        return $this->add($groupdetail);
    }

    /**
     * 更新
     * @param type $data
     * @return type
     */
    public function updateGroupDetail($data)
    {
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    /**
     *  根据设备类型和ID查找设备
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
        if($r){
        return $r->toArray();
        }
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
                'equipment_name' => $machine['name'],
                'device_tag' => $machine['device_tag'],
            );
            $this->updateGroupDetail($detailData);
            unset($detailData);
        }
    }

}