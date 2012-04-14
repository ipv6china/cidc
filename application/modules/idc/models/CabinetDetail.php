<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * CabinetDetail
 *
 * @author y109
 */
class Idc_Model_CabinetDetail extends Etao_Model_Base
{

    protected $_name = 'cabinet_detail';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'cabinet_id',
        'cabinet_name',
        'equipment_type',
        'equipment_id',
        'equipment_name',
        'equipment_status',
        'equipment_height',
        'position'
    );

    /**
     * 添加
     */
    public function addCabinetDetail($data)
    {
        $select = $this->select();
        $select->where('cabinet_id = ?', $data['cabinet_id']);
        $select->where('position >= ?', $data['position']);
        $select->order('position');
        $cl = $this->fetchAll($select)->toArray();
        $count_cl = count($cl);
        $i = 0;
        for($i = 1; $i < $count_cl; $i++)
        {
            $this->update(array('position' => ($data['position'] + $i)), 'id = ' . $cl[$i]['id']);
        }
        $id = $this->add($data);
        return $id;
    }

    /**
     * 根据 cabinet_id 获取设备列表
     */
    public function getEquipments($cabinetId)
    {
        $select = $this->select();
        $select->where('cabinet_id = ?', $cabinetId);
        $select->order('position');
        return $this->fetchAll($select);
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
        if(count($r) > 0) {
            return $r[0];
        }
    }

    /**
     * 更新 machine 时，同步更新本表
     * @param type $machine
     */
    public function updateMachine($machine)
    {
        $detail = $this->getByEquipmentTypeAndEquipmentId(Idc_Model_Equipment::TYPE_MACHINE, $machine['id']);
        if($detail) {
            $detailData = array(
                'id' => $detail['id'],
                'device_tag' => $machine['device_tag'],
                'equipment_name' => $machine['name'],
                'equipment_height' => $machine['height'],
                'equipment_status' => $machine['status']
            );
            $this->updateCabinetDetail($detailData);
        }
    }

    /**
     * 获取设备在机柜中的位置
     * @param type $equipmentType
     * @param type $equipmentId
     */
    public function getEquipmentByTypeAndId($equipmentType, $equipmentId)
    {
        $select = $this->select();
        $select->where('equipment_type = ?', $equipmentType);
        $select->where('equipment_id = ?', $equipmentId);
        $r = $this->fetchAll($select)->toArray();
        return $r[0];
    }

    /**
     * 更新
     * @param type $data
     * @return type
     */
    public function updateCabinetDetail($data)
    {
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    /**
     * 更新顺序
     * @param type $ids
     */
    public function updateEquipmentPosition($ids)
    {
        $equipmentObj = new Idc_Model_Equipment();
        $ids = explode(',', $ids);
        $_length = count($ids);
        for($i = 0; $i < $_length; $i++)
        {
            $oldData = $this->findById($ids[$i]);
            if(intval($oldData->position) != $i) {
                $data = array('id' => $ids[$i], 'position' => $i);
                $r = $this->updateCabinetDetail($data);
                //记录日志
                $e = $this->findById($ids[$i]);
                $equipmentObj->addLog('更新在机柜: ' . $e->cabinet_name . ' 中的顺序: ' . $oldData->position . '->' . $i, $e->equipment_type, $e->equipment_id, $e->equipment_name);
            }
        }
    }

}