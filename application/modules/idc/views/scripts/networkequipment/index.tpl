<div class="mainContent">
    <div class="pageTitle">
        <{$this->title}>
    </div>
    <div class="subMenu">
        <div class="menu">
            <ul>
                <li><a href="<{$this->baseUrl('idc/networkequipment/add')}>">添加</a></li>
            </ul>
        </div>
        <div class="serverFilter">
            <form method="post" action="<{$this->baseUrl('idc/machine/index')}>">
                <ul>
                    <li><span>机器ID：</span><input type="text" name="machine_id" /></li>
                    <li><span>主机名：</span><input type="text" name="hostname" /></li>
                    <li><span>IP：</span><input type="text" name="ip" /></li>
                    <li><span></span><input type="submit" value="搜索" /></li>
                </ul>
            </form>
        </div>
    </div>
    <div class="clear"></div>
    <div class="serverList">
        <div class="floatLeft padding6" style="width:160px">设备名称</div>
        <div class="floatLeft padding6" style="width:200px">帐号</div>
        <div class="floatLeft padding6" style="width:160px">网卡</div>
        <div class="clear"></div>
        <{foreach key="key" item="e" from=$equipments}>
            <div class="floatLeft padding6" style="width:160px">
                <a href="<{$this->baseUrl('idc/networkequipment/info')}>/id/<{$e->id}>"><{$e->name}></a>
            </div>
            <div class="floatLeft padding6" style="width:200px;word-wrap: break-word; word-break:nomal;"><{$machine->accounts}></div>
            <div class="floatLeft padding6" style="width:160px"><{$machine->network_interfaces}></div>
            <div class="clear"></div>
        <{/foreach}>
        <div class="clear"></div>
        <div class="paginator"><{$this->paginationControl($this->paginator,'Sliding','common/pagination_control_item.tpl')}></div>
        <div class="clear"></div>
    </div>
</div>