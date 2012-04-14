<?php

class IpController extends App_CommonController
{

    protected $ipObj;

    public function init()
    {
        parent::init();
        $this->ipObj = new Idc_Model_Ip();
    }

    public function indexAction()
    {
        $url = $this->getUrl('index', 'cabinet', 'idc', array());
        $this->_redirect($url);
    }

    public function jsonipgraphicAction()
    {
        $address = '';
        $network = $this->getParam('network', '');
        $netmask = $this->getParam('netmask', '');
        $ipList = array();
        if(strlen($network) > 0 && strlen($netmask) > 0) {
            $address = $network . '/' . $netmask;
            $ipList = $this->ipObj->getIpGraphic($address);
        }
        if(count($ipList) > 0) {
            echo json_encode($ipList);
        } else {
            echo '[]';
        }
    }

    public function jsonallipsAction()
    {
        $address = '';
        $network = $this->getParam('network', '');
        $netmask = $this->getParam('netmask', '');
        if(strlen($network) > 0 && strlen($netmask) > 0) {
            $address = $network . '/' . $netmask;
        }

        $ipList = $this->ipObj->getAll($address);
        $data = array();
        foreach($ipList as $e)
        {
            $e = (array)$e;
            $data[] = $e;
        }
        $list = new Etao_Ext_GridList();
        $list->setStart(0);
        $list->setLimit(count($ipList));
        $list->setTotal(count($ipList));
        $list->setRows($data);

        echo $list->toJson();
    }

}

