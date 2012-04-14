<?php

class EquipmentController extends App_CommonController
{

    protected $equipmentObj;
    protected $machineObj;
    protected $networkEquipmentObj;

    public function init()
    {
        parent::init();
        $this->equipmentObj = new Idc_Model_Equipment();
        $this->machineObj = new Idc_Model_Machine();
        $this->networkEquipmentObj = new Idc_Model_NetworkEquipment();
    }

    public function indexAction()
    {
        $url = $this->getUrl('index', 'cabinet', 'idc', array());
        $this->_redirect($url);
    }

    public function infoAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getParam('id', '');
        $type = $this->getParam('type', '');
        if(!isset($type) || !isset($id) || strlen($id) < 1) {
            $this->addMessage('参数错误');
            $url = $this->getUrl('index', 'cabinet', 'idc', array());
            $this->_redirect($url);
        }
        switch($type)
        {
            case Idc_Model_Equipment::TYPE_MACHINE:
                $url = $this->getUrl('info', 'machine', 'idc', array('id' => $id));
                break;
            case Idc_Model_Equipment::TYPE_NETWORK_EQUIPMENT:
                $url = $this->getUrl('info', 'networkequipment', 'idc', array('id' => $id));
                break;
            default:
                break;
        }

        $this->_redirect($url);
    }

    public function jsonfreeequipmentAction()
    {
        $queryType = $this->getParam('queryType', '');
        $queryWord = $this->getParam('queryWord', '');
        if(strlen($queryType) > 0 && strlen($queryWord) > 0) {
            $queryWord = '%' . $queryWord . '%';
        }
        $equipmentList = $this->equipmentObj->getFreeEquipment($queryType, $queryWord);
        $result = Etao_Ext_GridList::getSimleListFromArray($equipmentList);
        echo json_encode($result);
    }

    public function jsonallequipmentsAction()
    {
        $queryType = $this->getParam('queryType', '');
        $queryWord = $this->getParam('queryWord', '');
        if(strlen($queryType) > 0 && strlen($queryWord) > 0) {
            if('ip' == $queryType) {
                $equipmentList = $this->equipmentObj->getEquipmentByQueryIp($queryWord);
            } else if('mac' == $queryType) {
                $equipmentList = $this->equipmentObj->getEquipmentByQueryMac(strtolower($queryWord));
            } else {
                $equipmentList = $this->equipmentObj->getEquipmentByQuery($queryType, '%' . $queryWord . '%');
            }
        } else {
            $equipmentList = $this->equipmentObj->getAll();
        }

        $result = Etao_Ext_GridList::getSimleListFromArray($equipmentList);
        echo json_encode($result);
    }

    public function jsonqueryAction()
    {
        $queryType = $this->getParam('queryType', '');
        $queryWord = $this->getParam('queryWord', '');
        if(strlen($queryType) > 0 && strlen($queryWord) > 0) {
            if('ip' == $queryType) {
                $equipmentList = $this->equipmentObj->getEquipmentByQueryIp('%' . $queryWord . '%');
            } elseif('mac' == $queryType) {
                $equipmentList = $this->equipmentObj->getEquipmentByQueryMac($queryWord);
            } else {
                $equipmentList = $this->equipmentObj->getEquipmentByQuery($queryType, '%' . $queryWord . '%');
            }
        } else {
            $equipmentList = $this->equipmentObj->getAll();
        }

        $result = Etao_Ext_GridList::getSimleListFromArray($equipmentList);
        echo json_encode($result);
    }

}

