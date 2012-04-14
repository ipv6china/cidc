<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * GroupController
 *
 * @author y109
 */
class GroupController extends App_CommonController
{

    protected $groupObj;
    protected $groupDetailObj;

    public function init()
    {
        parent::init();
        $this->view->assign('title', '分组管理');
        $this->groupObj = new Idc_Model_Group();
        $this->groupDetailObj = new Idc_Model_GroupDetail();
    }

    public function indexAction()
    {

    }

    public function jsonlistAction()
    {
        $list = $this->groupObj->fetchAll()->toArray();
        $result = Etao_Ext_GridList::getSimleListFromArray($list);
        echo json_encode($result);
    }

    public function jsondetaillistAction()
    {
        $groupId = intval($this->getParam('groupId', ''));
        if($groupId > 0) {
            $list = $this->groupDetailObj->getDetailByGroupId($groupId);
        } else {
           // $list = $this->groupDetailObj->fetchAll()->toArray();
            $list = array();
        }
        $result = Etao_Ext_GridList::getSimleListFromArray($list);
        echo json_encode($result);
    }

    public function jsontreeAction()
    {
        $groupList = $this->groupObj->fetchAll()->toArray();
        $data = array();
        foreach($groupList as $e)
        {
            $e = (array)$e;
            $_text = $e['name'];
            //strlen($e['description']) > 0 && $_text.= ' <font color="#369773a">' . $e['description'] . '</font>';
            $e['text'] = $_text;
            $e['expanded'] = false;
            $data[] = $e;
        }
        $tree = Etao_Ext_Tree::arrayToExtTree($data, 'id', 'parent', 'children', false);
        echo json_encode($tree);
    }

    public function jsondelAction()
    {
        $id = $this->getParam('id');
        if($id > 0) {
            $this->groupObj->deleteGroup($id);
            echo '删除成功';
        }
    }

    //@todo 设备从分组中移除成功
    public function jsondetaildelAction()
    {
        $id = $this->getParam('id');
        if($id > 0) {
            $this->groupDetailObj->delById($id);
            echo '设备从分组中移除成功';
        }
    }

    public function jsonsaveAction()
    {
        $this->view->title = '保存';
        $group = array();
        if($this->getRequest()->isPost()) {
            $_id = intval($this->getParam('id', ''));
            $group['name'] = $this->getParam('name');
            $parent = $this->getParam('parent', 0);
            $group['parent'] = $parent;
            $group['description'] = $this->getParam('description');
            if($_id == 0) {
                $id = $this->groupObj->addGroup($group);
            } else {
                $group['id'] = $_id;
                $this->groupObj->updateGroup($group);
                $id = $_id;
            }

            if($id > 0) {
                echo '{"msg":"保存成功","success":true,
                       "data":{
                        "id":' . $id . '}
                       }';
            } else {
                echo '{"msg":"保存失败",
                     "success":false,
                       "data":{
                        "id":' . $id . '}
                       }';
            }
        }
    }

    public function jsondetailsaveAction()
    {
        $this->view->title = '保存';
        $detail = array();
        if($this->getRequest()->isPost()) {
            $_id = intval($this->getParam('id', ''));
            $detail['gid'] = $this->getParam('gid');
            $detail['gname'] = $this->getParam('gname');
            $detail['equipment_type'] = $this->getParam('equipment_type');
            $detail['equipment_id'] = $this->getParam('equipment_id');
            $detail['equipment_name'] = $this->getParam('equipment_name');
            $detail['description'] = $this->getParam('description');
            if($_id == 0) {
                $id = $this->groupDetailObj->addGroupDetail($detail);
            } else {
                $detail['id'] = $_id;
                $this->groupDetailObj->updateGroupDetail($detail);
                $id = $_id;
            }

            if($id > 0) {
                echo '{"msg":"保存成功","success":true,
                       "data":{
                        "id":' . $id . '}
                       }';
            } else {
                echo '{"msg":"保存失败",
                     "success":false,
                       "data":{
                        "id":' . $id . '}
                       }';
            }
        }
    }

}