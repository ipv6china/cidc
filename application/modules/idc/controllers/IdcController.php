<?php

class IdcController extends App_CommonController
{

    protected $idcObj;
    protected $cabinetObj;

    public function init()
    {
        parent::init();
        $this->idcObj = new Idc_Model_Idc();
        $this->cabinetObj = new Idc_Model_Cabinet();
    }

    public function indexAction()
    {
        $url = $this->getUrl('index', 'index', 'idc', array());
        $this->_redirect($url);
    }

    public function jsontreeAction()
    {
        $idcList = $this->idcObj->fetchAll()->toArray();
        $id = 0;
        $treeArray = array();
        foreach($idcList as $idc)
        {
            $idc['idc_id'] = $idc['id'];
            $idc['idc_name'] = $idc['name'];
            $idc['id'] = ++$id;
            $idc['text'] = $idc['name'];
            $treeArray[] = $idc;
            $cabinets = $this->cabinetObj->getCabinetByIdcId($idc['idc_id'])->toArray();
            foreach($cabinets as $cabinet)
            {
                $cabinet['cabinet_id'] = $cabinet['id'];
                $cabinet['idc_name'] = $idc['name'];
                $cabinet['id'] = ++$id;
                $cabinet['text'] = $cabinet['name'];
                $cabinet['parent'] = $idc['id'];
                $treeArray[] = $cabinet;
            }
        }
        $tree = Etao_Ext_Tree::arrayToExtTree($treeArray, 'id', 'parent', 'children', false);
        echo json_encode($tree);
    }

    public function jsonallAction()
    {
        $idcList = $this->idcObj->queryAll();
        $result = Etao_Ext_GridList::getSimleListFromArray($idcList);
        echo json_encode($result);
    }

    public function jsonsaveAction()
    {
        $this->view->title = '保存';
        $idc = array();
        if($this->getRequest()->isPost()) {
            $_id = intval($this->getParam('id', ''));
            $idc['name'] = $this->getParam('name');
            $idc['address'] = $this->getParam('address');
            $idc['contact'] = $this->getParam('contact');
            $idc['tel'] = $this->getParam('tel');
            $idc['description'] = $this->getParam('description');
            if($_id == 0) {
                $id = $this->idcObj->addIdc($idc);
            } else {
                $idc['id'] = $_id;
                $this->idcObj->updateIdc($idc);
                $id = $_id;
            }

            if($id > 0) {
                echo '{"msg":"保存 机房 成功","success":true,
                       "data":{
                        "id":' . $id . '}
                       }';
            } else {
                echo '{"msg":"保存 机房 失败",
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

