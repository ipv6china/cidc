<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * MachineController
 *
 * @author y109
 */
class MachineController extends App_CommonController
{

    private $machineObj;
    private $cabinetObj;

    public function init()
    {
        parent::init();
        $this->view->assign('title', '服务器管理');
        $this->machineObj = new Idc_Model_Machine();
        $this->cabinetObj = new Idc_Model_Cabinet();
    }

    public function indexAction()
    {
        $this->view->assign('title', '服务器管理');
        $select = $this->machineObj->getSelect();
        $conditions = array();
        $urls = array();
        $_machine_id = $this->getParam('machine_id');
        if(strlen($_machine_id) > 0) {
            $conditions['machine_id'] = '%' . $_machine_id . '%';
            $urls['machine_id'] = $_machine_id;
            $select->where('machine_id LIKE :machine_id');
            $this->view->machine_id = $_machine_id;
        }
        $_name = $this->getParam('name');
        if(strlen($_name) > 0) {
            $conditions['name'] = '%' . $_name . '%';
            $urls['name'] = $_name;
            $select->where('name LIKE :name');
            $this->view->name = $_name;
        }
        $_ip = $this->getParam('ip');
        if(strlen($_ip) > 0) {
            $conditions['ip'] = '%' . $_ip . '%';
            $urls['ip'] = $_ip;
            $select->where('network_interfaces LIKE :ip');
            $this->view->ip = $_ip;
        }
        $machineList = $this->machineObj->queryAll($select, $conditions);
        $paginator = Zend_Paginator::factory($machineList);
        //每页条数
        $paginator->setItemCountPerPage(20);
        $this->view->paginator = $paginator;

        //获得当前页，设置当前页数
        $paginator->setCurrentPageNumber($this->getParam('page'));
        $this->view->machines = $paginator;
    }

    /**
     * 导入
     */
    public function importAction()
    {
        $this->view->assign('title', '导入服务器');
        if($this->getRequest()->isPost()) {
            $data0 = json_decode($this->getParam('data'));
            if(!$data0) {
                $this->addMessage('JSON 格式有错误');
                $this->_redirect($this->getUrl('index'));
                return;
            }
            $data = array();
            is_object($data0) && $data[] = $data0;
            is_array($data0) && $data = $data0;
            $c_success = 0;
            $c_error = 0;
            foreach($data as $m)
            {
                $id = $this->machineObj->importMachine($m);
                $id > 0 ? $c_success++ : $c_error++;
            }
            $c_success > 0 && $this->addMessage('导入成功 ' . $c_success . ' 条记录');
            $c_error > 0 && $this->addMessage('导入失败 ' . $c_error . ' 条记录');
            $this->_redirect($this->getUrl('index'));
        }
    }

    /**
     * 导出
     */
    public function jsonexportAction()
    {
        $id = $this->getParam('id', '');
        if(!isset($id) || intval($id) > 0) {
            echo '<pre>';
            print_r($this->machineObj->export($id));
        }
    }

    /**
     * 服务器信息
     */
    public function infoAction()
    {

        $this->view->assign('title', '服务器资料');
        $id = $this->getParam('id', '');
        if(!isset($id) || intval($id) < 1) {
            $this->addMessage('id 格式不正确');
            $this->_redirect($this->getUrl('index'));
        }
        $e = $this->machineObj->findMachineById($id);

        if($e) {
            $this->view->equipment = $e;
        } else {
            $this->addMessage('没有找到 id = ' . $id . ' 的服务器');
            $this->_redirect($this->getUrl('index'));
        }
    }

    public function jsoninfoAction()
    {
        $id = $this->getParam('id', '');
        $e = $this->machineObj->findMachineById($id);
        $this->view->equipment = $e;
        echo json_encode($e);
    }

    public function jsonsaveAction()
    {
        $this->view->title = '保存';
        $machine = array();
        if($this->getRequest()->isPost()) {
            $_id = intval($this->getParam('id', ''));
            $machine['machine_id'] = $this->getParam('machine_id');
            $machine['name'] = $this->getParam('name');
            $machine['height'] = $this->getParam('height');
            $machine['host_machine'] = $this->getParam('host_machine');
            $machine['cpu'] = $this->getParam('cpu');
            $machine['memory'] = $this->getParam('memory');
            $machine['harddisk'] = $this->getParam('harddisk');
            $machine['manufacturer'] = $this->getParam('manufacturer');
            $machine['product_name'] = $this->getParam('product_name');
            $machine['serial_number'] = $this->getParam('serial_number');
            $machine['os'] = $this->getParam('os');
            $machine['plateform'] = $this->getParam('plateform');
            $machine['version'] = $this->getParam('version');
            $machine['admin_dept'] = $this->getParam('admin_dept');
            $machine['admin_username'] = $this->getParam('admin_username');
            $machine['admin_realname'] = $this->getParam('admin_realname');
            $machine['cabinet_id'] = $this->getParam('cabinet_id');
            //$machine['cabinet_position'] = $this->getParam('cabinet_position');
            $machine['device_tag'] = $this->getParam('device_tag');
            $machine['description'] = $this->getParam('description');
            $machine['status'] = $this->getParam('status');
            if($_id == 0) {
                $id = $this->machineObj->addMachine($machine);
            } else {
                $machine['id'] = $_id;
                $this->machineObj->updateMachine($machine);
                $id = $_id;
            }
            if(intval($machine['cabinet_id']) > 0) {
                //向机柜中添加服务器
                $this->cabinetObj->addEquipmentToCabinet(Idc_Model_Equipment::TYPE_MACHINE, $id, $machine['cabinet_id']);
            }
            if($id > 0) {
                echo '{"msg":"保存 服务器 成功","success":true,
                       "data":{
                        "id":' . $id . '}
                       }';
            } else {
                echo '{"msg":"保存 服务器 失败",
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
            $this->machineObj->delById($id);
            echo '删除成功';
        }
    }

}