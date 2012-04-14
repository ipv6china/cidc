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
class Idc_Model_Group extends Etao_Model_Base
{

    protected $_name = 'group';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'name',
        'parent',
        'description'
    );
    protected $detailObj;
    protected $equipmentObj;

    public function __construct()
    {
        parent::__construct();
        $this->detailObj = new Idc_Model_GroupDetail();
        $this->equipmentObj = new Idc_Model_Equipment();
    }

    public function addLog($message, $equipmentId, $equipmentName=null)
    {
        Idc_Model_Log::log(array('message' => $message,
            'priority' => LOG_INFO,
            'equipment_type' => Idc_Model_Equipment::TYPE_GROUP,
            'equipment_id' => $equipmentId,
            'equipment_name' => $equipmentName,
            'type' => Idc_Model_Log::TYPE_SYSTEM,
            'issystem' => true
        ));
    }

    /**
     * 添加
     * @param type $group
     * @return type
     */
    public function addGroup($data)
    {
        $id = $this->add($data);
        $this->addLog('添加分组', $id, $data['name']);
        return $id;
    }

    /**
     * 更新
     * @param type $data
     * @return type
     */
    public function updateGroup($data)
    {
        $arr_old = $this->findById($data['id'])->toArray();
        $updateLog = Etao_Common_Util::arrayUpdateToString($data, $arr_old);
        $updateLog && $this->addLog('修改分组:' . $updateLog, $data['id'], $arr_old['name']);
        if(key_exists('name', $data) && $arr_old['name'] != $data['name']) {
            $details = $this->detailObj->queryByFieldAndValue('gid', $data['id']);
            foreach($details as $detail)
            {
                $this->detailObj->updateGroupDetail(array('id' => $detail->id, 'gname' => $data['name']));
            }
        }
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    /**
     * @todo 删除分组
     * @param type $id
     */
    public function deleteGroup($id)
    {
        $group = $this->load($id);
        $details = $this->detailObj->queryByFieldAndValue('gid', $id);
        foreach($details as $detail)
        {
            $this->equipmentObj->removeFromGroup($group->name, $detail->equipment_type, $detail->equipment_id, $detail->equipment_name);
            $this->detailObj->delByID($detail->id);
        }
        $this->addLog('删除分组 [' . $group->name . ']', $id, $group->name);

        return $this->delById($id);
    }

}