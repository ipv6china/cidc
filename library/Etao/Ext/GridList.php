<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * GridList
 *
 * @author y109
 */
class Etao_Ext_GridList extends stdClass
{

    public $total;
    public $totalCount;
    public $start;
    public $limit;
    public $rows;

    public function toJson()
    {
        return json_encode($this);
    }

    public function setTotal($total)
    {
        $this->total = $total;
        $this->totalCount = $total;
    }

    public function setStart($start)
    {
        $this->start = $start;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    public static function getSimleListFromArray($array)
    {

        $data = array();
        foreach($array as $e)
        {
            $e = (array)$e;
            $data[] = $e;
        }
        $list = new self();
        $list->setStart(0);
        $list->setLimit(count($array));
        $list->setTotal(count($array));
        $list->setRows($data);

        return $list;
    }

}