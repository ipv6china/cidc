<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Idc 机房资料
 *
 * @author y109
 */
class Idc_Model_Idc extends Etao_Model_Base
{
    protected $_name = 'idc';
    protected $_primary = 'id';
    protected $_fields = array(
        'id',
        'name',
        'address',
        'contact',
        'tel',
        'description'
    );

    /**
     * 添加
     * @param type $data
     * @return type
     */
    public function addIdc($data)
    {
        return $this->add($data);
    }

    /**
     * 更新
     * @param type $data
     * @return type
     */
    public function updateIdc($data)
    {
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }
}