<div class="mainContent">
    <div class="pageTitle">
        <{$this->title}>
    </div>
    <div class="etao_form">
        <pre>
        <form method="post">
            <ul>
                <li><span class="title">设备类型：</span><select name="equipment_type">
                            <{foreach key="key" item="t" from=$equipmentType}>
                            <option value="<{$key}>"><{$t}></option>
                            <{/foreach}>
                    </select>
                </li>
                <li><span class="title">设备ID：</span><select name="equipment_id">
                            <{foreach key="key" item="e" from=$equipmentList}>
                            <option value="<{$e->id}>"><{$e->name}></option>
                            <{/foreach}>
                    </select>
                </li>
                <li><span class="title">机柜ID：</span><input name="cabinet_id" type="text" value="<{$cabinet_id}>"/></li>
                <li><span class="title">位置：</span><input name="position" type="text" value="<{$position}>"/></li>
                <li><span class="title"></span><input type="submit" /></li>
            </ul>
        </form>
    </div>
</div>