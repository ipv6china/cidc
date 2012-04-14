<?php

/**
 * 机柜管理
 */
class CabinetController extends App_CommonController
{

    private $machineObj;
    private $cabinetObj;
    private $equipmentObj;
    private $cabinetDetailObj;

    public function init()
    {
        parent::init();
        $this->machineObj = new Idc_Model_Machine();
        $this->cabinetObj = new Idc_Model_Cabinet();
        $this->cabinetDetailObj = new Idc_Model_CabinetDetail();
        $this->equipmentObj = new Idc_Model_Equipment();
        $this->view->assign('title', '机柜管理');
    }

    public function indexAction()
    {
        $this->view->assign('title', '机柜管理');
        $select = $this->machineObj->getSelect();
        $conditions = array();
        $urls = array();
        $cabinetList = $this->cabinetObj->queryAll($select, $conditions);
        foreach($cabinetList as $c)
        {
            $equipmentList[$c->id] = $this->cabinetObj->getEquipmentsByCabinetId($c->id);
        }
        $this->view->equipmentList = $equipmentList;
        $paginator = Zend_Paginator::factory($cabinetList);
//每页条数
        $paginator->setItemCountPerPage(20);
        $this->view->paginator = $paginator;

//获得当前页，设置当前页数
        $paginator->setCurrentPageNumber($this->getParam('page'));
        $this->view->cabinets = $paginator;
    }

    /**
     * json Cainbinetlist
     */
    public function jsonlistAction()
    {
        $idc_id = $this->getParam('idc_id', '');
        if(strlen($idc_id) > 0) {
            $cabinetList = $this->cabinetObj->queryByFieldAndValue('idc_id',intval($idc_id));
        } else {
            $cabinetList = $this->cabinetObj->queryAll();
        }
        $result = Etao_Ext_GridList::getSimleListFromArray($cabinetList);
        echo json_encode($result);
    }

    public function jsoninfoAction()
    {
        $id = intval($this->getParam('id', ''));
        if($id < 1) {
            return;
        }
        $equipment = $this->cabinetObj->findById($id);
        if($equipment) {
            $equipment = $equipment->toArray();
            $equipmentList = $this->cabinetObj->getEquipmentsByCabinetId($id);
            $data = array();
            foreach($equipmentList as $e)
            {
                $e = (array)$e;
                $e['title'] = $e['equipment_name'];
                $e['name'] = $e['equipment_name'];
                $e['height'] = $e['equipment_height'];
                $e['device_ip'] = $e['device_ip'];
                $data[] = $e;
            }
            $list = new Etao_Ext_GridList();
            $list->setStart(0);
            $list->setLimit(count($equipmentList));
            $list->setTotal(count($equipmentList));
            $list->setRows($data);

            echo $list->toJson();
        } else {
            return;
        }
    }

    public function jsoncabinetinfoAction()
    {
        $id = $this->getParam('id', '');
        $e = $this->cabinetObj->findById($id)->toArray();
        echo json_encode($e);
    }

    /**
     * 添加
     */
    public function addAction()
    {
        $this->view->assign('title', '添加机柜');
        $cabinet = array();
        if($this->getRequest()->isPost()) {
            $cabinet['idc_id'] = $this->getParam('idc_id');
            $cabinet['name'] = $this->getParam('name');
            $cabinet['place'] = $this->getParam('place');
            $cabinet['height'] = $this->getParam('height');
            $cabinet['height_used'] = $this->getParam('height_used');
            $cabinet['admin_dept'] = $this->getParam('admin_dept');
            $cabinet['admin_realname'] = $this->getParam('admin_realname');
            $cabinet['admin_username'] = $this->getParam('admin_username');
            $cabinet['equipment_amount'] = $this->getParam('equipment_amount');
            $cabinet['machine_amount'] = $this->getParam('machine_amount');
            $cabinet['description'] = $this->getParam('description');
            $id = $this->cabinetObj->addCabinet($cabinet);
            if($id) {
                $this->addMessage('添加成功');
                $this->_redirect($this->getUrl('index'));
            }
        }
        $this->render('form');
    }

