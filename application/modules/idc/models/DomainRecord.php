<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Idc_Model_DomainRecord
 *
 * @author y109
 */
class Idc_Model_DomainRecord extends Etao_Model_Base
{

    protected $_name = 'domain_record';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'zone_id',
        'zone_name',
        'name',
        'address',
        'type',
        'priority',
        'active',
        'description'
    );

    public function addLog($message, $equipmentId, $equipmentName=null)
    {
        Idc_Model_Log::log(array('message' => $message,
            'priority' => LOG_INFO,
            'equipment_type' => Idc_Model_Equipment::TYPE_DOMAIN_RECORD,
            'equipment_id' => $equipmentId,
            'equipment_name' => $equipmentName,
            'type' => Idc_Model_Log::TYPE_SYSTEM,
            'issystem' => true
        ));
    }

    /**
     * 根据域名获得DNS记录
     * @param type $domain
     */
    public function getRecordsByDomain($domain)
    {
        $select = $this->getSelect();
        $conditions = array();
        if(isset($domain) && strlen($domain) > 0) {
            $select->where('name LIKE :domain');
            $conditions['domain'] = '%' . $domain . '%';
        }
        $select->order('id');
        return $this->queryAll($select, $conditions);
    }

    /**
     * 根据域名获得DNS记录
     * @param type $domain
     */
    public function getRecordsByDomainId($domainId)
    {
        $select = $this->getSelect();
        $conditions = array();
        if(isset($domainId) && $domainId > 0) {
            $select->where('zone_id = :domainId');
            $conditions['domainId'] = $domainId;
        }
        $select->order('id');
        return $this->queryAll($select, $conditions);
    }

    /**
     * 添加
     * @param type $zone
     * @return type
     */
    public function addRecord($data)
    {
        $id = $this->add($data);
        $this->addLog('添加 DNS 记录', $id, $data['name']);
        return $id;
    }

    /**
     * 更新
     * @param type $zone
     * @return type
     */
    public function updateRecord($data)
    {
        $arr_old = $this->findById($data['id'])->toArray();
        $updateLog = Etao_Common_Util::arrayUpdateToString($data, $arr_old);
        $updateLog && $this->addLog('修改 DNS 记录:' . $updateLog, $data['id'], $arr_old['name'] . '.' . $arr_old['zone_name']);
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    /**
     * 删除
     * @param type $id
     * @return type
     */
    public function deleteDomainRecord($id)
    {
        $data = $this->findById($id);
        $this->addLog('删除域名记录 [' . $data->name . '.' . $data->zone_name . ']', $id, $data->name . '.' . $data->zone_name);
        return $this->delByID($id);
    }

}