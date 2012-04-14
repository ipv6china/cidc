<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * UserController
 *
 * @author y109
 */
class User_UserController extends App_CommonController
{

    protected $userObj;

    public function init()
    {
        parent::init();
        $this->userObj = new User_Model_User();
        $this->view->assign('title', '用户管理');
    }

    public function indexAction()
    {

    }

    /*
     * The default action - show the home page
     */

    public function index0Action()
    {
        $this->isAllow('allow_admin_user');
        $this->view->assign('title', '用户管理');
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $users = $this->_user_obj->fetchAll();
        $paginator = Zend_Paginator::factory($users);
        //每页条数
        $paginator->setItemCountPerPage(10);

        //获得当前页，设置当前页数
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
        $this->view->paginator = $paginator;
    }

    public function jsonallAction()
    {
        $userList = $this->userObj->queryAll();
        $result = Etao_Ext_GridList::getSimleListFromArray($userList);
        echo json_encode($result);
    }

    public function jsonlistAction()
    {
        $limit = $this->getParam('limit') ? $this->getParam('limit') : 25;
        $start = $this->getParam('start') ? $this->getParam('start') : 0;
        $page = $this->getParam('page') ? $this->getParam('page') : 1;

        $select = $this->userObj->getSelect();
        $conditions = array();
        $_name = $this->getParam('username');
        if(strlen($_name) > 0) {
            $conditions['username'] = '%' . $_name . '%';
            $select->where('username LIKE :username');
        }

        $_sort = $this->getParam('sort');
        if(strlen($_sort) > 0) {
            $sort = json_decode($_sort);
            foreach($sort as $s)
            {
                $select->order($s->property . ' ' . $s->direction);
            }
        }

        $select->order('uid DESC');
        $list = $this->userObj->queryAll($select, $conditions);
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


    public function deleteAction()
    {
        $this->isAllow('allow_admin_user');
        $user_obj = new Model_User();
        $this->view->title = '删除用户';
        $uid = $this->_getParam('uid', 0);
        $del = $this->_getParam('delete');
        if($this->getRequest()->isPost()) {
            if($del == 'Yes') {
                $user_obj->delete('uid = "' . $uid . '"');
                $this->addMessage('删除成功');
            }
            $this->_helper->redirector('index');
        } else {
            $this->view->user = $user_obj->load($uid);
        }
    }

    public function editAction()
    {
        $this->isAllow('allow_admin_user');
        $this->view->title = '编辑';
        $uid = intval($this->_getParam('uid'));
        $request = $this->getRequest();
        //$form    = new Application_Form_User();
        if($this->getRequest()->isPost()) {
            $user = array();
            $user['email'] = $this->_getParam('email');
            $user['rtx'] = $this->_getParam('rtx');
            $user['mobilephone'] = $this->_getParam('mobilephone');
            $user['qq'] = $this->_getParam('qq');
            $user['status'] = $this->_getParam('status', 0);
            $user['issuperadmin'] = $this->_getParam('issuperadmin', 0);
            $user['allow_admin_task'] = $this->_getParam('allow_admin_task', 0);
            $user['allow_admin_router'] = $this->_getParam('allow_admin_router', 0);
            $user['allow_admin_user'] = $this->_getParam('allow_admin_user', 0);
            $user['allow_admin_log'] = $this->_getParam('allow_admin_log', 0);
            $user['allow_admin_setting'] = $this->_getParam('allow_admin_setting', 0);

            $obj = new Model_User();
            $obj->update($user, 'uid = ' . $uid);
            $this->addMessage('保存成功');
            $this->_helper->redirector('index');
        } else {
            $users = new User_Model_User();
            $user = $users->fetchRow('uid = ' . $uid);
            $this->view->user = $user;
            $this->render('/form');
        }
    }

}