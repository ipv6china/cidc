<div class="mainContent">
    <div class="pageTitle">
        <{$this->title}>
    </div>
    <div class="etao_form">
        <form method="post">
            <ul>
                <li><span class="title">名称：</span><input name="name" type="text" />
                </li>
                <li><span class="title">存放地点：</span><input name="place" type="text" /></li>
                <li><span class="title">可用高度：</span><input name="height" type="text" /></li>
                <li><span class="title">已使用高度：</span><input name="height_used" type="text" /></li>
                <li><span class="title">使用部门：</span><input name="admin_dept" type="text" /></li>
                <li><span class="title">负责人：</span><input name="admin_realname" type="text" />
                    <input name="admin_username" type="hidden" />
                </li>
                <li><span class="title">设备数量：</span><input name="equitment_amount" type="text" /></li>
                <li><span class="title">机器数量：</span><input name="machine_amount" type="text" /></li>
                <li><span class="title">设备描述：</span>
                    <textarea name="description"></textarea>
                </li>
                <li><span class="title"></span><input type="submit" /></li>
            </ul>
        </form>
    </div>
</div>