    public function jsonsaveAction()
    {
        $this->view->title = '保存';
        $cabinet = array();
        if($this->getRequest()->isPost()) {
            $_id = intval($this->getParam('id', ''));
            $cabinet['idc_id'] = $this->getParam('idc_id');
            $cabinet['name'] = $this->getParam('name');
            $cabinet['place'] = $this->getParam('place');
            $cabinet['height'] = $this->getParam('height');
            $cabinet['height_used'] = $this->getParam('height_used');
            $cabinet['admin_dept'] = $this->getParam('admin_dept');
            $cabinet['admin_realname'] = $this->getParam('admin_realname');
            $cabinet['admin_username'] = $this->getParam('admin_username');
            $cabinet['equipment_amount'] = $this->getParam('equipment_amount');
            $cabinet['machine_amount'] = $this->getParam('machine_amount');
            $cabinet['locked'] = $this->getParam('locked');
            $cabinet['description'] = $this->getParam('description');
            if($_id == 0) {
                $id = $this->cabinetObj->addCabinet($cabinet);
            } else {
                $cabinet['id'] = $_id;
                $this->cabinetObj->updateCabinet($cabinet);
                $id = $_id;
            }

            if($id > 0) {
                echo '{"msg":"保存成功","success":true,"data":{"id":' . $id . '}}';
            } else {
                echo '{"msg":"保存失败","success":false,"data":{"id":' . $id . '}}';
            }
        }
    }

    /**
     * 向机柜中添加设备
     */
    public function addequipmentAction()
    {
        $this->view->title = '向机柜中添加设备';
        $cabinetId = $this->getParam('cabinet_id');
        if(empty($cabinetId) || intval($cabinetId) < 1) {
            $this->addMessage('机柜ID 不能为空');
            $this->_redirect($this->getUrl('index'));
        }
        $this->view->cabinet_id = $cabinetId;
        $this->view->equipment_id = 'A04EI21Y';
        $this->view->position = '0';
        if($this->getRequest()->isPost()) {
            $equipment_id = $this->getParam('equipment_id');
            $equipment_type = $this->getParam('equipment_type');
            $cabinet_id = $this->getParam('cabinet_id');
            $position = $this->getParam('position');

            $device_id = $this->cabinetObj->addEquipmentToCabinet($equipment_type, $equipment_id, $cabinet_id, $position);
            if($device_id > 0) {
                $this->addMessage('添加成功');
            } else {
                $this->addMessage('添加失败');
            }
            $this->_redirect($this->getUrl('index'));
        }
        $this->view->equipmentType = $this->equipmentObj->TYPE;
        $this->view->equipmentList = $t = $this->equipmentObj->getAll();
    }

    public function jsonaddequipmentAction()
    {
        $equipment_id = $this->getParam('equipment_id');
        $equipment_type = $this->getParam('equipment_type');
        $cabinet_id = $this->getParam('cabinet_id');

        $device_id = $this->cabinetObj->addEquipmentToCabinet($equipment_type, $equipment_id, $cabinet_id);
        if($device_id > 0) {
            echo '添加成功';
        } else {
            echo '添加失败';
        }
    }

    public function jsonremoveequipmentAction()
    {
        $id = $this->getParam('id');

        $result = $this->cabinetObj->removeEquipmentFromCabinet($id);
        if($result) {
            echo '成功';
        } else {
            echo '失败';
        }
    }

    public function jsondelAction()
    {
        $id = $this->getParam('id');
        if($id > 0) {
            $this->cabinetObj->deleteCabinet($id);
            echo '删除成功';
        }
    }

    public function jsonallAction()
    {
        $cabinetList = $this->cabinetObj->queryAll();
        $result = Etao_Ext_GridList::getSimleListFromArray($cabinetList);
        echo json_encode($result);
    }

    public function jsonupdatepositionAction()
    {
        $ids = $this->getParam('ids');
        $this->cabinetDetailObj->updateEquipmentPosition($ids);
        echo '改变设备顺序成功';
    }

}

