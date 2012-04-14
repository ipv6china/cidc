<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * nod
 *
 * @author y109
 */
class Tools_Model_GraphvizNode extends Etao_Model_Base
{

    protected $_name = 'graphviz_node';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'gid',
        'name',
        'label',
        'usecount',
        'attrs'
    );

    /**
     * 添加
     * @param type $data
     * @return type
     */
    public function addGraphvizNode($data)
    {
        return $this->add($data);
    }

    /**
     * 修改
     * @param type $data
     * @return type
     */
    public function updateGraphvizNode($data)
    {
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    /**
     * 删除
     * @param type $id
     * @return type
     */
    public function deleteGraphvizNode($id)
    {
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

    /**
     *
     * @param type $gid
     * @param type $name
     * @return type
     */
    public function findByGidAndName($gid, $name)
    {
        $select = $this->select();
        $select->where('gid = ?', $gid);
        $select->where('name = ?', $name);
        $r = $this->fetchAll($select);
        if(count($r) > 0) {
            return $r[0];
        }
    }

    /**
     * 用名称添加节点，已存在同名节点则 count+1
     * @param type $name
     */
    public function addNodeByName($gid, $name)
    {
        $node = $this->findByGidAndName($gid, $name);
        if($node) {
            $this->updateGraphvizNode(array('id' => $node->id, 'use_count' => ((int)$node->use_count + 1)));
        } else {
            $this->addGraphvizNode(array(
                'gid' => $gid,
                'name' => $name,
                'use_count' => 1
            ));
        }
    }

    /**
     * 用名称删除节点，count - 1
     * @param type $name
     */
    public function deleteNodeByName($gid, $name)
    {
        $node = $this->findByGidAndName($gid, $name);
        if($node) {
            $use_count = ((int)$node->use_count - 1);
            if($use_count > 0) {
                $this->updateGraphvizNode(array('id' => $node->id, 'use_count' => $use_count));
            } else {
                $this->deleteGraphvizNode($node->id);
            }
        }
    }

}