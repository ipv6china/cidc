<div class="mainContent">
    <div class="pageTitle">
        <{$this->title}>
    </div>
    <div class="equipmentInfo">
        <ul>
            <li><span class="title">MachineID</span><span class="body"><{$equipment->machine_id}></span></li>
            <li><span class="title">Hostname</span><span class="body"><{$equipment->name}></span></li>
            <li><span class="title">机柜</span><span class="body"><{$equipment->cabinet_name}></span></li>
            <li><span class="title">系统帐号</span><span class="body"><{$equipment->accounts}></span></li>
            <li><span class="title">操作系统</span><span class="body"><{$equipment->os}></span></li>
            <li><span class="title">内核版本</span><span class="body"><{$equipment->version}> <{$equipment->plateform}></span></li>
            <li><span class="title">制造商</span><span class="body"><{$equipment->manufacturer}></span></li>
            <li><span class="title">产品名称</span><span class="body"><{$equipment->product_name}></span></li>
            <li><span class="title">商品序列号</span><span class="body"><{$equipment->serial_number}></span></li>
            <li><span class="title">CPU</span><span class="body"><{$equipment->cpu}></span></li>
            <li><span class="title">内存</span><span class="body"><{$equipment->memory}></span></li>
            <li><span class="title">硬盘</span><span class="body"><{$equipment->harddisk}></span></li>
            <li><span class="title">状态</span><span class="body"><{if 0 == $equipment->status}>正常<{else}>错误<{/if}></span></li>
            <li><span class="title">网卡</span><span class="body"><{$equipment->network_interfaces}></span></li>
            <li><span class="title">系统帐号</span>
                <div class="body">
                    <{foreach item="account" from=$equipment->systemAccounts}>
                        <{$account->username}> |
                    <{/foreach}>
                </div>
            </li>
            <li><span class="title">网络接口</span>
                <div class="body">
                    <{foreach item="interface" from=$equipment->networkInterfaces}>
                        <{$interface->id}> | <{$interface->interface}> | <{$interface->speed}> | <{$interface->ip}> | <{$interface->route}> </br>
                    <{/foreach}>
                </div>
            </li>
            <li><span class="title">IP Address</span>
                <div class="body">
                    <{foreach item="ip" from=$equipment->ips}>
                        <{$ip->id}> | <{$ip->ip_string}> | <{$ip->netmask_string}> <a href="#" onclick='runPutty("<{$ip->ip_string}>")'>Login</a></br>
                    <{/foreach}>
                </div>
            </li>
        </ul>
    </div>
    <div style="clear:both;"></div>
</div>