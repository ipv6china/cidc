<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Tree
 *
 * @author y109
 */
class Etao_Ext_Tree extends stdClass
{

    public static function arrayToExtTree($arr, $fid, $fparent = 'parent_id', $fchildrens = 'childrens', $returnReferences = false)
    {
        $tree =  self::arrayToTree($arr, $fid, $fparent, $fchildrens, $returnReferences);
        self::tidyTree($tree);
        return $tree;
    }

    /**
     * 数组转换为树
     * @param type $arr
     * @param type $fid
     * @param type $fparent
     * @param type $fchildrens
     * @param type $returnReferences
     * @return type
     */
    public static function arrayToTree($arr, $fid, $fparent = 'parent_id', $fchildrens = 'childrens', $returnReferences = false)
    {
        $pkvRefs = array();
        foreach($arr as $offset => $row)
        {
            $pkvRefs[$row[$fid]] = & $arr[$offset];
        }
        $tree = array();
        foreach($arr as $offset => $row)
        {
            $parentId = $row[$fparent];
            if($parentId) {
                if(!isset($pkvRefs[$parentId])) {
                    continue;
                }
                $parent = & $pkvRefs[$parentId];//1
                $parent[$fchildrens][] = & $arr[$offset];   //2
            } else {
                $tree[] = & $arr[$offset];
            }
        }
        if($returnReferences) {
            return array('tree' => $tree, 'refs' => $pkvRefs);
        } else {
            return $tree;
        }
    }

    /**
     * 整理树给树加上 Expand 属性
     * @param type $tree
     */
    public static function tidyTree(&$tree)
    {
        foreach($tree as &$value)
        {
            if(is_array($value) && key_exists('children', $value)) {
                $value['expanded'] = true;
                self::tidyTree($value['children']);
            } else {
                $value['leaf'] = true;
            }
        }
    }

}