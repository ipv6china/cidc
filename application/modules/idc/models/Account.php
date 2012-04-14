<?php

/**
 * Description of Acount
 *
 * @author y109
 */
class Idc_Model_Account extends Etao_Model_Base
{

    protected $_name = 'account';
    protected $_primary = 'id';
    protected $_fileds = array(
        'id',
        'username',
        'machine_id',
        'type',
        'rtx',
        'realname',
        'status'
    );
    /**
     * 状态
     */
    const STATE_NOMAL = 0;
    const STATE_LOCKED = 1;

    public $STATE = array(
        self::STATE_NOMAL => '正常',
        self::STATE_LOCKED => '锁定',
    );
    /**
     * 类型
     */
    const TYPE_MACHINE = 0;
    const TYPE_SVN = 1;

    public $TYPE = array(
        self::TYPE_MACHINE => '机器',
        self::TYPE_SVN => 'SVN',
    );

    /**
     * 添加
     * @param type $data
     * @return type
     */
    public function addAccount($data)
    {
        $neworkInterface = array();
        $neworkInterface['username'] = $data['username'];
        $neworkInterface['machine_id'] = $data['machine_id'];
        $neworkInterface['type'] = isset($data['type']) ? $data['type'] : self::TYPE_MACHINE;
        $neworkInterface['rtx'] = isset($data['rtx']) ? $data['rtx'] : $data['username'];
        $neworkInterface['realname'] = isset($data['realname']) ? $data['realname'] : '';
        $neworkInterface['status'] = isset($data['status']) ? $data['status'] : self::STATE_NOMAL;

        $account = $this->isExist(array(
            'machine_id' => $neworkInterface['machine_id']
            , 'type' => $neworkInterface['type']
            , 'username' => $neworkInterface['username']
                ));
        if($account) {
            $id = $account['id'];
            $this->update($neworkInterface, 'id = "' . $account['id'] . '"');
        } else {
            $id = $this->add($neworkInterface);
        }
        return $id;
    }

    /**
     * 判断是否已经存在
     * @param array $data
     * @return int id
     */
    public function isExist($data)
    {
        return $this->fetchRow(
                        array(
                            'machine_id=?' => $data['machine_id']
                            , 'type=?' => $data['type']
                            , 'username=?' => $data['username']
                        )
        );
    }

    /**
     * 删除不在$account中列出的记录
     */
    public function deleteOldData($machineId, $accounts, $type = self::TYPE_MACHINE)
    {
        $_accounts = $this->fetchAll(array(
            'machine_id=?' => $machineId
            , 'type=?' => $type));
        $dbAccounts = array();
        foreach($_accounts as $key => $a)
        {
            $dbAccounts[] = $a['id'];
        }
        $delAccount = array_diff($dbAccounts, $accounts);
        foreach($delAccount as $del_id)
        {
            $this->delete(array('id=?' => $del_id));
        }
        return count($delAccount);
    }

}