<?php

class DomainController extends App_CommonController
{

    public $domainZoneObj;
    public $domainRecordObj;

    public function init()
    {
        parent::init();
        $this->view->assign('title', '域名管理');
        $this->domainZoneObj = new Idc_Model_DomainZone();
        $this->domainRecordObj = new Idc_Model_DomainRecord();
    }

    public function indexAction()
    {

    }

    /**
     * 域名列表
     */
    public function jsondomainzonelistAction()
    {
        $result = Etao_Ext_GridList::getSimleListFromArray($this->domainZoneObj->fetchAll()->toArray());
        echo json_encode($result);
    }

    /**
     * 删除域名
     */
    public function jsondeldomainzoneAction()
    {
        $domainId = intval($this->getParam('id', ''));
        if($domainId > 0) {
            $this->domainZoneObj->deleteZone($domainId);
            echo '删除成功';
        }
    }

    /**
     * 删除DNS记录
     */
    public function jsondeldomainrecordAction()
    {
        $id = intval($this->getParam('id', ''));
        if($id > 0) {
            $this->domainRecordObj->deleteDomainRecord($id);
            echo '删除成功';
        }
    }

    /**
     * DNS记录列表
     */
    public function jsondomainrecordlistAction()
    {
        $domainId = intval($this->getParam('domainId', ''));
        $domain = $this->getParam('domain', '');
        if($domainId > 0) {
            $list = $this->domainRecordObj->getRecordsByDomainId($domainId);
        } else {
            $list = $this->domainRecordObj->getRecordsByDomain($domain);
        }
        $result = Etao_Ext_GridList::getSimleListFromArray($list);
        echo json_encode($result);
    }

    /**
     * 域名树
     */
    public function jsondomainzonetreeAction()
    {
        $list = $this->domainZoneObj->fetchAll()->toArray();
        $data = array();
        foreach($list as $e)
        {
            $e = (array)$e;
            $_text = $e['name'];
            strlen($e['description']) > 0 && $_text.= ' <font color="#369773a">' . $e['description'] . '</font>';
            $e['text'] = $_text;
            $data[] = $e;
        }
        $tree = Etao_Ext_Tree::arrayToExtTree($data, 'id', 'parent', 'children', false);
        echo json_encode($tree);
    }

    /**
     * 保存域名
     */
    public function jsondomainzonesaveAction()
    {
        $this->view->title = '保存';
        $zone = array();
        if($this->getRequest()->isPost()) {
            $_id = $this->getParam('id', 0);
            $zone['name'] = $this->getParam('name');
            $zone['ns'] = $this->getParam('ns');
            $zone['admin_username'] = $this->getParam('admin_username');
            $zone['description'] = $this->getParam('description');

            if($_id == 0) {
                $id = $this->domainZoneObj->addZone($zone);
            } else {
                $zone['id'] = $_id;
                $id = $this->domainZoneObj->updateZone($zone);
            }

            if($id > 0) {
                echo '{"msg":"保存成功","success":true,"data":{"id":' . $id . '}}';
            } else {
                echo '{"msg":"保存失败","success":false,"data":{"id":' . $id . '}}';
            }
        }
    }

    /**
     * 保存DNS记录
     */
    public function jsondomainrecordsaveAction()
    {
        $this->view->title = '保存';
        $record = array();
        if($this->getRequest()->isPost()) {
            $_id = $this->getParam('id', 0);
            $record['zone_id'] = $this->getParam('zone_id');
            $record['zone_name'] = $this->getParam('zone_name');
            $record['name'] = $this->getParam('name');
            $record['address'] = $this->getParam('address');
            $record['type'] = $this->getParam('type');
            $record['priority'] = $this->getParam('priority');
            $record['active'] = $this->getParam('active');
            $record['description'] = $this->getParam('description');

            if($_id == 0) {
                $id = $this->domainRecordObj->addRecord($record);
            } else {
                $id = $record['id'] = $_id;
                $this->domainRecordObj->updateRecord($record);
            }

            if($id > 0) {
                echo '{"msg":"保存成功","success":true,"data":{"id":' . $id . '}}';
            } else {
                echo '{"msg":"保存失败","success":false,"data":{"id":' . $id . '}}';
            }
        }
    }

     /**
     * 画图
     */
    public function imageAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_id = $this->getParam('id', 0);
        if((int)$_id > 0) {
            $this->domainZoneObj->getGraphVizImage($_id);
        }
    }

}
