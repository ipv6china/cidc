<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tree
 *
 * @author y109
 */
class Etao_Common_Tree
{

    public function tree ($table, $p_id='00000000-0000-0000-0000-000000000000')
    {
        $tree = array ();
        foreach ($table as $row)
        {
            if ($row['parent_id'] == $p_id) {
                $tmp = $this->tree ($table, $row['id']);
                if ($tmp) {
                    $row['children'] = $tmp;
                } else {
                    $row['leaf'] = true;
                }
                $tree[] = $row;
            }
        }
        Return $tree;
    }

    /**
     * 将一个平面的二维数组按照指定的字段转换为树状结构
     *
     * 用法：
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1', 'parent' => 0),
     *     array('id' => 2, 'value' => '2-1', 'parent' => 0),
     *     array('id' => 3, 'value' => '3-1', 'parent' => 0),
     *
     *     array('id' => 7, 'value' => '2-1-1', 'parent' => 2),
     *     array('id' => 8, 'value' => '2-1-2', 'parent' => 2),
     *     array('id' => 9, 'value' => '3-1-1', 'parent' => 3),
     *     array('id' => 10, 'value' => '3-1-1-1', 'parent' => 9),
     * );
     *
     * $tree = Helper_Array::tree($rows, 'id', 'parent', 'nodes');
     *
     * dump($tree);
     *   // 输出结果为：
     *   // array(
     *   //   array('id' => 1, ..., 'nodes' => array()),
     *   //   array('id' => 2, ..., 'nodes' => array(
     *   //        array(..., 'parent' => 2, 'nodes' => array()),
     *   //        array(..., 'parent' => 2, 'nodes' => array()),
     *   //   ),
     *   //   array('id' => 3, ..., 'nodes' => array(
     *   //        array('id' => 9, ..., 'parent' => 3, 'nodes' => array(
     *   //             array(..., , 'parent' => 9, 'nodes' => array(),
     *   //        ),
     *   //   ),
     *   // )
     * @endcode
     *
     * 如果要获得任意节点为根的子树，可以使用 $refs 参数：
     * @code php
     * $refs = null;
     * $tree = Helper_Array::tree($rows, 'id', 'parent', 'nodes', $refs);
     *
     * // 输出 id 为 3 的节点及其所有子节点
     * $id = 3;
     * dump($refs[$id]);
     * @endcode
     *
     * @param array $arr 数据源
     * @param string $key_node_id 节点ID字段名
     * @param string $key_parent_id 节点父ID字段名
     * @param string $key_childrens 保存子节点的字段名
     * @param boolean $refs 是否在返回结果中包含节点引用
     *
     * return array 树形结构的数组
     */
    public static function arrayToTree($arr, $key_node_id, $key_parent_id = 'parent_id',
                           $key_childrens = 'childrens', & $refs = null)
    {
        $refs = array();
        foreach ($arr as $offset => $row)
        {
            $arr[$offset][$key_childrens] = array();
            $refs[$row[$key_node_id]] =& $arr[$offset];
        }

        $tree = array();
        foreach ($arr as $offset => $row)
        {
            $parent_id = $row[$key_parent_id];
            if ($parent_id)
            {
                if (!isset($refs[$parent_id]))
                {
                    $tree[] =& $arr[$offset];
                    continue;
                }
                $parent =& $refs[$parent_id];
                $parent[$key_childrens][] =& $arr[$offset];
            }
            else
            {
                $tree[] =& $arr[$offset];
            }
        }

        return $tree;
    }

}

