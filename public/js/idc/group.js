Ext.onReady(function(){
    Ext.QuickTips.init();
    Ext.ns('Idc','Idc.group');
    /****************************************************************************************/
    Idc.group.GroupStore = Ext.create('Ext.data.Store',{
        fields: ['id','name','parent','description'],
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: '/idc/group/jsonlist',
            reader:{
                type:'json',
                root:'rows'
            }
        }
    });
    /****************************************************************************************/
    Idc.group.GroupDetailStore = Ext.create('Ext.data.Store',{
        fields: ['id','gid','gname','equipment_type','equipment_id','equipment_name','description'],
        autoLoad:false,
        proxy: {
            type: 'ajax',
            url: '/idc/group/jsondetaillist',
            reader:{
                type:'json',
                root:'rows'
            }
        },
        listeners:{
            load:function(){
                var c = Idc.group.GroupDetailStore.getTotalCount();
                if(c>0){
                    //默认选中id=1的节点
                    Idc.group.GroupDetailPanel.getSelectionModel().select(0);
                    Idc.group.GroupDetailPanel.fireEvent('rowclick', Idc.group.GroupDetailPanel, 0);
                }
            }
        }
    });
    /****************************************************************************************/
    Idc.group.AllEquipmentStore = Ext.create('Ext.data.Store',{
        fields: ['id',
        'name',
        'cabinet_id',
        'cabinet_name',
        'equipment_name',
        'equipment_id',
        'equipment_type',
        'equipment_height',
        'device_tag',
        'position'
        ],
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: '/idc/equipment/jsonallequipments',
            reader:{
                type:'json',
                root:'rows'
            }
        }
    });
    /****************************************************************************************/
    Idc.group.AllIpStore = Ext.create('Ext.data.Store',{
        fields: ['id','equipment_id','name', 'equipment_type','machine_id','interface','ip','ip_string','netmask','netmask_string'],
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: '/idc/ip/jsonallips',
            reader:{
                type:'json',
                root:'rows'
            }
        }
    });
    /****************************************************************************************/
    Idc.group.GroupTreePanel = Ext.create('Ext.tree.Panel',{
        title : '分组',
        useArrows: false,
        rootVisible: true,
        collapsible:true,
        singleExpand:true,
        region:'center',
        border:0,
        store: {
            xtype:'store',
            proxy: {
                type: 'ajax',
                url: '/idc/group/jsontree'
            },
            folderSort: true
        },
        multiSelect: false,
        root: {
            text: 'ComsenzIDC',
            id: '0',
            expanded: true
        },
        listeners:{
            itemclick: function(view, record, item, index, e)
            {
                //点击节点时记录下节点和 index
                Idc.group.currentGroupTreeNode = record;
                Idc.group.currentGroupTreeNodeIndex = index;

                //选择节点时改变右侧Grid的内容
                Idc.group.currentGroupId = record.data.id;
                Ext.apply(Idc.group.GroupDetailStore.proxy.extraParams,{
                    groupId:Idc.group.currentGroupId
                });
                Idc.group.GroupDetailStore.load();

                var groupRecord = Idc.group.GroupStore.findRecord('id',Idc.group.currentGroupId);
                if(groupRecord){
                    var groupData = groupRecord.data;
                    Idc.group.GroupInfoPanel.update(groupData.description);
                }

            },
            load:function(){
                //选中上次选中的节点，默认选中第一个节点
                var id,record;
                var treepanel = Idc.group.GroupTreePanel;
                var root = treepanel.getStore().getRootNode();
                if(Idc.group.currentGroupTreeNode){
                    id = Idc.group.currentGroupTreeNode.data.id;
                    record = root.findChild('id',id,true);
                }else{
                    record = root;
                }
                treepanel.collapseAll();
                treepanel.expandPath(record.getPath());
                treepanel.getSelectionModel().select(record);
                treepanel.fireEvent('itemclick', this,record);
            },
            itemdblclick:function(view,record,item,index,event,options){
                if(!record.data.root){
                    var form = Idc.group.GroupFormWindow.down('form');
                    Idc.group.GroupFormWindow.setTitle('修改域名');
                    Idc.group.GroupFormWindow.down('button[text="删除"]').show();
                    Idc.group.GroupStore.load();
                    var domainzoneData = Idc.group.GroupStore.findRecord('id',record.data.id);
                    var tdata;
                    if(domainzoneData){
                        tdata = domainzoneData.data;
                    }
                    form.getForm().setValues(tdata);
                    Idc.group.GroupFormWindow.show();
                }
            }
        },
        tbar:[{
            text:'添加',
            iconCls: 'icon-add',
            handler:function(){
                Idc.group.GroupFormWinShow();
                Idc.group.GroupFormWindow.setTitle('添加分组');
                Idc.group.GroupFormWindow.down('button[text="删除"]').hide();
                Idc.group.GroupFormWindow.down('form').getForm().reset();

                var record = Idc.group.GroupTreePanel.getSelectionModel().getSelection()[0];
                var data = record.data;
                var groupId;
                if(data.root){
                    groupId = 0;
                }else{
                    groupId = data.id;
                }

                var form = Idc.group.GroupFormWindow.down('form');
                form.getForm().setValues({
                    parent:groupId
                });
                Idc.group.GroupFormWindow.show();
            }
        },'->',{
            tooltip:'刷新',
            iconCls: 'icon-refresh',
            handler:function(btn){
                var tree = btn.up('treepanel'),store;
                store = tree.getStore();
                store.load();
                Etao.msg.info('刷新成功','重新载入 '+ store.getNewRecords().length + ' 条数据');
            }
        }]

    });
    /****************************************************************************************/
    Idc.group.GroupInfoPanel = Ext.create('Ext.panel.Panel',{
        region: 'south',
        title:'备注',
        height:200,
        autoScroll: true,
        collapsible:true,
        border:0,
        bodyPadding:5,
        bodyStyle: 'background:#fafafa;',
        html:''
    });
    /****************************************************************************************/
    //左侧导航
    Idc.group.WestPanel = Ext.create('Ext.panel.Panel',{
        layout: 'border',
        region:'west',
        width:200,
        minWidth:100,
        maxWidth:550,
        items:[Idc.group.GroupTreePanel,Idc.group.GroupInfoPanel]
    });
    /****************************************************************************************/
    Idc.group.GroupFormWinShow = function(){
        if('undefined' == Idc.group.currentGroupId){
            alert('请先选择一个节点');
        }
        if(!Idc.group.GroupFormWindow.isVisible()){
            Idc.group.GroupFormWindow.show();
        }else{
            Idc.group.GroupFormWindow.hide();
        }
    };
    /****************************************************************************************/
    Idc.group.GroupFormWindow = Ext.create('Ext.window.Window',{
        title : '分组表单',
        layout: 'fit',
        autoShow: false,
        width: 580,
        closeAction:'hide',
        items:[{
            xtype: 'form',
            padding: '5 5 0 5',
            border: false,
            style: 'background-color: #fff;',
            defaults:{
                labelWidth:50,
                xtype: 'textfield',
                anchor:'-20'
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            },
            {
                name : 'name',
                fieldLabel: '名称'
            },{
                name : 'parent',
                fieldLabel: '父节点',
                xtype: 'treecombobox',
                rootText:'Comsenz',
                treeUrl:'/idc/group/jsontree',
                folderSelectAble:true,
                rootVisible:true,
                valueField: 'id',
                displayField: 'text'
            },
            {
                xtype: 'htmleditor',
                name : 'description',
                fieldLabel: '描述'
            }
            ]
        }],
        buttons : [{
            text:'删除',
            handler:function(button){
                var node = Idc.group.GroupTreePanel.getSelectionModel().getSelection()[0];
                if(!node.isLeaf()){
                    Ext.Msg.alert('错误','含有子元素的节点不允许删除');
                }else{
                    var win = button.up('window');
                    Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                        if('yes' == btn){
                            Ext.Ajax.request({
                                method:'GET',
                                url: '/idc/group/jsondel',
                                params:{
                                    'id':node.data.id
                                },
                                success: function(response) {
                                    Etao.msg.info('success',response.responseText);
                                    node.remove();
                                }
                            });
                            //删除之后选中第一个节点
                            var index = 2;
                            Idc.group.GroupTreePanel.getSelectionModel().select(index);
                            var root = Idc.group.GroupTreePanel.getStore().getRootNode();
                            var record = root.findChild('id',index,true);

                            Idc.group.GroupTreePanel.getSelectionModel().select(record);
                            Idc.group.GroupTreePanel.fireEvent('itemclick', this,record);
                            win.close();
                        }
                    });
                }
            }
        },{
            text: '保存',
            handler:function(button){
                var win    = button.up('window'),
                form   = win.down('form'),
                record = form.getRecord(),
                values = form.getValues();
                if (form.getForm().isValid()) {
                    form.getForm().submit({
                        url: '/idc/group/jsonsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            Idc.group.GroupStore.load();
                            //刷新树
                            Idc.group.GroupTreePanel.getRootNode().removeAll();
                            Idc.group.GroupTreePanel.getStore().load();
                            Idc.group.GroupTreePanel.getView().refresh();
                            var ddata = form.getForm().getValues();
                            Idc.group.GroupInfoPanel.update(ddata.description);
                            form.getForm().reset();
                            win.hide();

                            win.close();
                        }
                    });
                }

            }
        },
        {
            text: '取消',
            handler: function(button){
                var win = button.up('window');
                win.close();
            }
        }
        ]
    });
    /****************************************************************************************/
    Idc.group.GroupDetailPanel = Ext.create('Ext.grid.Panel',{
        title:'设备',
        border:1,
        region:'center',
        cmargins:'5 0 0 0',
        store:Idc.group.GroupDetailStore,
        columns: [{
            xtype:'rownumberer'
        },{
            text: '记录',
            width:150,
            dataIndex: 'equipment_name'
        },{
            text: '描述',
            flex:1,
            dataIndex: 'description'
        }],
        listeners:{
            selectionchange:function(view,selections,options){
                if(selections.length > 0){
                    //选择节点时记录当前ID
                    Idc.group.currentGroupDetailId = selections[0].data.id;
                    var data = selections[0].data;
                    equipmentTpl =Ext.create('Ext.XTemplate',
                        '<p><b>类型:</b> {typeString}</p>',
                        '<p><b>名称:</b> <a href="/idc/equipment/info/type/{equipment_type}/id/{equipment_id}" target="_blank">{name}</a></p>',
                        '<p><b>ID:</b> {equipment_id}</p>'
                        );
                    switch(parseInt(data.equipment_type)){
                        case 0:
                            var machineData;
                            Ext.Ajax.request({
                                url: '/idc/machine/jsoninfo/id/'+data.equipment_id,
                                success: function(response) {
                                    machineData = Ext.JSON.decode(response.responseText);
                                    machineData.typeString = '服务器';
                                    machineTpl =Ext.create('Ext.XTemplate',
                                        '<div style="line-height:150%;">',
                                        '<table class="ext-dataTable">',
                                        '<tr><td style="width:65px;">Hostname</td><td> <a href="/idc/equipment/info/type/{type}/id/{id}" target="_blank">{name}</a></td></tr>',
                                        '<tr><td>MachineID</td><td> {machine_id}</td></tr>',
                                        '<tr><td>设备标签</td><td> {device_tag}</td></tr>',
                                        '<tr><td>状态</td><td> {status}</td></tr>',
                                        '<tr><td>系统帐号</td><td> {accounts}</td></tr>',
                                        '<tr><td>操作系统</td><td> {os} {plateform}</td></tr>',
                                        '<tr><td>内核</td><td> {version}</td></tr>',
                                        '<tr><td>制造商</td><td> {manufacturer}</td></tr>',
                                        '<tr><td>产品型号</td><td> {product_name}</td></tr>',
                                        '<tr><td>序列号</td><td> {serial_number}</td></tr>',
                                        '<tr><td>CPU</td><td> {cpu}</td></tr>',
                                        '<tr><td>内存</td><td> {memory}</td></tr>',
                                        '<tr><td>硬盘</td><td> {harddisk}</td></tr>',
                                        '<tr><td>所在机柜</td><td> {cabinet_name}</td></tr>',
                                        '</table>',
                                        '<p><b>IP:</b> </p>',
                                        '<table class="ext-dataTable">',
                                        '<tr><td>登录</td><td>接口</td><td>IP</td><td>Netmask</td></tr>',
                                        '<tpl for="ips">',
                                        '<tr><td><a href="#" onclick=\'runPutty("{ip_string}")\'>登录</a></td><td>{interface}</td><td>{ip_string}</td>',
                                        '<td>{netmask_string}</td></tr>',
                                        '</tpl>',
                                        '</table>',
                                        '<table>',
                                        '<p><b>网卡:</b></p>',
                                        '<table class="ext-dataTable">',
                                        '<tr><td style="width:35px;">接口</td><td style="width:65px;">速率</td><td style="width:110px;">MAC</td><td>路由表</td></tr>',
                                        '<tpl for="networkInterfaces">',
                                        '<tr><td>{interface}</td><td>{speed}</td><td>{mac}</td><td>{route}</td></tr>',
                                        '</tpl>',
                                        '</table>',
                                        '<p><b>分组:</b></p>',
                                        '<table class="ext-dataTable">',
                                        '<tpl for="groups">',
                                        '<tr><td>{gname}</td></tr>',
                                        '</tpl>',
                                        '</table>',
                                        '</div>'
                                        );
                                    machineTpl.overwrite(Idc.group.EquipmentInfoPanel.body,machineData);
                                }
                            });
                            break;
                        case 1:
                            data.typeString='机柜';
                            equipmentTpl.overwrite(Idc.group.EquipmentInfoPanel.body,data);
                            break;
                        case 2:
                            var networkEquipmentData;
                            Ext.Ajax.request({
                                url: '/idc/networkequipment/jsoninfo/id/'+data.equipment_id,
                                success: function(response) {
                                    networkEquipmentData = Ext.JSON.decode(response.responseText);
                                    networkEquipmentData.typeString = '服务器';
                                    networkEquipmentTpl =Ext.create('Ext.XTemplate',
                                        '<div style="line-height:150%;">',
                                        '<table class="ext-dataTable">',
                                        '<tr><td>设备名称</td><td> <a href="/idc/equipment/info/type/{type}/id/{id}" target="_blank">{name}</a></td></tr>',
                                        '<tr><td>设备高度</td><td> {height}</td></tr>',
                                        '<tr><td>使用部门</td><td> {admin_dept}</td></tr>',
                                        '<tr><td>负责人</td><td> {admin_realname}({admin_username})</td></tr>',
                                        '<tr><td>制造商</td><td> {manufacturer}</td></tr>',
                                        '<tr><td>产品型号</td><td> {product_name}</td></tr>',
                                        '<tr><td>网络类型</td><td> {network_type}</td></tr>',
                                        '<tr><td>所在机柜</td><td> {cabinet_name}</td></tr>',
                                        '<tr><td>管理</td><td> {ip}</td></tr>',
                                        '</table>',
                                        '</div>'
                                        );
                                    networkEquipmentTpl.overwrite(Idc.group.EquipmentInfoPanel.body,networkEquipmentData);
                                }
                            });
                            break;
                        default:
                            Idc.group.EquipmentInfoPanel.update('未知的设备类型');
                            break;
                    }
                    Idc.group.EquipmentInfoPanel.doComponentLayout();
                }
            },
            itemdblclick:function(view,record,item,index,e,options){
                var trecord = Idc.group.GroupTreePanel.getSelectionModel().getSelection()[0];
                var tdata = trecord.data;
                var gname = tdata.text;

                var form = Idc.group.GroupDetailFormWindow.down('form');
                form.getForm().setValues(record.data);
                form.getForm().setValues({
                    gname:gname
                });

                Idc.group.GroupDetailFormWindow.down('button[text="删除"]').show();
                Idc.group.GroupDetailFormWindow.show();

            },
            'render':function(grid,options){
                grid.getView().on('render', function(view) {
                    view.tip = Ext.create('Ext.tip.ToolTip', {
                        autoHide:true,
                        closable:true,
                        anchor:'top',
                        title:'设备信息',
                        mouseOffset:[10,-10],
                        width:230,
                        // The overall target element.
                        target: view.el,
                        // Each grid row causes its own seperate show and hide.
                        delegate: view.itemSelector,
                        // Moving within the row should not hide the tip.
                        trackMouse: false,
                        // Render immediately so that tip.body can be referenced prior to the first show.
                        renderTo: Ext.getBody(),
                        dismissDelay: 15000,
                        listeners: {
                            // Change content dynamically depending on which element triggered the show.
                            beforeshow: function updateTipBody(tip) {
                                var record = view.getRecord(tip.triggerElement);
                                var tipData = '设备名称：' + record.get('name') + '<br />'
                                + '设备类型：' + record.get('equipment_type') + '<br />';
                                Ext.Ajax.request({
                                    //url: '/idc/equipment/info/type/'+record.get('equipment_type')+'/id/'+record.get('equipment_id'),
                                    url:'/idc/machine/jsoninfo/id/'+record.get('equipment_id'),
                                    success: function (response) {
                                        //Ext.Msg.alert('success',response.responseText);
                                        var machine = Ext.JSON.decode(response.responseText);
                                        var ipData = '';
                                        for(var i=0;i < machine.ips.length;i++){
                                            ipData += '  <a href="#" onClick="runPutty(\''+machine.ips[i].ip_string+'\')">'+machine.ips[i].ip_string + ' SSH登录</a><br />';
                                        }
                                        tipData = tipData + '<br />'
                                        +'<b>设备 IP </b><br />'+ ipData;

                                        tip.update(tipData);
                                    }
                                });
                            //tip.update(tipData);
                            }
                        }
                    });
                });
            }
        },
        tbar:[{
            text:'添加',
            handler:function(){
                if(Idc.group.SelectEquipmentWindow.isVisible()){
                    Idc.group.SelectEquipmentWindow.hide();
                }else{
                    Idc.group.SelectEquipmentWindow.show();
                }
            }
        },'->',{
            text:'刷新',
            handler:function(btn){
                var grid = btn.up('gridpanel'),store;
                store = grid.getStore();
                store.load();
                Etao.msg.info('载入成功','共载入 '+store.getTotalCount()+ ' 条数据');
            }
        }]
    });
    /****************************************************************************************/
    Idc.group.GroupDetailFormWindow = Ext.create('Ext.window.Window',{
        title : '设备',
        layout: 'fit',
        autoShow: false,
        height:200,
        width: 580,
        closeAction:'hide',
        items:[{
            xtype: 'form',
            padding: '5 5 0 5',
            border: false,
            style: 'background-color: #fff;',
            layout: 'anchor',
            defaults:{
                labelWidth:50,
                xtype: 'textfield',
                anchor:'-20'
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            },{
                xtype: 'hiddenfield',
                name: 'gid'
            },{
                xtype: 'hiddenfield',
                name: 'gname'
            },{
                xtype: 'hiddenfield',
                name: 'equipment_id'
            },{
                name : 'equipment_name',
                xtype: 'hiddenfield',
                fieldLabel: '名称'
            },{
                xtype: 'textarea',
                name : 'description',
                fieldLabel: '描述'
            }
            ]
        }],
        buttons : [{
            text:'删除',
            handler:function(button){
                var win = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Idc.group.GroupDetailPanel.getSelectionModel().getSelection()[0];
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/idc/group/jsondetaildel',
                            params:{
                                'id':node.data.id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                            }
                        });
                        //删除之后选中第一个节点
                        Idc.group.GroupDetailStore.load();
                        Idc.group.GroupDetailPanel.getSelectionModel().select(0);
                        var record = Idc.group.GroupDetailPanel.getSelectionModel().getSelection()[0];
                        Idc.group.GroupDetailPanel.fireEvent('itemclick', '',record, 0);
                        win.close();
                    }
                });
            }
        },
        {
            text: '保存',
            handler:function(button){
                var win    = button.up('window'),
                form   = win.down('form'),
                record = form.getRecord(),
                values = form.getValues();
                if (form.getForm().isValid()) {
                    form.getForm().submit({
                        url: '/idc/group/jsondetailsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            win.hide();
                            Idc.group.GroupDetailStore.load();
                        }
                    });
                }
            }
        },
        {
            text: '取消',
            handler: function(button){
                var win = button.up('window');
                win.close();
            }
        }
        ]
    });
    /****************************************************************************************/
    Idc.group.SelectEquipmentWindow = Ext.create('Ext.window.Window',{
        title : '选择设备',
        layout: 'fit',
        autoShow: false,
        height:600,
        width: 680,
        closeAction:'hide',
        items:[{
            xtype:'gridpanel',
            columnLines: true,
            loadMask: true,
            stripeRows:true,
            border:0,
            selModel:  Ext.create('Ext.selection.CheckboxModel'),
            columns: [
            {
                text: "设备名称",
                flex: 1,
                dataIndex:'name'
            },{
                text: "设备标签",
                flex: 1,
                dataIndex:'device_tag'
            },
            {
                text: "类型",
                width: 135,
                sortable: true,
                dataIndex: 'type_string'
            }],
            store : Ext.create('Ext.data.Store',{
                autoLoad:true,
                fields: ['id','name', 'height','device_tag', 'equipment_type','equipment_id',{
                    name:'type_string',
                    convert:function(value,record){
                        switch(parseInt(record.get('equipment_type'))){
                            case 0:
                                return '服务器';
                                break;
                            case 1:
                                return '机柜';
                                break;
                            case 2:
                                return '网络设备';
                                break;
                            default:
                                return '未知类型';

                        }
                    }
                }],
                proxy: {
                    type: 'ajax',
                    url: '/idc/equipment/jsonallequipments',
                    reader:{
                        type:'json',
                        root:'rows'
                    }
                }
            }),
            listeners:{
                'render':function(grid,options){
                    grid.getView().on('render', function(view) {
                        view.tip = Ext.create('Ext.tip.ToolTip', {
                            autoHide:true,
                            closable:true,
                            anchor:'top',
                            title:'设备信息',
                            mouseOffset:[10,-10],
                            width:230,
                            // The overall target element.
                            target: view.el,
                            // Each grid row causes its own seperate show and hide.
                            delegate: view.itemSelector,
                            // Moving within the row should not hide the tip.
                            trackMouse: false,
                            // Render immediately so that tip.body can be referenced prior to the first show.
                            renderTo: Ext.getBody(),
                            dismissDelay: 15000,
                            listeners: {
                                // Change content dynamically depending on which element triggered the show.
                                beforeshow: function updateTipBody(tip) {
                                    var record = view.getRecord(tip.triggerElement);
                                    var tipData = '设备名称：' + record.get('name') + '<br />'
                                    + '设备类型：' + record.get('equipment_type') + '<br />';
                                    Ext.Ajax.request({
                                        //url: '/idc/equipment/info/type/'+record.get('equipment_type')+'/id/'+record.get('equipment_id'),
                                        url:'/idc/machine/jsoninfo/id/'+record.get('equipment_id'),
                                        success: function (response) {
                                            //Ext.Msg.alert('success',response.responseText);
                                            var machine = Ext.JSON.decode(response.responseText);
                                            var ipData = '';
                                            for(var i=0;i < machine.ips.length;i++){
                                                ipData += '  <a href="#" onClick="runPutty(\''+machine.ips[i].ip_string+'\')">'+machine.ips[i].ip_string + ' SSH登录</a><br />';
                                            }
                                            tipData = tipData + '<br />'
                                            +'<b>设备 IP </b><br />'+ ipData;

                                            tip.update(tipData);
                                        }
                                    });
                                //tip.update(tipData);
                                }
                            }
                        });
                    });
                }
            }
        }],
        tbar:['按主机名筛选：',{
            xtype:'combo',
            store: Idc.group.AllEquipmentStore,
            hideLabel: true,
            displayField: 'name',
            valueField:'name',
            typeAhead: false,
            queryMode: 'local',
            autoSelect:false,
            triggerAction: 'all',
            width: 135,
            enableKeyEvents:true,
            listeners:{
                specialkey:{
                    fn:function(field,e){
                        if (e.getKey() == e.ENTER) {
                            var panel = Idc.group.SelectEquipmentWindow.down('gridpanel');
                            var store = panel.getStore();
                            store.getProxy().url = '/idc/equipment/jsonallequipments/queryType/name/queryWord/'+field.getValue();
                            Etao.msg.info('Search Url', '/idc/equipment/jsonallequipments/queryType/name/queryWord/'+field.getValue());
                            store.load();
                        }
                    }
                }
            }
        },'-','按设备IP筛选：',{
            xtype:'combo',
            store: Idc.group.AllIpStore,
            hideLabel: true,
            displayField: 'ip_string',
            valueField:'ip_string',
            typeAhead: false,
            queryMode: 'local',
            autoSelect:false,
            triggerAction: 'all',
            width: 135,
            enableKeyEvents:true,
            listeners:{
                specialkey:{
                    fn:function(field,e){
                        if (e.getKey() == e.ENTER) {
                            var panel = Idc.group.SelectEquipmentWindow.down('gridpanel');
                            var store = panel.getStore();
                            store.getProxy().url = '/idc/equipment/jsonallequipments/queryType/ip/queryWord/'+field.getValue();
                            Etao.msg.info('Search Url', '/idc/equipment/jsonallequipments/queryType/ip/queryWord/'+field.getValue());
                            store.load();
                        }
                    }
                }
            }
        },'->',{
            text:'刷新',
            handler:function(btn){
                var win = btn.up('window'),
                grid = win.down('gridpanel');
                store = grid.getStore();
                store.load();
                Etao.msg.info('载入成功','共载入 '+store.getTotalCount()+ ' 条数据');
            }
        }],
        buttons:[{
            text:'加入',
            handler:function(btn){
                var win = btn.up('window'),
                grid = win.down('gridpanel');
                Ext.each(grid.getSelectionModel().getSelection(),function(rec){
                    var data = rec.data;
                    var isExist = false;
                    isExist = Ext.isEmpty(Idc.group.GroupDetailStore.findRecord('equipment_name',data.name))?false:true;
                    if(isExist){
                        Etao.msg.info('错误',data.name + '已存在于分组中');
                    }else{
                        var trecord = Idc.group.GroupTreePanel.getSelectionModel().getSelection()[0];
                        var tdata = trecord.data;
                        var gname = tdata.text;
                        Ext.Ajax.request({
                            method:'POST',
                            url: '/idc/group/jsondetailsave',
                            params:{
                                'gid':Idc.group.currentGroupId,
                                'gname':gname,
                                'equipment_type':data.equipment_type,
                                'equipment_id':data.equipment_id,
                                'equipment_name':data.name,
                                'description':data.description
                            },
                            success: function(response) {
                                result = Ext.decode(response.responseText);
                                Etao.msg.info(result.success?'成功':'错误',result.msg);
                                Idc.group.GroupDetailStore.load();
                            //win.hide();
                            }
                        });
                    }

                });
            //win.hide();
            }
        },{
            text:'取消',
            handler:function(btn){
                var win = btn.up('window');
                win.hide();
            }
        }]
    });
    /****************************************************************************************/
    //右侧显示部分，包括 机器信息，修改日志等
    Idc.group.EquipmentInfoPanel = Ext.create('Ext.panel.Panel',{
        title : '设备详情',
        region: 'east',
        collapsible:false,
        flex:1,
        border:1,
        bodyPadding:12,
        autoScroll: true,
        html:'设备信息',
        tbar:['->',{
            iconCls: 'icon-note_add',
            text:'添加日志',
            handler:function(){
                var selects = Idc.group.GroupDetailPanel.getSelectionModel().getSelection();
                if(selects.length > 0){
                    var equipmentRecord = Idc.group.GroupDetailPanel.getSelectionModel().getSelection()[0];
                    var equipment = equipmentRecord.data;
                    var form =  Idc.group.LogFormWindow.down('form');
                    form.getForm().reset();
                    form.getForm().setValues({
                        'type':3,
                        'issystem':false,
                        'equipment_type':equipment.equipment_type,
                        'equipment_id':equipment.equipment_id,
                        'equipment_name':equipment.equipment_name
                    });
                    Idc.group.LogFormWindow.down('button[text="保存"]').show();
                    Idc.group.LogFormWindow.down('button[text="取消"]').show();
                    Idc.group.LogFormWindow.show();
                }else{
                    alert('请先选择一个设备');
                }
            }
        }]
    });
    /****************************************************************************************/
    //日志Form
    Idc.group.LogFormWindow = Ext.create('Ext.window.Window',{
        title : '操作日志',
        layout: 'fit',
        autoShow: false,
        width: 580,
        closeAction:'hide',
        items:[{
            xtype: 'form',
            padding: '5 5 0 5',
            border: false,
            style: 'background-color: #fff;',
            layout: 'anchor',
            defaults:{
                labelWidth:60,
                xtype: 'textfield',
                allowBlank: true,
                anchor:'90%'
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            },{
                fieldLabel: '类型',
                xtype: 'radiogroup',
                items:[
                {
                    boxLabel:'设备变更',
                    name:'type',
                    inputValue:0
                }, {
                    boxLabel:'配置变更',
                    name:'type',
                    inputValue:1
                }, {
                    boxLabel:'网络变更',
                    name:'type',
                    inputValue:2
                }, {
                    boxLabel:'其它',
                    name:'type',
                    inputValue:3
                }
                ]
            },{
                name : 'log_time_date',
                fieldLabel: '日期',
                xtype:'datefield',
                format: 'Y-m-d',
                value: new Date()
            },{
                name : 'log_time_time',
                fieldLabel: '时间',
                xtype:'timefield',
                format:'H:i',
                value:new Date()
            },{
                xtype: 'hiddenfield',
                name : 'issystem',
                fieldLabel: '系统日志'
            },{
                xtype: 'hiddenfield',
                name : 'username',
                fieldLabel: '用户名'
            },{
                xtype: 'hiddenfield',
                name : 'realname',
                fieldLabel: '姓名'
            },{
                xtype: 'hiddenfield',
                name : 'equipment_type',
                fieldLabel: '设备类型'
            },{
                xtype: 'hiddenfield',
                name : 'equipment_id',
                fieldLabel: '设备ID'
            },{
                xtype: 'hiddenfield',
                name : 'equipment_name',
                fieldLabel: '设备名称'
            },{
                xtype: 'textarea',
                name : 'message',
                fieldLabel: '内容'
            }
            ]
        }],
        buttons : [
        {
            text: '保存',
            handler:function(button){
                var win    = button.up('window'),
                form   = win.down('form'),
                record = form.getRecord(),
                values = form.getValues();
                if (form.getForm().isValid()) {
                    form.getForm().submit({
                        url: '/idc/log/jsonsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            win.hide();
                            form.getForm().reset();
                        }
                    });
                }

            }
        },
        {
            text: '取消',
            handler: function(button){
                var win = button.up('window');
                win.close();
            }
        }
        ]
    });
    /****************************************************************************************/
    Idc.group.MainPanel = Ext.create('Ext.panel.Panel',{
        //title:'分组管理',
        renderTo:'MainPanel',
        bodyPadding:5,
        height:650,
        width:990,
        layout:'border',
        defaults:{
            collapsible:true,
            split:true
        },
        items:[Idc.group.WestPanel,Idc.group.GroupDetailPanel,Idc.group.EquipmentInfoPanel]
    });
});