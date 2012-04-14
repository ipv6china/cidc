<?php

class Tools_GraphvizController extends Etao_Controller_Action
{

    private $graphObj;
    private $nodeObj;
    private $edgeObj;

    public function init()
    {
        parent::init();
        $this->view->assign('title', '工具类');
        $this->graphObj = new Tools_Model_GraphvizGraph();
        $this->nodeObj = new Tools_Model_GraphvizNode();
        $this->edgeObj = new Tools_Model_GraphvizEdge();
    }

    public function indexAction()
    {
        $this->view->assign('title', 'Grahpviz 工具 beta');
    }

    public function jsongraphlistAction()
    {
        $limit = $this->getParam('limit') ? $this->getParam('limit') : 25;
        $start = $this->getParam('start') ? $this->getParam('start') : 0;
        $page = $this->getParam('page') ? $this->getParam('page') : 1;

        $select = $this->graphObj->getSelect();
        $conditions = array();
        $_name = $this->getParam('name');
        if(strlen($_name) > 0) {
            $conditions['name'] = '%' . $_name . '%';
            $select->where('name LIKE :name');
        }
        $_sort = $this->getParam('sort');
        if(strlen($_sort) > 0) {
            $sort = json_decode($_sort);
            foreach($sort as $s)
            {
                $select->order($s->property . ' ' . $s->direction);
            }
        }

        $select->order('id ASC');
        $list = $this->graphObj->queryAll($select, $conditions);
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

    /**
     * 保存
     */
    public function jsongraphsaveAction()
    {
        $this->view->title = '保存';
        $data = array();
        if($this->getRequest()->isPost()) {
            $_id = $this->getParam('id', 0);
            $this->_hasParam('name') && $data['name'] = $this->getParam('name');
            $this->_hasParam('label') && $data['label'] = $this->getParam('label');
            $this->_hasParam('type') && $data['type'] = $this->getParam('type');
            $this->_hasParam('directed') && $data['directed'] = $this->getParam('directed');
            $this->_hasParam('file_format') && $data['file_format'] = $this->getParam('file_format');
            $this->_hasParam('strict') && $data['strict'] = $this->getParam('strict');
            $this->_hasParam('attrs') && $data['attrs'] = $this->getParam('attrs');
            $this->_hasParam('advanced') && $data['advanced'] = $this->getParam('advanced');
            $this->_hasParam('code') && $data['code'] = $this->getParam('code');

            if($_id == 0) {
                $id = $this->graphObj->addGraphvizGraph($data);
            } else {
                $id = $data['id'] = $_id;
                $this->graphObj->updateGraphvizGraph($data);
            }

            if($id > 0) {
                echo '{"msg":"保存成功","success":true,"data":{"id":' . $id . '}}';
            } else {
                echo '{"msg":"保存失败","success":false,"data":{"id":' . $id . '}}';
            }
        }
    }

    /**
     * 保存
     */
    public function jsongraphsaveattrAction()
    {
        $this->view->title = '保存';
        $data = array();
        if($this->getRequest()->isPost()) {
            $_id = $this->getParam('id', 0);
            $data['attrs'] = $this->getParam('attrs');
            $id = $data['id'] = $_id;
            $this->graphObj->updateGraphvizGraph($data);

            if($id > 0) {
                echo '{"msg":"保存成功","success":true,"data":{"id":' . $id . '}}';
            } else {
                echo '{"msg":"保存失败","success":false,"data":{"id":' . $id . '}}';
            }
        }
    }

    public function jsongraphdelAction()
    {
        $id = $this->getParam('id');
        if($id > 0) {
            $this->graphObj->deleteGraphvizGraph($id);
            echo '删除成功';
        }
    }

    public function jsonnodelistAction()
    {
        $limit = $this->getParam('limit') ? $this->getParam('limit') : 25;
        $start = $this->getParam('start') ? $this->getParam('start') : 0;
        $page = $this->getParam('page') ? $this->getParam('page') : 1;

        $select = $this->nodeObj->getSelect();
        $conditions = array();
        $_gid = $this->getParam('gid');
        if(strlen($_gid) > 0) {
            $conditions['gid'] = '%' . $_gid . '%';
            $select->where('gid LIKE :gid');
        }
        $_name = $this->getParam('name');
        if(strlen($_name) > 0) {
            $conditions['name'] = '%' . $_name . '%';
            $select->where('name LIKE :name');
        }
        $_sort = $this->getParam('sort');
        if(strlen($_sort) > 0) {
            $sort = json_decode($_sort);
            foreach($sort as $s)
            {
                $select->order($s->property . ' ' . $s->direction);
            }
        }

        $select->order('id ASC');
        $list = $this->nodeObj->queryAll($select, $conditions);
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

    /**
     * 保存
     */
    public function jsonnodesaveAction()
    {
        $this->view->title = '保存';
        $data = array();
        if($this->getRequest()->isPost()) {
            $_id = $this->getParam('id', 0);
            $data['gid'] = $this->getParam('gid');
            $data['name'] = $this->getParam('name');
            $data['label'] = $this->getParam('label');
            $data['attrs'] = $this->getParam('attrs');

            if($_id == 0) {
                $id = $this->nodeObj->addGraphvizNode($data);
            } else {
                $data['id'] = $_id;
                $id = $this->nodeObj->updateGraphvizNode($data);
            }

            if($id > 0) {
                echo '{"msg":"保存成功","success":true,"data":{"id":' . $id . '}}';
            } else {
                echo '{"msg":"保存失败","success":false,"data":{"id":' . $id . '}}';
            }
        }
    }

    /**
     * 保存
     */
    public function jsonnodesaveattrAction()
    {
        $this->view->title = '保存';
        $data = array();
        if($this->getRequest()->isPost()) {
            $_id = $this->getParam('id', 0);
            $data['attrs'] = $this->getParam('attrs');
            $id = $data['id'] = $_id;
            $this->nodeObj->updateGraphvizNode($data);

            if($id > 0) {
                echo '{"msg":"保存成功","success":true,"data":{"id":' . $id . '}}';
            } else {
                echo '{"msg":"保存失败","success":false,"data":{"id":' . $id . '}}';
            }
        }
    }

    public function jsonnodedelAction()
    {
        $id = $this->getParam('id');
        if($id > 0) {
            $this->nodeObj->deleteGraphvizNode($id);
            echo '删除成功';
        }
    }

    public function jsonedgelistAction()
    {
        $limit = $this->getParam('limit') ? $this->getParam('limit') : 25;
        $start = $this->getParam('start') ? $this->getParam('start') : 0;
        $page = $this->getParam('page') ? $this->getParam('page') : 1;

        $select = $this->edgeObj->getSelect();
        $conditions = array();
        $_gid = $this->getParam('gid');
        if(strlen($_gid) > 0) {
            $conditions['gid'] = '%' . $_gid . '%';
            $select->where('gid LIKE :gid');
        }
        $_name = $this->getParam('name');
        if(strlen($_name) > 0) {
            $conditions['name'] = '%' . $_name . '%';
            $select->where('name LIKE :name');
        }
        $_sort = $this->getParam('sort');
        if(strlen($_sort) > 0) {
            $sort = json_decode($_sort);
            foreach($sort as $s)
            {
                $select->order($s->property . ' ' . $s->direction);
            }
        }

        $select->order('id ASC');
        $list = $this->edgeObj->queryAll($select, $conditions);
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

    /**
     * 保存
     */
    public function jsonedgesaveAction()
    {
        $this->view->title = '保存';
        $data = array();
        if($this->getRequest()->isPost()) {
            $_id = $this->getParam('id', 0);
            $data['gid'] = $this->getParam('gid');
            $data['type'] = $this->getParam('type');
            $data['node1'] = $this->getParam('node1');
            $data['node2'] = $this->getParam('node2');
            $data['label'] = $this->getParam('label');
            $data['attrs'] = $this->getParam('attrs');

            if($_id == 0) {
                $id = $this->edgeObj->addGraphvizEdge($data);
            } else {
                $data['id'] = $_id;
                $id = $this->edgeObj->updateGraphvizEdge($data);
            }

            if($id > 0) {
                echo '{"msg":"保存成功","success":true,"data":{"id":' . $id . '}}';
            } else {
                echo '{"msg":"保存失败","success":false,"data":{"id":' . $id . '}}';
            }
        }
    }

    /**
     * 保存
     */
    public function jsonedgesaveattrAction()
    {
        $this->view->title = '保存';
        $data = array();
        if($this->getRequest()->isPost()) {
            $_id = $this->getParam('id', 0);
            $data['attrs'] = $this->getParam('attrs');
            $id = $data['id'] = $_id;
            $this->edgeObj->updateGraphvizEdge($data);

            if($id > 0) {
                echo '{"msg":"保存成功","success":true,"data":{"id":' . $id . '}}';
            } else {
                echo '{"msg":"保存失败","success":false,"data":{"id":' . $id . '}}';
            }
        }
    }

    public function jsonedgedelAction()
    {
        $id = $this->getParam('id');
        if($id > 0) {
            $this->edgeObj->deleteGraphvizEdge($id);
            echo '删除成功';
        }
    }

    /**
     * 获取代码
     */
    public function codeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_id = $this->getParam('id', 0);
        if((int)$_id > 0) {
            echo nl2br($this->graphObj->getCode($_id));
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
            $this->graphObj->getImage($_id);
        }
    }

}

