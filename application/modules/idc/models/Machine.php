<?php

/*
 * (C)2001-2011 Comsenz Inc.
 * This is NOT a freeware, use is subject to license terms
 * GavinYao <Yaojungnag@comsenz.com>
 */

/**
 * Machine
 */
class Idc_Model_Machine extends Etao_Model_Base
{

    protected $_name = 'machine';
    protected $_primary = 'id';
    protected $_fileds = array(
        'id',
        'machine_id',
        'name',
        'host_machine',
        'cpu',
        'memory',
        'harddisk',
        'serial_number',
        'manufacturer',
        'product_name',
        'os',
        'plateform',
        'version',
        'accounts',
        'network_interfaces',
        'admin_dept',
        'admin_username',
        'admin_realname',
        'place',
        'cabinet_id',
        'cabinet_name',
        'cabinet_position',
        'status'
    );

    /**
     * 网卡
     * @var List MachineNetwork
     */
    protected $networkInterfaces;

    /**
     * ip
     * @var List Ip
     */
    protected $ips;

    /**
     * 帐号
     * @var List Account
     */
    protected $systemAccounts;
    private $accountObj;
    private $networkInterfaceObj;
    private $ipObj;
    private $cabinetDetailObj;
    private $groupDetailObj;

    public function __construct()
    {
        parent::__construct();
        $this->accountObj = new Idc_Model_Account();
        $this->networkInterfaceObj = new Idc_Model_MachineNetworkInterface();
        $this->ipObj = new Idc_Model_Ip();
        $this->cabinetDetailObj = new Idc_Model_CabinetDetail();
        $this->groupDetailObj = new Idc_Model_GroupDetail();
    }

    public function addLog($message, $equipmentId, $equipmentName=null)
    {
        Idc_Model_Log::log(array('message' => $message,
            'priority' => LOG_INFO,
            'equipment_type' => Idc_Model_Equipment::TYPE_MACHINE,
            'equipment_id' => $equipmentId,
            'equipment_name' => $equipmentName,
            'type' => Idc_Model_Log::TYPE_SYSTEM,
            'issystem' => true
        ));
    }

    public function addMachine($data)
    {
        $id = $this->add($data);
        $this->addLog('添加服务器', $id, $data['name']);
        return $id;
    }

    /**
     * 更新
     * @param type $data
     * @return type
     */
    public function updateMachine($data)
    {
        //更新 cabinet_detail 表
        $this->cabinetDetailObj->updateMachine($data);
        //更新 IP 表
        $this->ipObj->updateMachine($data);
        //更新 group_detail 表
        $this->groupDetailObj->updateMachine($data);
        $arr_old = $this->findById($data['id'])->toArray();
        $updateLog = Etao_Common_Util::arrayUpdateToString($data, $arr_old);
        $updateLog && $this->addLog('修改服务器:' . $updateLog, $data['id'], $arr_old['name']);
        return $this->update($data, 'id = "' . $data['id'] . '"');
    }

    public function importMachine($data)
    {
        $ma = array();
        $ma['type'] = Idc_Model_Equipment::TYPE_MACHINE;
        $ma['machine_id'] = trim($data->machineId);
        $ma['name'] = trim($data->hostname);
        $ma['host_machine'] = trim($data->hostMachine);
        $ma['status'] = intval($data->status);
        $ma['cpu'] = trim($data->cpu);
        $ma['memory'] = trim($data->memory);
        $ma['harddisk'] = trim($data->hardDisk);
        $ma['serial_number'] = trim($data->serialNumber);
        $ma['product_name'] = trim($data->productName);
        $ma['manufacturer'] = trim($data->manufacturer);
        $ma['os'] = trim($data->os);
        $ma['plateform'] = trim($data->plateform);
        $ma['version'] = trim($data->version);
        $device_tag = trim($data->device_tag);
        $device_tag = substr($device_tag, -1) == '-' ? substr($device_tag, 0, -1) : $device_tag;
        $device_tag = str_replace('-', ',', $device_tag);
        $ma['device_tag'] = $device_tag;
        $ma['accounts'] = implode(',', $data->account);
        $_network_interfaces = '';

        $_machine = self::isExist(array('machine_id' => $ma['machine_id']));
        if($_machine) {
            $id = $_machine['id'];
            $ma['id'] = $id;
            $this->updateMachine($ma);
        } else {
            $id = $this->addMachine($ma);
        }


        //帐号
        $_accountForClean = array();
        foreach($data->account as $account)
        {
            $_accountForClean[] = $this->accountObj->addAccount(array('machine_id' => $ma['machine_id'], 'username' => trim($account)));
        }
        //删除多余的帐号
        $this->accountObj->deleteOldData($ma['machine_id'], $_accountForClean);

        //网卡
        $_interfaceForClean = array();
        $_ipAds = array();
        foreach($data->networkInterface as $networkInterface)
        {

            $ni['machine_id'] = trim($ma['machine_id']);
            $ni['interface'] = trim($networkInterface->interface);
            $ni['speed'] = trim($networkInterface->speed);
            $ni['mac'] = trim($networkInterface->mac);
            $ni['ip'] = implode(',', $networkInterface->ip);
            $ni['route'] = implode(',', $networkInterface->route);
            $_network_interfaces .= $ni['interface'] . ' ' . $ni['mac'] . ' ' . $ni['ip'] . ',';
            $_interfaceForClean[] = $this->networkInterfaceObj->addNetworkInterface($ni);

            //ip
            $ipdata = $ma;
            $ipdata['id'] = $id;
            $ipdata['interface'] = $ni['interface'];

            foreach($networkInterface->ip as $ip)
            {
                if(strpos($ip, '/') > 0) {
                    $_ipAds[] = $this->ipObj->addIp(trim($ip), $ipdata);
                }
            }
        }
        //删除多余的网卡
        $this->networkInterfaceObj->deleteOldData($ma['machine_id'], $_interfaceForClean);
        //删除多余的IP
        $this->ipObj->deleteOldData($ma['machine_id'], $_ipAds);
        $ma['network_interfaces'] = substr($_network_interfaces, 0, -1);
        $ma['id'] = $id;
        $this->updateMachine($ma);

        return $id;
    }

