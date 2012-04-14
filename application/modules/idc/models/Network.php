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
class Idc_Model_Network extends Etao_Model_Base
{

    protected $_name = 'network';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'network',
        'network_string',
        'netmask',
        'network_string',
        'parent',
        'description'
    );

    /**
     * 添加
     * @param array $data
     * @return int
     */
    public function addNetwork($data)
    {
        $data['network'] = sprintf("%u", ip2long($data['network_string']));
        $data['netmask'] = sprintf("%u", ip2long($data['netmask_string']));
        return $this->add($data);
    }

    public function updateNetwork($data){
        $data['network'] = sprintf("%u", ip2long($data['network_string']));
        $data['netmask'] = sprintf("%u", ip2long($data['netmask_string']));
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    /**
     * 根据networkString 获取数据库Id
     * @param type $networkString
     * @return int
     */
    public function findNetworkByNetworkString($networkString){
        $id = 0;
        $network = $this->fetchRow('network_string = "' . $networkString . '"');
        if($network){
            $id = $network->id;
        }
        return $id;
    }

}