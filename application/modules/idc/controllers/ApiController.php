<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * ApiController
 *
 * @author y109
 */
class ApiController extends App_CommonController
{

    protected $equipmentObj;
    protected $machineObj;
    protected $networkEquipmentObj;

    public function init()
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        $whiteIps = array('127.0.0.1', '124.207.144.197');
        if(in_array($ip, $whiteIps)) {
            $this->noLogin = true;
        } else {
            exit('ip:' . $ip . ' is not trust!');
        }
        parent::init();
        $this->equipmentObj = new Idc_Model_Equipment();
        $this->machineObj = new Idc_Model_Machine();
        $this->networkEquipmentObj = new Idc_Model_NetworkEquipment();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function queryAction()
    {
        $queryType = $this->getParam('t', '');
        $queryWord = $this->getParam('w', '');
        if(strlen($queryType) > 0 && strlen($queryWord) > 0) {
            if('ip' == $queryType) {
                $equipmentList = $this->equipmentObj->getEquipmentByQueryIp($queryWord);
            } else if('mac' == $queryType) {
                $equipmentList = $this->equipmentObj->getEquipmentByQueryMac(strtolower($queryWord));
            } else {
                $equipmentList = $this->equipmentObj->getEquipmentByQuery($queryType, '%' . $queryWord . '%');
            }
        } else {
            //$equipmentList = $this->equipmentObj->getAll();
            $equipmentList = array();
        }
        if(count($equipmentList) > 0) {
            $result = Etao_Ext_GridList::getSimleListFromArray($equipmentList);
            echo json_encode($result);
        } else {
            echo 'null';
        }
    }

}