    public function export($id)
    {
        $machineObj = $this->findMachinebyId($id);
        $ma = array();
        $ma['id'] = trim($machineObj->id);
        $ma['machineId'] = trim($machineObj->machine_id);
        $ma['type'] = Idc_Model_Equipment::TYPE_MACHINE;
        $ma['hostname'] = trim($machineObj->name);
        $ma['hostMachine'] = trim($machineObj->host_machine);
        $ma['status'] = intval($machineObj->status);
        $ma['cpu'] = trim($machineObj->cpu);
        $ma['memory'] = trim($machineObj->memory);
        $ma['hardDisk'] = trim($machineObj->harddisk);
        $ma['serialNumber'] = trim($machineObj->serial_number);
        $ma['productName'] = trim($machineObj->product_name);
        $ma['manufacturer'] = trim($machineObj->manufacturer);
        $ma['os'] = trim($machineObj->os);
        $ma['plateform'] = trim($machineObj->plateform);
        $ma['version'] = trim($machineObj->version);
        $ma['device_tag'] = $machineObj->device_tag;
        $_accounts = array();
        foreach($machineObj->systemAccounts as $account)
        {
            $_accounts[] = $account->username;
        }
        $ma['accounts'] = $_accounts;
        $_networkInterface = array();
        foreach($machineObj->networkInterfaces as $ni)
        {
            $_networkInterface[] = array(
                'interface' => $ni->interface,
                'speed' => $ni->speed,
                'mac' => $ni->mac,
                'route' => array($ni->route),
                'ip' => array($ni->ip)
            );
        }
        $ma['networkInterface'] = $_networkInterface;
        return json_encode($ma);
    }

    /**
     * 判断是否已经存在
     * @param array $data
     * @return int id
     */
    public function isExist($data)
    {
        return $this->fetchRow('machine_id = "' . $data['machine_id'] . '"');
    }

    /**
     * 根据 machineId 返回对象
     * @param string $machineId
     * @return Machine
     */
    public function findByMachineId($machineId)
    {
        return $this->fetchRow('machine_id = "' . $machineId . '"');
    }

    /**
     * 根据主机名 返回对象
     * @param string $hostname
     */
    public function findByHostname($hostname)
    {
        return $this->fetchRow('hostname = "' . $hostname . '"');
    }

    /**
     * 获取 machine 信息，并拼装 networkinterface & ip
     * @param type $id
     * @return type
     */
    public function findMachinebyId($id)
    {
        $machine = $this->findById($id);
        if($machine) {
            $machine = $machine->toArray();
            $machine['networkInterfaces'] = $this->networkInterfaceObj->queryByFieldAndValue('machine_id', $machine['machine_id']);
            $machine['systemAccounts'] = $this->accountObj->queryByFieldAndValue('machine_id', $machine['machine_id']);
            $machine['ips'] = $this->ipObj->queryByFieldAndValue('machine_id', $machine['machine_id']);
            $machine['groups'] = $this->groupDetailObj->getByEquipmentTypeAndEquipmentId(Idc_Model_Equipment::TYPE_MACHINE, $machine['id']);
            return (object)$machine;
        } else {
            return null;
        }
    }

    public function findMachinesByIds($ids)
    {
        $select = $this->select();
        $select->where('id IN(?)', $ids);
        $r = $this->fetchAll($select)->toArray();
        return $r;
    }

}