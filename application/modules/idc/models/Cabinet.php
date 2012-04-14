<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Cabinet
 *
 * @author y109
 */
class Idc_Model_Cabinet extends Etao_Model_Base
{

    protected $_name = 'cabinet';
    protected $_primary = 'id';
    protected $_fileds = array(
        'id',
        'idc_id',
        'name',
        'description',
        'place',
        'height',
        'height_used',
        'admin_dept',
        'admin_username',
        'admin_realname',
        'equipment_amount',
        'machine_amount',
        'locked',
        'description'
    );
    const EQUIPMENT_AMOUNT_PRE_CABINET = 15;
    protected $equipmentObj;
    protected $detailObj;
    protected $machineObj;

    public function __construct()
    {
        parent::__construct();
        $this->equipmentObj = new Idc_Model_Equipment();
        $this->detailObj = new Idc_Model_CabinetDetail();
    }

    public function addLog($message, $equipmentId, $equipmentName=null)
    {
        Idc_Model_Log::log(array('message' => $message,
            'priority' => LOG_INFO,
            'equipment_type' => Idc_Model_Equipment::TYPE_CABINET,
            'equipment_id' => $equipmentId,
            'equipment_name' => $equipmentName,
            'type' => Idc_Model_Log::TYPE_SYSTEM,
            'issystem' => true
        ));
    }

    /**
     * 添加
     */
    public function addCabinet($data)
    {
        $id = $this->add($data);
        $this->addLog('添加机柜', $id, $data['name']);
        return $id;
    }

    /**
     * 更新
     * @param type $data
     * @return type
     */
    public function updateCabinet($data)
    {
        $arr_old = $this->findById($data['id'])->toArray();
        $updateLog = Etao_Common_Util::arrayUpdateToString($data, $arr_old);
        $updateLog && $this->addLog('修改机柜:' . $updateLog, $data['id'], $arr_old['name']);
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    /**
     * 根据 CabinetId 获取机柜中的设备列表
     */
    public function getEquipmentsByCabinetId($cabinetId)
    {
        $eqs = $this->detailObj->getEquipments($cabinetId);
        $eqs = $eqs->toArray();
        return $eqs;
    }

    /**
     * 向机柜中添加设备
     */
    public function addEquipmentToCabinet($equipmentType, $equipmentId, $cabinetId)
    {
        //查找设备
        $equipment = $this->equipmentObj->findEquipmentByEquipmentId($equipmentType, $equipmentId);
        if(empty($equipment)) {
            throw new Exception('没有 Id = ' . $equipmentId . ' 的 ' . $this->equipmentObj->TYPE[$equipmentType] . '');
            return;
        }
        //查找机柜
        $cabinet = $this->findById($cabinetId);
        if(empty($cabinet)) {
            throw new Exception('没有 Id = ' . $cabinetId . ' 机柜');
            return;
        }
        //查找设备当前所在机柜
        $oldposition = $this->detailObj->getEquipmentByTypeAndId($equipment['type'], $equipment['id']);
        if($oldposition) {
            if(intval($oldposition['cabinet_id']) != intval($cabinetId)) {
                $this->equipmentObj->addLog('调整机柜[' . $oldposition['cabinet_name'] . ' > ' . $cabinet['name'] . ']', $equipment['type'], $equipment['id'], $equipment['name']);
            }
            $oldposition['cabinet_id'] = $cabinetId;
            $oldposition['cabinet_name'] = $cabinet['name'];
            $oldposition['equipment_type'] = $equipment['type'];
            $oldposition['equipment_name'] = $equipment['name'];
            $oldposition['equipment_id'] = $equipment['id'];
            $oldposition['equipment_height'] = $equipment['height'];
            $oldposition['equipment_status'] = $equipment['status'];
            $oldposition['device_tag'] = $equipment['device_tag'];
            $this->detailObj->updateCabinetDetail($oldposition);
            $detailId = $oldposition['id'];
        } else {
            //添加 detail 记录
            $newDetail = array();
            $newDetail['cabinet_id'] = $cabinetId;
            $newDetail['cabinet_name'] = $cabinet['name'];
            $newDetail['equipment_type'] = $equipment['type'];
            $newDetail['equipment_name'] = $equipment['name'];
            $newDetail['equipment_id'] = $equipment['id'];
            $newDetail['equipment_height'] = $equipment['height'];
            $newDetail['equipment_status'] = $equipment['status'];
            $newDetail['device_tag'] = $equipment['device_tag'];
            $newDetail['position'] = 0;
            $detailId = $this->detailObj->addCabinetDetail($newDetail);
            $this->equipmentObj->addLog('设备入柜[' . $cabinet->name . ']', $equipment['type'], $equipment['id'], $equipment['name']);
        }
        //修改设备cabinetId,cabinetname
        $cabinetData = $cabinet->toArray();
        $this->equipmentObj->updateEquipmentCabinet($equipmentType, $equipmentId, $cabinetData);
        return $detailId;
    }

    /**
     * 从机柜中移除设备
     */
    public function removeEquipmentFromCabinet($id)
    {
        $detail = $this->detailObj->findById($id);
        $this->equipmentObj->removeFromCabinet($detail->cabinet_name, $detail->equipment_type, $detail->equipment_id, $detail->equipment_name);
        return $this->detailObj->delByID($detail->id);
    }

    public function getCabinetByIdcId($idcId)
    {
        //return $this->queryByFieldAndValue('idc_id', $idcId);
        $select = $this->select();
        $select->where('idc_id = ?', $idcId);
        $select->order('name');
        // $sql = $select->__toString();var_dump($sql);exit;
        return $this->fetchAll($select);
    }

    /**
     * 删除机柜
     * @param type $id
     */
    public function deleteCabinet($id)
    {
        $cabinet = $this->load($id);
        $details = $this->detailObj->queryByFieldAndValue('cabinet_id', $id);
        foreach($details as $detail)
        {
            $this->equipmentObj->removeFromCabinet($detail->cabinet_name, $detail->equipment_type, $detail->equipment_id, $detail->equipment_name);
            $this->detailObj->delByID($detail->id);
        }
        $this->addLog('删除机柜 [' . $cabinet->name . ']', $id, $cabinet->name);

        return $this->delByID($id);
    }

}