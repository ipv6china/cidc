<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Idc_Model_DomainZone
 *
 * @author y109
 */
class Idc_Model_DomainZone extends Etao_Model_Base
{

    protected $_name = 'domain_zone';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'name',
        'ns',
        'server',
        'admin_username',
        'admin_realname',
        'description'
    );
    protected $domainRecordObj;

    public function __construct()
    {
        parent::__construct();
        $this->domainRecordObj = new Idc_Model_DomainRecord();
    }

    public function addLog($message, $equipmentId, $equipmentName=null)
    {
        Idc_Model_Log::log(array('message' => $message,
            'priority' => LOG_INFO,
            'equipment_type' => Idc_Model_Equipment::TYPE_DOMAIN_ZONE,
            'equipment_id' => $equipmentId,
            'equipment_name' => $equipmentName,
            'type' => Idc_Model_Log::TYPE_SYSTEM,
            'issystem' => true
        ));
    }

    /**
     * 添加
     * @param type $zone
     * @return type
     */
    public function addZone($data)
    {
        $id = $this->add($data);
        $this->addLog('添加 DNS 区域', $id, $data['name']);
        return $id;
    }

    /**
     * 更新
     * @param type $zone
     * @return type
     */
    public function updateZone($data)
    {
        $arr_old = $this->findById($data['id'])->toArray();
        $updateLog = Etao_Common_Util::arrayUpdateToString($data, $arr_old);
        $updateLog && $this->addLog('修改 DNS 区域 ' . $updateLog, $data['id'], $arr_old['name']);
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    /**
     * 删除
     * @param type $id
     * @return type
     */
    public function deleteZone($id)
    {
        $zone = $this->findById($id);
        $details = $this->domainRecordObj->queryByFieldAndValue('zone_id', $id);
        foreach($details as $detail)
        {
            $this->domainRecordObj->deleteDomainRecord($detail->id);
        }
        $this->addLog('删除域名 ' . $zone->name, $id, $zone->name);
        return $this->delByID($id);
    }

    public function getGraphVizImage($id)
    {
        $zone = $this->findById($id);

        if($zone) {
            $graphvizObj = new Etao_Image_GraphViz();
            //引擎
            //$graphvizObj->setLayoutEngine('circle');
            $graphvizObj->setLayoutEngine('dot');
            //文件格式
            $graphvizObj->setFileFormat('svg');
            $gattrs = array();
            $gattrs['rankdir'] = 'LR';
            $gattrs['splines'] = 'line';
            $gattrs['label'] = $zone->name . ' DNS 关系图';
            $graphvizObj->addAttributes($gattrs);
            $details = $this->domainRecordObj->queryByFieldAndValue('zone_id', $id);
            if(count($details) > 0) {
                foreach($details as $detail)
                {
                    $edge = array($detail->name.'.'.$detail->zone_name => $detail->address);
                    $attributes = array();
                    $color = $this->rand_color();
                    $attributes['taillabel'] = '[' . $detail->type . ']';
                    $attributes['fontcolor'] = $color;
                    $attributes['fontsize'] = 8;

                    $attributes['color'] = $color;
                    $attributes['minlen'] = 4;
                    $attributes['label'] = $detail->description;

                    $graphvizObj->addEdge($edge, $attributes);
                }
                $graphvizObj->image();
            }
        }
    }

    /**
     * 随机产生一个颜色
     * @return type
     */
    private function rand_color()
    {
        for($a = 0; $a < 6; $a++)
        {    //采用#FFFFFF方法，
            $d.=dechex(rand(0, 15));//累加随机的数据--dechex()将十进制改为十六进制
        }
        return '#' . $d;
    }

}