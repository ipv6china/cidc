<?php

class NetworkequipmentController extends App_CommonController
{

    private $networkEquipmentObj;
    private $cabinetObj;
    private $equipmentObj;

    public function init()
    {
        parent::init();
        $this->networkEquipmentObj = new Idc_Model_NetworkEquipment();
        $this->cabinetObj = new Idc_Model_Cabinet();
        $this->equipmentObj = new Idc_Model_Equipment();
        $this->view->assign('title', '网路设备管理');
    }

    public function indexAction()
    {
        $select = $this->networkEquipmentObj->getSelect();
        $conditions = array();
        $urls = array();
        $equipmentList = $this->networkEquipmentObj->queryAll($select, $conditions);
        $this->view->equipmentList = $equipmentList;
        $paginator = Zend_Paginator::factory($equipmentList);
        //每页条数
        $paginator->setItemCountPerPage(20);
        $this->view->paginator = $paginator;

        //获得当前页，设置当前页数
        $paginator->setCurrentPageNumber($this->getParam('page'));
        $this->view->equipments = $paginator;
    }

    /**
     * 信息
     */
    public function infoAction()
    {
        $this->view->assign('title', '设备资料');
        $id = $this->getParam('id', '');
        if(!isset($id) || intval($id) < 1) {
            $this->addMessage('id 格式不正确');
            $this->_redirect($this->getUrl('index'));
        }
        $e = $this->networkEquipmentObj->findById($id);

        if($e) {
            $this->view->equipment = $e;
        } else {
            $this->addMessage('没有找到 id = ' . $id . ' 的网络设备');
            $this->_redirect($this->getUrl('index'));
        }
    }

    public function jsoninfoAction()
    {
        $id = $this->getParam('id', '');
        $e = $this->networkEquipmentObj->findById($id);
        if($e) {
            echo json_encode($e->toArray());
        }
    }

    public function jsonsaveAction()
    {
        $this->view->title = '保存';
        $e = array();
        if($this->getRequest()->isPost()) {
            $_id = intval($this->getParam('id', ''));
            $e['name'] = $this->getParam('name');
            $e['height'] = $this->getParam('height');
            $e['admin_dept'] = $this->getParam('admin_dept');
            $e['admin_username'] = $this->getParam('admin_username');
            $e['admin_realname'] = $this->getParam('admin_realname');
            $e['manufacturer'] = $this->getParam('manufacturer');
            $e['product_name'] = $this->getParam('product_name');
            $e['network_type_lan'] = $this->getParam('network_type_lan');
            $e['network_type_tel'] = $this->getParam('network_type_tel');
            $e['network_type_cnc'] = $this->getParam('network_type_cnc');
            $e['device_tag'] = $this->getParam('device_tag');
            $e['status'] = $this->getParam('status');
            $e['ip'] = $this->getParam('ip');
            $e['cabinet_id'] = $this->getParam('cabinet_id');
            //$e['cabinet_position'] = $this->getParam('cabinet_position');
            $e['description'] = $this->getParam('description');
            if($_id == 0) {
                $id = $this->networkEquipmentObj->addNetworkEquipment($e);
            } else {
                $e['id'] = $_id;
                $this->networkEquipmentObj->updateNetworkEquipment($e);
                $id = $_id;
            }
            if(intval($e['cabinet_id']) > 0) {
                //向机柜中添加服务器
                $this->cabinetObj->addEquipmentToCabinet(Idc_Model_Equipment::TYPE_NETWORK_EQUIPMENT, $id, $e['cabinet_id']);
            }

            if($id > 0) {
                echo '{"msg":"保存 网络设备 成功","success":true,
                       "data":{
                        "id":' . $id . '}
                       }';
            } else {
                echo '{"msg":"保存 网络设备 失败",
                     "success":false,
                       "data":{
                        "id":' . $id . '}
                       }';
            }
        }
    }

    public function jsondelAction()
    {
        $id = $this->getParam('id');
        if($id > 0) {
            $this->idcObj->delById($id);
            echo '删除成功';
        }
    }

}

