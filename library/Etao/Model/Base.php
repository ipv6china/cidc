<?php

class Etao_Model_Base extends Zend_Db_Table
{

    protected $db;

    public function __construct()
    {
        $config = Zend_Registry::get('config');
        $dbprefix = $config->resources->db->params->prefix;
        $this->_name = $dbprefix . $this->_name;
        $this->db = $this->getAdapter();
        parent::__construct();
    }

    /**
     * 根据条件统计表内总数据
     */
    public function getTotal($where=1)
    {
        try
        {
            $db = $this->getAdapter();
            $select = $db->select();
            $select->from($this->_name, 'count(' . $this->_primary . ') as total')
                    ->where($where);
            $sql = $select->__toString();//生成SQL语句
            $nums = $db->query($sql)->fetchColumn();
            return $nums;
        } catch(Exception $e)
        {
            return false;
        }
    }

    /**
     * 增加一条数据
     * @param array $data
     */
    public function add(array $data)
    {
        try
        {
            $result = $this->createRow($data);
            $id = $result->save();
            return $id;
        } catch(Exception $e)
        {
            return false;
        }
    }

    /**
     * 修改对应数据
     * @param array $data
     * @param int $id
     */
    public function edit(array $data, $where)
    {
        try
        {
            $condition = $this->select()->where($where);
            $result = $this->fetchRow($condition);
            $result->setFromArray($data);
            $id = $result->save();
            return $id;
        } catch(Exception $e)
        {

            return false;
        }
    }

    /**
     * 通过ID数组进行,批量修改
     * @param array $data
     * @param array $ids
     */
    public function editByArray(array $data, array $ids)
    {

        $string = join(",", $ids);
        $where = $this->_primary . "  IN ( $string )";
        try
        {
            $result = $this->update($data, $where);
            return true;
        } catch(Exception $e)
        {
            return false;
        }
    }

    /**
     * 批量删除
     * @param array $ids
     */
    public function delByArray(array $ids)
    {

        $string = join(",", $ids);
        $where = $this->_primary . "  IN ( $string )";
        try
        {
            $this->delete($where);
            return true;
        } catch(Exception $e)
        {
            return false;
        }
    }

    /**
     * 删除其中一条数据
     * Enter description here ...
     * @param unknown_type $id
     */
    public function delByID($id)
    {
        $pr = $this->_primary;
        if(is_array($this->_primary)) {
            $pr = $this->_primary[1];
        }
        $where = $pr . '=' . $id;
        try
        {
            $this->delete($where);
            return true;
        } catch(Exception $e)
        {
            return false;
        }
    }

    /**
     * 载入项目
     * @return array
     */
    public function load($id)
    {
        $obj = $this->fetchRow('id = "' . $id . '"');
        return $obj;
    }

    /**
     * 获取Select对象
     * @return type
     */
    public function getSelect()
    {

        $select = $this->getAdapter()->select();
        //$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter ();
        // $select = $dbAdapter->select();
        return $select;
    }

    /**
     * 查找
     */
    public function findById($id)
    {
        return $this->fetchRow('id = "' . $id . '"');
    }

    /**
     * 根据fieldname,value查找某行
     * @param string $field
     * @param string $value
     * @return Record
     */
    public function findByFieldAndValue($field, $value)
    {
        return $this->fetchRow($field . ' = "' . $value . '"');
    }

    public function queryByFieldAndValue($field='id', $value=1, $c='=')
    {
        $select = $this->getSelect();
        $select->from($this->_name, '*');
        $select->where($field . ' ' . $c . ' "' . $value . '"');
        //打印SQL语句
        //$sql = $select->__toString();var_dump($sql);exit;
        return $this->getAdapter()->fetchAll($select, array());
    }

    /**
     * 查询
     * @param type $select
     * @return type
     */
    public function queryAll($select=null, $params='')
    {
        if(!$select) {
            $select = $this->getSelect();
        }
        $db = $this->getAdapter();
        $select->from($this->_name, '*');
        //打印SQL语句
        //$sql = $select->__toString(); var_dump($sql); exit;
        return $db->fetchAll($select, $params);
    }

    /**
     * 添加
     * @param type $data
     * @return type
     */
    public function addObj($data)
    {
        return $this->add($data);
    }

    /**
     * 更新
     * @param type $data
     * @return type
     */
    public function updateObj($data)
    {
        return $this->update($data, $this->_primary . ' = "' . $data[$this->_primary] . '"');
    }

}