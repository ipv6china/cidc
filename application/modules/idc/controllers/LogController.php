<?php

class LogController extends App_CommonController
{

    protected $logObj;

    public function init()
    {
        parent::init();
        $this->logObj = new Idc_Model_Log();
        $this->view->assign('title', '操作日志');
    }

    public function indexAction()
    {

    }

    public function jsonlistAction()
    {
        $limit = $this->getParam('limit') ? $this->getParam('limit') : 25;
        $start = $this->getParam('start') ? $this->getParam('start') : 0;
        $page = $this->getParam('page') ? $this->getParam('page') : 1;

        $select = $this->logObj->getSelect();
        $conditions = array();
        $_name = $this->getParam('equipment_name');
        if(strlen($_name) > 0) {
            $conditions['equipment_name'] = '%' . $_name . '%';
            $select->where('equipment_name LIKE :equipment_name');
        }
        $_issystem = $this->getParam('issystem');
        if(strlen($_issystem) > 0) {
            $conditions['issystem'] = $_issystem;
            $select->where('issystem = :issystem');
        }
        date_default_timezone_set('Asia/Shanghai');
        $_time_start = $this->getParam('log_time_start');
        if(strlen($_time_start) > 0) {
            $conditions['time_start'] = strtotime($_time_start);
            $select->where('log_time >= :time_start');
        }
        $_time_end = $this->getParam('log_time_end');
        if(strlen($_time_end) > 0) {
            $conditions['time_end'] = strtotime($_time_end);
            $select->where('log_time <= :time_end');
        }
        $_sort = $this->getParam('sort');
        if(strlen($_sort) > 0) {
            $sort = json_decode($_sort);
            foreach($sort as $s)
            {
                $select->order($s->property . ' ' . $s->direction);
            }
        }

        $select->order('id DESC');
        $list = $this->logObj->queryAll($select, $conditions);
        $paginator = Zend_Paginator::factory($list);
        $paginator->setCurrentPageNumber($page);
        //每页条数
        $paginator->setItemCountPerPage($limit);
        $data = $paginator->getCurrentItems()->getArrayCopy();
        $list = new Etao_Ext_GridList();
        $list->setStart($start);
        $list->setLimit($limit);
        $list->setTotal($paginator->getTotalItemCount());
        $list->setRows($data);
        echo json_encode($list);
    }

    public function jsonallAction()
    {
        $etype = $this->getParam('equipment_type');
        $eid = $this->getParam('equipment_id');
        if(strlen($etype) > 0 && strlen($eid) > 0) {
            $list = $this->logObj->getLogsByEquipmentTypeAndEquipmentId($etype, $eid);
        } else {
            $list = $this->logObj->queryAll();
        }
        $result = Etao_Ext_GridList::getSimleListFromArray($list);
        echo json_encode($result);
    }

    public function jsonsaveAction()
    {
        $this->view->title = '保存';

        $log = array();
        if($this->getRequest()->isPost()) {
            $_id = intval($this->getParam('id', ''));
            $log['type'] = $this->getParam('type');

            if(strlen($this->getParam('username')) > 0) {
                $log['username'] = $this->getParam('username');
            } else {
                $log['username'] = $this->currentUser->username;
            }
            if(strlen($this->getParam('realname')) > 0) {
                $log['realname'] = $this->getParam('realname');
            } else {

                $log['realname'] = $this->currentUser->realname;
            }
            $log['message'] = $this->getParam('message');
            //date_default_timezone_set('UTC');
            date_default_timezone_set('Asia/Shanghai');
            $time = $this->getParam('log_time_date') . ' ' . $this->getParam('log_time_time') . ':00';
            $log['log_time'] = strtotime($time);
            $log['issystem'] = $this->getParam('issystem');
            if(0 == intval($log['issystem'])) {
                $log['priority'] = LOG_USER;
            }
            $log['equipment_type'] = $this->getParam('equipment_type');
            $log['equipment_id'] = $this->getParam('equipment_id');
            $log['equipment_name'] = $this->getParam('equipment_name');
            if($_id == 0) {
                $id = $this->logObj->addLog($log);
            } else {
                $log['id'] = $_id;
                $this->logObj->updateLog($log);
                $id = $_id;
            }

            if($id > 0) {
                echo '{"msg":"保存 日志 成功 "
                    ,"success":true,
                       "data":{
                        "id":' . $id . '}
                       }';
            } else {
                echo '{"msg":"保存 日志 失败",
                     "success":false}';
            }
        }
    }

    public function jsondelAction()
    {
        $id = $this->getParam('id');
        if($id > 0) {
            $this->logObj->delById($id);
            echo '删除成功';
        }
    }

}

