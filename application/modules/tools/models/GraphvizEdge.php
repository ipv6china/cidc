<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * edge
 *
 * @author y109
 */
class Tools_Model_GraphvizEdge extends Etao_Model_Base
{

    protected $_name = 'graphviz_edge';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'gid',
        'type',
        'node1',
        'node2',
        'attrs'
    );
    /**
     * 类型
     */
    const TYPE_ONE2ONE = 1;
    const TYPE_ONE2MANY = 2;
    const TYPE_LINK = 3;

    public $TYPE = array(
        self::TYPE_ONE2ONE => '一对一',
        self::TYPE_ONE2MANY => '一对多',
        self::TYPE_LINK => '链式'
    );
    private $nodeObj;

    public function __construct()
    {
        parent::__construct();
        $this->nodeObj = new Tools_Model_GraphvizNode();
    }

    /**
     * 添加
     * @param type $data
     * @return type
     */
    public function addGraphvizEdge($data)
    {
        if(isset($data['gid']) && isset($data['node1']) && isset($data['node2'])) {
            isset($data['node1']) && $this->nodeObj->addNodeByName($data['gid'], $data['node1']);
            switch((int)$data['type'])
            {
                case self::TYPE_ONE2ONE:
                    $this->nodeObj->addNodeByName($data['gid'], $data['node2']);
                    break;
                case self::TYPE_ONE2MANY:
                case self::TYPE_LINK:
                    $nodes = explode(',', $data['node2']);
                    foreach($nodes as $value)
                    {
                        $this->nodeObj->addNodeByName($data['gid'], $value);
                    }
                    break;
                default:
                    break;
            }
            return $this->add($data);
        }
    }

    /**
     * 修改
     * @param type $data
     * @return type
     */
    public function updateGraphvizEdge($data)
    {
        //@todo 修改节点表
        $oldData = $this->findById($data['id']);
        if(isset($data['node1']) && $data['node1'] != $oldData['node1']) {
            $this->nodeObj->deleteNodeByName($data['gid'], $oldData['node1']);
            $this->nodeObj->addNodeByName($data['gid'], $data['node1']);
        }
        if(isset($data['node2']) && $data['node2'] != $oldData['node2']) {
            switch((int)$data['type'])
            {
                case self::TYPE_ONE2ONE:
                    $this->nodeObj->deleteNodeByName($data['gid'], $oldData['node2']);
                    $this->nodeObj->addNodeByName($data['gid'], $data['node2']);
                    break;
                case self::TYPE_ONE2MANY:
                case self::TYPE_LINK:
                    $oldNodes = explode(',', $oldData['node2']);
                    $nodes = explode(',', $data['node2']);
                    $delArray = array_diff($oldNodes, $nodes);
                    $addArray = array_diff($nodes, $oldNodes);
                    foreach($delArray as $value)
                    {
                        $this->nodeObj->deleteNodeByName($data['gid'], $value);
                    }
                    foreach($addArray as $value)
                    {
                        $this->nodeObj->addNodeByName($data['gid'], $value);
                    }
                    break;
                default:
                    break;
            }
        }
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    /**
     * 删除
     * @param type $id
     * @return type
     */
    public function deleteGraphvizEdge($id)
    {
        $data = $this->getById($id);
        if($data) {
            $this->nodeObj->deleteNodeByName($data['gid'], $data['node1']);
            switch((int)$data['type'])
            {
                case self::TYPE_ONE2ONE:
                    $this->nodeObj->deleteNodeByName($data['gid'], $data['node2']);
                    break;
                case self::TYPE_ONE2MANY:
                case self::TYPE_LINK:
                    $nodes = explode(',', $data['node2']);
                    foreach($nodes as $value)
                    {
                        $this->nodeObj->deleteNodeByName($data['gid'], $value);
                    }
                    break;
                default:
                    break;
            }
        }
        return $this->delByID($id);
    }

    /**
     * 根据名称获取对象
     * @param type $id
     * @return type
     */
    public function getById($id)
    {
        return $this->findById($id);
    }

    /**
     * 根据名称查询对象
     * @param type $name
     * @return type
     */
    public function findByName($name)
    {
        return $this->queryByFieldAndValue('name', $name);
    }

    /**
     * 根据 Gid 查询对象
     * @param type $gid
     * @return type
     */
    public function findByGid($gid)
    {
        return $this->queryByFieldAndValue('gid', $gid);
    }

    /**
     * 根据 Gid 删除对象
     * @param type $gid
     * @return type
     */
    public function deleteByGid($gid)
    {
        return $this->delete('gid = "' . $gid . '"');
    }

}