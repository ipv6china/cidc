<div class="mainContent">
    <div class="pageTitle">
        <{$this->title}>
    </div>
    <div class="etao_form">
        <form method="post">
            <ul>
                <li><span class="title">类型：</span><input name="type" type="text" /></li>
                <li><span class="title">名称：</span><input name="name" type="text" /></li>
                <li><span class="title">存放地点：</span><input name="place" type="text" /></li>
                <li><span class="title">高度：</span><input name="height" type="text" /></li>
                <li><span class="title">使用部门：</span><input name="admin_dept" type="text" /></li>
                <li><span class="title">负责人：</span><input name="admin_realname" type="text" />
                    <input name="admin_username" type="hidden" />
                </li>
                <li><span class="title">制造商：</span><input name="manufacturer" type="text" /></li>
                <li><span class="title">产品型号：</span><input name="product_name" type="text" /></li>
                <li><span class="title">网络类型：</span><input name="network_type" type="text" /></li>
                <li><span class="title">管理 IP：</span><input name="ip" type="text" /></li>
                <li><span class="title">所在机柜：</span><input name="cabinet_id" type="text" /></li>
                <li><span class="title">位置：</span><input name="cabinet_position" type="text" /></li>
                <li><span class="title">设备描述：</span>
                    <textarea name="description"></textarea>
                </li>
                <li><span class="title"></span><input type="submit" /></li>
            </ul>
        </form>
    </div>
</div>