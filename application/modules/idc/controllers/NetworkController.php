<?php

class NetworkController extends App_CommonController
{

    protected $networkObj;

    public function init()
    {
        parent::init();
        $this->view->assign('title', '网络管理');
        $this->networkObj = new Idc_Model_Network();
    }

    public function indexAction()
    {

    }

    public function jsonlistAction()
    {
        $networkList = $this->networkObj->fetchAll()->toArray();
        $data = array();
        foreach($networkList as $e)
        {
            $e = (array)$e;
            $data[] = $e;
        }
        $list = new Etao_Ext_GridList();
        $list->setStart(0);
        $list->setLimit(count($networkList));
        $list->setTotal(count($networkList));
        $list->setRows($data);

        echo $list->toJson();
    }

    public function jsontreeAction()
    {
        $networkList = $this->networkObj->fetchAll()->toArray();
        $data = array();
        foreach($networkList as $e)
        {
            $e = (array)$e;
            $_text = $e['network_string'];
            strlen($e['netmask_string']) > 0 && $_text.= '/' . Etao_Common_SubNetwork::mask2cidr($e['netmask_string']);
            //strlen($e['description']) > 0 && $_text.= ' <font color="#369773a">' . $e['description'] . '</font>';
            $e['text'] = $_text;
            $data[] = $e;
        }
        $tree = Etao_Ext_Tree::arrayToExtTree($data, 'id', 'parent', 'children', false);
        echo json_encode($tree);
    }



    public function jsondelAction()
    {
        $id = $this->getParam('id');
        if($id > 0) {
            $this->networkObj->delById($id);
            echo '删除成功';
        }
    }

    public function jsonsaveAction()
    {
        $this->view->title = '保存';
        $network = array();
        if($this->getRequest()->isPost()) {
            //$parent = $this->networkObj->findNetworkByNetworkString($this->getParam('parent',0));
            $_id = $this->getParam('id', 0);
            $network['network_string'] = $this->getParam('network_string');
            $network['netmask_string'] = $this->getParam('netmask_string');
            $network['parent'] =  $this->getParam('parent', 0);
            $network['description'] = $this->getParam('description');
            if($_id == 0) {
                $id = $this->networkObj->addNetwork($network);
            } else {
                $network['id'] = $_id;
                $id = $this->networkObj->updateNetwork($network);
            }

            if($id > 0) {
                echo '{"msg":"保存 网络 成功","success":true,
                       "data":{
                        "id":' . $id . '}
                       }';
            } else {
                echo '{"msg":"保存 网络 失败",
                     "success":false,
                       "data":{
                        "id":' . $id . '}
                       }';
            }
        }
    }

}