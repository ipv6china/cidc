Ext.onReady(function(){
    Ext.QuickTips.init();
    Ext.ns('Idc','Idc.index','Idc.cabinet');
    /****************************************************************************************/
    Idc.index.IdcStore = Ext.create('Ext.data.Store',{
        fields: ['id','name','address', 'tel','contact','description'],
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: '/idc/idc/jsonall',
            reader:{
                type:'json',
                root:'rows'
            }
        }
    });
    /****************************************************************************************/
    Idc.index.CabinetStore = Ext.create('Ext.data.Store',{
        fields: ['id','idc_id','name','description','locked',{
            name:'lockedString',
            convert:function(v,r){
                var result = r.get('locked') == 1 ? '是':'否';
                return result;
            }
        }],
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: '/idc/cabinet/jsonall',
            reader:{
                type:'json',
                root:'rows'
            }
        }
    });
    /****************************************************************************************/
    Idc.index.EquipmentStore = Ext.create('Ext.data.Store',{
        autoLoad:false,
        fields: ['id',
        'name',
        'cabinet_id',
        'cabinet_name',
        'equipment_name',
        'equipment_id',
        'equipment_type',
        'equipment_height',
        'equipment_status',
        'device_tag',
        'position',
        {
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
        },{
            name:'status-icon',
            convert:function(value,record){

                switch(parseInt(record.get('equipment_status'))){
                    case 0:
                        return '<img src="/images/icons/status_green.png" border="0" />';
                        break;
                    case 1:
                        return '<img src="/images/icons/status_yellow.png" border="0" />';
                        break;
                    case 2:
                        return '<img src="/images/icons/status_red.png" border="0" />';
                        break;
                    default:
                        return '<img src="/images/icons/status_gray.png" border="0" />';
                        break;

                }
            }
        }
        ],
        proxy: {
            type: 'ajax',
            url: '/idc/equipment/jsonallequipments',
            reader:{
                type:'json',
                root:'rows'
            }
        },
        listeners:{
            load:function(){
                var c = Idc.index.CabinetDetailPanel.getStore().getTotalCount();
                if(c>0){
                    var record;
                    if(Ext.isEmpty(Idc.index.currentEquipmentDetail)){
                        //默认选中id=1的节点
                        Idc.index.CabinetDetailPanel.getSelectionModel().select(0);
                        record =Idc.index.CabinetDetailPanel.getSelectionModel().getSelection()[0];
                        Idc.index.CabinetDetailPanel.fireEvent('itemclick', '',record, 0);
                    }else{
                        var data = Idc.index.currentEquipmentDetail;
                        if(parseInt(data.id) >0){
                            record =  Idc.index.EquipmentStore.findRecord('id',data.id);
                            if(!Ext.isEmpty(record)){
                                Idc.index.CabinetDetailPanel.getSelectionModel().select(record);
                                Idc.index.CabinetDetailPanel.fireEvent('itemclick', '',record, 0);
                            }else{
                                Idc.index.CabinetDetailPanel.getSelectionModel().select(0);
                                record =Idc.index.CabinetDetailPanel.getSelectionModel().getSelection()[0];
                                Idc.index.CabinetDetailPanel.fireEvent('itemclick', '',record, 0);
                            }
                        }
                    }
                }
            }
        }
    });
    /****************************************************************************************/
    Idc.index.AllEquipmentStore = Ext.create('Ext.data.Store',{
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
    Idc.index.AllIpStore = Ext.create('Ext.data.Store',{
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
    Idc.index.TempIpStore = Ext.create('Ext.data.Store',{
        fields:['ip_string'],
        data:[{
            'ip_string':'device_tag'
        }]
    });
    /****************************************************************************************/
    //IDC机房导航树
    Ext.define('Idc.index.NavTreePanel',{
        extend:'Ext.tree.Panel',
        useArrows: false,
        rootVisible: true,
        multiSelect: false,
        singleExpand: false,
        store: {
            xtype:'store',
            fields: ['id','name','text', 'idc_id', 'cabinet_id','description','locked'
            ,{
                name:'qtip',
                convert:function(value,record){
                    return record.get('description').replace( /<[^>]*>/g,'');
                }
            }],
            proxy: {
                type: 'ajax',
                url: '/idc/idc/jsontree'
            }
        },
        root: {
            text: 'IDC',
            id: 'root',
            qtip:'根节点下默认显示未入机柜的设备',
            expanded: true
        },
        tbar:[{
            text: '添加',
            tooltip: '添加机房或者添加机柜',
            iconCls: 'icon-add',
            menu : {
                items: [{
                    text: '添加机房',
                    handler:function Idc_index_addIdc(btn){
                        Idc.index.IdcFormWindow.setTitle(btn.text);
                        Idc.index.IdcFormWindow.show();
                    }
                }, {
                    text: '添加机柜',
                    handler:function Idc_index_addCabinet(btn){
                        Idc.index.CabinetFormWindow.setTitle(btn.text);
                        Idc.index.CabinetFormWindow.show();
                    }
                }, {
                    text: '添加网络设备',
                    handler:function Idc_index_addCabinet(btn){
                        Idc.index.NetworkEquipmentFormWindow.setTitle(btn.text);
                        Idc.index.NetworkEquipmentFormWindow.show();
                    }
                },{
                    text:'导入服务器',
                    handler:function(btn){
                        window.location = '/idc/machine/import';
                    }
                }]
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
        }],
        listeners:{
            itemclick: function(view, record, item, index, e)
            {
                //点击节点时记录下节点和 index
                Idc.index.currentNavTreeNode = record;
                Idc.index.currentNavTreeNodeIndex = index;
                //改变机房信息面板的内容
                if(!record.data.root){
                    if(record.data.leaf){
                        //选择的是机柜
                        Idc.index.EquipmentStore.getProxy().url = '/idc/cabinet/jsoninfo/id/' + record.data.cabinet_id;
                        Idc.index.EquipmentStore.load();
                    }

                }else{
                    //选择根节点时，载入没有放入机柜的设备
                    Idc.index.EquipmentStore.getProxy().url = '/idc/equipment/jsonfreeequipment';
                    Idc.index.EquipmentStore.load();
                }
            },
            selectionchange:function(view,selects,options){
                if(selects.length > 0){
                    var data = selects[0].data;
                    if(!data.root){
                        if(data.leaf){
                            //选择的是机柜
                            var cabinetTpl =  Ext.create('Ext.XTemplate',
                                '<p style="font-size:15px;padding:6px;">机柜信息</p>',
                                '<div style="line-height:150%;"><p><b>名称:</b>{name}</p>',
                                '<p><b>锁定:</b>{lockedString}</p>',
                                '<p><b>备注:</b> {description}</p></div>'
                                );
                            var cabinetRecord = Idc.index.CabinetStore.findRecord('id',data.cabinet_id);
                            if(cabinetRecord){
                                var cabinetData = cabinetRecord.data;
                                cabinetTpl.overwrite(Idc.index.IdcDetailPanel.body,cabinetData);
                                Idc.index.IdcDetailPanel.doComponentLayout();
                            }

                        }else{
                            //选择的是机房
                            var idcTpl = Ext.create('Ext.XTemplate',
                                '<p style="font-size:15px;padding:6px;">机房信息</p>',
                                '<div style="line-height:150%;"><p><b>名称:</b>{name}</p>',
                                '<p><b>地址:</b>{address}</p>',
                                '<p><b>电话:</b>{tel}</p>',
                                '<p><b>联系人:</b>{contact}</p>',
                                '<p><b>备注:</b> {description}</p></div>'
                                );
                            var idcRecord = Idc.index.IdcStore.findRecord('id',data.idc_id);
                            if(idcRecord){
                                var idcData = idcRecord.data;
                                idcTpl.overwrite(Idc.index.IdcDetailPanel.body,idcData);
                                Idc.index.IdcDetailPanel.doComponentLayout();
                            }
                        }
                    }
                }
            },
            load:function(){
                //选中上次选中的节点，默认选中第一个节点
                var index = Idc.index.currentNavTreeNodeIndex || 2;
                var root = Idc.index.NavTreePanel.getStore().getRootNode();
                var record = root.findChild('id',index,true);

                Idc.index.NavTreePanel.getSelectionModel().select(record);
                Idc.index.NavTreePanel.fireEvent('itemclick', this,record);
            },
            itemdblclick:function(view,record,item,index,event,options){
                if(!record.data.root){
                    var data = record.data;
                    var form;
                    if(data.cabinet_id > 0){
                        Idc.index.CabinetFormWindow.setTitle('修改机柜');
                        Idc.index.CabinetFormWindow.show();
                        Idc.index.CabinetStore.load();
                        var cabinetData = Idc.index.CabinetStore.findRecord('id',record.data.cabinet_id).data;
                        form = Idc.index.CabinetFormWindow.down('form');
                        form.getForm().setValues(cabinetData);

                    }else{
                        Idc.index.IdcFormWindow.setTitle('修改机房');
                        Idc.index.IdcFormWindow.show();
                        Idc.index.IdcStore.load();
                        var idcData = Idc.index.IdcStore.findRecord('id',record.data.idc_id).data;
                        form = Idc.index.IdcFormWindow.down('form');
                        form.getForm().setValues(idcData);
                    }
                }
                //阻止默认双击事件发生
                event.preventDefault();
            }
        }
    });

    /****************************************************************************************/
    Idc.index.NavTreePanel = Ext.create('Idc.index.NavTreePanel',{
        region:'north',
        split: true,
        autoScroll: true,
        border:0,
        height: 390,
        minSize: 150
    });
    /****************************************************************************************/
    Idc.index.IdcDetailPanel = Ext.create('Ext.panel.Panel',{
        //title:'机房资料',
        region: 'center',
        autoScroll: true,
        border:0,
        bodyPadding:5,
        bodyStyle: 'background:#fafafa;',
        html: ''
    });
    /****************************************************************************************/
    //左侧IDC导航
    Idc.index.WestPanel = Ext.create('Ext.panel.Panel',{
        title : '导航',
        region:'west',
        collapsible:true,
        width:160,
        minWidth:100,
        maxWidth:350,
        layout: 'border',
        items:[Idc.index.NavTreePanel,Idc.index.IdcDetailPanel]
    });
    /****************************************************************************************/
    //机房Form
    Idc.index.IdcFormWindow = Ext.create('Ext.window.Window',{
        title : 'IDC机房',
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
                labelWidth:50,
                xtype: 'textfield',
                allowBlank: false,
                anchor:'90%'
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            },{
                name : 'name',
                fieldLabel: '名称'
            },{
                name : 'address',
                fieldLabel: '地址'
            },{
                name : 'contact',
                fieldLabel: '联系人'
            },{
                name : 'tel',
                fieldLabel: '电话'
            },{
                xtype: 'htmleditor',
                name : 'description',
                fieldLabel: '描述'
            }
            ]
        }],
        buttons : [
        {
            text:'<font color="red">删除</font>',
            handler:function(button){
                var win = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Idc.index.NavTreePanel.getSelectionModel().getSelection()[0];
                        var idc_id = node.data.idc_id;
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/idc/idc/jsondel',
                            params:{
                                'id':idc_id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                                node.remove();
                            }
                        });
                        //删除之后选中第一个节点
                        Idc.index.NavTreePanel.getSelectionModel().select(2);
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
                        url: '/idc/idc/jsonsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            win.hide();
                            //刷新树
                            Idc.index.NavTreePanel.getRootNode().removeAll();
                            Idc.index.NavTreePanel.getStore().load();
                            Idc.index.NavTreePanel.getView().refresh();
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
    //机柜Form
    Idc.index.CabinetFormWindow = Ext.create('Ext.window.Window',{
        title : '机柜',
        layout: 'fit',
        autoShow: false,
        width: 380,
        closeAction:'hide',
        items:[{
            xtype: 'form',
            padding: '5 5 0 5',
            border: false,
            style: 'background-color: #fff;',
            layout:'anchor',
            defaults:{
                labelWidth:80,
                xtype: 'textfield',
                allowBlank:false,
                anchor:'96%'

            },
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            },{
                name : 'name',
                fieldLabel: '名称'
            },{
                xtype:'combo',
                name : 'idc_id',
                fieldLabel: '所属机房',
                store: Idc.index.IdcStore,
                typeAhead: true,
                queryMode: 'local',
                triggerAction: 'all',
                selectOnFocus: true,
                displayField:'name',
                valueField:'id'
            },{
                fieldLabel: '锁定',
                xtype: 'radiogroup',
                items: [{
                    boxLabel: '是',
                    name: 'locked',
                    inputValue:1
                },
                {
                    boxLabel: '否',
                    name: 'locked',
                    inputValue:0
                }]
            },{
                xtype: 'textarea',
                name : 'description',
                allowBlank:true,
                fieldLabel: '描述'
            }
            ]
        }],
        buttons : [{
            text:'<font color="red">删除</font>',
            handler:function(button){
                var win = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Idc.index.NavTreePanel.getSelectionModel().getSelection()[0];
                        var cabinet_id = node.data.cabinet_id;
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/idc/cabinet/jsondel',
                            params:{
                                'id':cabinet_id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                                node.remove();
                                Idc.index.CabinetStore.load();
                            }
                        });
                        //删除之后选中第一个节点
                        Idc.index.NavTreePanel.getSelectionModel().select(2);
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
                        url: '/idc/cabinet/jsonsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            win.hide();
                            //刷新树
                            Idc.index.NavTreePanel.getRootNode().removeAll();
                            Idc.index.NavTreePanel.getStore().load();
                            Idc.index.NavTreePanel.getView().refresh();
                            form.getForm().reset();
                            Idc.index.CabinetStore.load();
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
    //服务器 Form
    Idc.index.MachineFormWindow = Ext.create('Ext.window.Window',{
        title : '修改服务器',
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
                labelWidth:100,
                xtype: 'textfield',
                allowBlank: false,
                anchor:'90%'
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            },{
                name : 'machine_id',
                fieldLabel: 'MachineId'
            },{
                name : 'name',
                fieldLabel: '主机名'
            },{
                name : 'device_tag',
                fieldLabel: '设备标签',
                allowBlank: true,
                editable:true,
                xtype:'combo',
                valueField:'ip_string',
                displayField:'ip_string',
                store:Idc.index.TempIpStore
            },{
                fieldLabel:'设备状态',
                xtype: 'combo',
                name:'status',
                store: Ext.create('Ext.data.ArrayStore', {
                    fields: ['value','text' ],
                    data: [
                    [0,'正常'],
                    [1,'闲置'],
                    [2,'损坏']
                    ]
                }),
                displayField: 'text',
                valueField: 'value',
                queryMode: 'local',
                editable:false
            },{
                name : 'height',
                fieldLabel: '服务器高度'
            },{
                name : 'host_machine',
                allowBlank:true,
                fieldLabel: '宿主机Id'
            },{
                name : 'cpu',
                fieldLabel: 'CPU'
            },{
                name : 'memory',
                fieldLabel: '内存'
            },{
                name : 'harddisk',
                fieldLabel: '硬盘'
            },{
                name : 'manufacturer',
                fieldLabel: '制造商'
            },{
                name : 'product_name',
                fieldLabel: '产品型号'
            },{
                name : 'serial_number',
                fieldLabel: '序列号'
            },{
                name : 'os',
                fieldLabel: '操作系统'
            },{
                name : 'plateform',
                fieldLabel: '平台'
            },{
                name : 'version',
                fieldLabel: '内核版本'
            },{
                name : 'admin_dept',
                allowBlank:true,
                fieldLabel: '所在部门'
            },{
                name : 'admin_username',
                allowBlank:true,
                fieldLabel: '负责人用户名'
            },{
                name : 'admin_realname',
                allowBlank:true,
                fieldLabel: '负责人姓名'
            },{
                name : 'cabinet_id',
                fieldLabel: '所在机柜',
                xtype: 'treecombobox',
                //treeUrl:'/idc/idc/jsontree',
                store:Ext.create('Ext.data.TreeStore', {
                    nodeParam: 'id',
                    fields: ['id','name', 'text', 'idc_id','idc_name','cabinet_id',{
                        name:'nameString',
                        convert:function(value,record){
                            return record.get('idc_id') + ' - ' + record.get('text');
                        }
                    }],
                    proxy: {
                        type: 'ajax',
                        url: '/idc/idc/jsontree'
                    },
                    root: {
                        id: 'root',
                        text: 'root',
                        expanded: true
                    }
                }),
                valueField: 'cabinet_id',
                displayField: 'nameString'
            },{
                xtype: 'htmleditor',
                name : 'description',
                fieldLabel: '描述'
            }]
        }],
        buttons : [{
            text:'添加日志',
            handler:function(btn){
                btn.up('window').hide();
                var equipmentRecord = Idc.index.CabinetDetailPanel.getSelectionModel().getSelection()[0];
                var equipment = equipmentRecord.data;
                var form =  Idc.index.LogFormWindow.down('form');
                form.getForm().reset();
                form.getForm().setValues({
                    'type':3,
                    'issystem':false,
                    'equipment_type':equipment.equipment_type,
                    'equipment_id':equipment.equipment_id,
                    'equipment_name':equipment.equipment_name
                });
                Idc.index.LogFormWindow.down('button[text="保存"]').show();
                Idc.index.LogFormWindow.down('button[text="取消"]').show();
                Idc.index.LogFormWindow.show();
            }
        },{
            text:'JSON导入',
            handler:function(btn){
                window.location = '/idc/machine/import';
            }
        },{
            text:'JSON导出',
            handler:function(btn){
                var node = Idc.index.CabinetDetailPanel.getSelectionModel().getSelection()[0];
                var id = node.data.id;
                window.location = '/idc/machine/jsonexport/id/'+id;
            }
        },
        {
            text:'<font color="red">移除</font>',
            tooltip:'从机柜中移除设备',
            handler:function(button){
                var win = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Idc.index.CabinetDetailPanel.getSelectionModel().getSelection()[0];
                        var id = node.data.id;
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/idc/cabinet/jsonremoveequipment',
                            params:{
                                'id':id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                            }
                        });
                        //删除之后选中第一个节点
                        Idc.index.CabinetDetailPanel.getStore().load();
                        Idc.index.CabinetDetailPanel.getSelectionModel().select(0);
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
                        url: '/idc/machine/jsonsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(form, action) {
                            Etao.msg.info('Success', action.result.msg);
                            win.hide();
                            Idc.index.CabinetDetailPanel.getStore().load();
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
    //网络设备 Form
    Idc.index.NetworkEquipmentFormWindow = Ext.create('Ext.window.Window',{
        title : '修改网络设备',
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
                labelWidth:100,
                xtype: 'textfield',
                allowBlank: true,
                anchor:'90%'
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            },{
                name : 'name',
                fieldLabel: '设备名称'
            },{
                name : 'device_tag',
                fieldLabel: '设备标签'
            },{
                fieldLabel:'设备状态',
                xtype: 'combo',
                name:'status',
                store: Ext.create('Ext.data.ArrayStore', {
                    fields: ['value','text' ],
                    data: [
                    [0,'正常'],
                    [1,'闲置'],
                    [2,'损坏']
                    ]
                }),
                displayField: 'text',
                valueField: 'value',
                queryMode: 'local',
                editable:false
            } ,{
                name : 'manufacturer',
                fieldLabel: '制造商'
            },{
                name : 'product_name',
                fieldLabel: '产品型号'
            },{
                fieldLabel: '线路类型',
                xtype: 'checkboxgroup',
                items: [{
                    boxLabel: '内网',
                    name: 'network_type_lan',
                    inputValue:1
                },
                {
                    boxLabel: '电信',
                    name: 'network_type_tel',
                    inputValue:1
                },
                {
                    boxLabel: '网通',
                    name: 'network_type_cnc',
                    inputValue:1
                }]
            },{
                name : 'height',
                fieldLabel: '设备高度'
            },{
                name : 'admin_dept',
                fieldLabel: '管理部门'
            },{
                name : 'admin_username',
                fieldLabel: '负责人用户名'
            },{
                name : 'admin_realname',
                fieldLabel: '负责人姓名'
            },{
                name : 'cabinet_id',
                fieldLabel: '所在机柜',
                xtype: 'treecombobox',
                store:Ext.create('Ext.data.TreeStore', {
                    nodeParam: 'id',
                    fields: ['id','name', 'text', 'idc_id','idc_name','cabinet_id',{
                        name:'nameString',
                        convert:function(value,record){
                            return record.get('idc_id') + ' - ' + record.get('text');
                        }
                    }],
                    proxy: {
                        type: 'ajax',
                        url: '/idc/idc/jsontree'
                    },
                    root: {
                        id: 'root',
                        text: 'root',
                        expanded: true
                    }
                }),
                valueField: 'cabinet_id',
                displayField: 'nameString'
            },{
                name : 'ip',
                fieldLabel: '管理IP'
            },{
                xtype: 'htmleditor',
                name : 'description',
                fieldLabel: '备注'
            }]
        }],
        buttons : [
        {
            text:'<font color="red">移除</font>',
            tooltip:'从机柜中移除设备',
            handler:function(button){
                var win = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Idc.index.CabinetDetailPanel.getSelectionModel().getSelection()[0];
                        var id = node.data.id;
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/idc/cabinet/jsonremoveequipment',
                            params:{
                                'id':id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                            }
                        });
                        //删除之后选中第一个节点
                        Idc.index.CabinetDetailPanel.getSelectionModel().select(0);
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
                        url: '/idc/networkequipment/jsonsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(form, action) {
                            Etao.msg.info('Success', action.result.msg);
                            win.hide();
                            Idc.index.CabinetDetailPanel.getStore().load();
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
    //机柜机器类
    Ext.define('Idc.cabinet.CabinetPanel', {
        extend: 'Ext.grid.Panel',
        //title : '机柜',
        columnLines: true,
        loadMask: true,
        stripeRows:true,
        viewConfig: {
            plugins: {
                ptype: 'gridviewdragdrop',
                ddGroup:'EquipmentDD',
                enableDrag:true,
                enableDrop:true
            },
            listeners: {
                beforedrop:{
                    fn:function(node,data,overModel,dropPosition,dropFunction){
                        var cabinetLocked = true;
                        var cabinetId = data.records[0].get('cabinet_id');
                        var cabinet = Idc.index.CabinetStore.findRecord('id',cabinetId);
                        if(null === cabinet || typeof(cabinet.data) == 'undefined'){
                            alert('不属于同一个机柜的设备不能改变顺序');
                            return false;
                        }

                        //判断store中的设备是否属于同一个机柜
                        var isSameCabinet = true;
                        Idc.index.EquipmentStore.each(function(record){
                            if(record.data.cabinet_id != cabinetId){
                                isSameCabinet = false;
                            }
                        });
                        if(!isSameCabinet){
                            alert('不属于同一个机柜的设备不能改变顺序');
                            return false;
                        }
                        if(parseInt(cabinet.data.locked) == 0){
                            return true;
                        }else{
                            alert('机柜被锁定，不允许拖动');
                            return false;
                        }
                    }
                },
                drop: function(node, data, dropRec, dropPosition) {
                    var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('id') : ' on empty view';
                    var datas = [];
                    Idc.index.EquipmentStore.each(function(record){
                        datas.push(record.data.id);
                    });
                    Ext.Ajax.request({
                        method:'GET',
                        url: '/idc/cabinet/jsonupdateposition',
                        params:{
                            'ids':implode(',',datas)
                        },
                        success: function(response) {
                            Etao.msg.info('Success', response.responseText);
                            Idc.index.EquipmentStore.load();
                        }
                    });
                }
            }
        },
        columns: [
        {
            xtype:'rownumberer',
            width:30
        },

        {
            dataIndex:'status-icon',
            width:30
        },
        {
            text: "设备名称",
            flex: 1,
            sortable: false,
            dataIndex:'name'
        },
        {
            text: "设备标签",
            flex: 1,
            sortable: false,
            dataIndex:'device_tag'
        }
        ],
        store:Idc.index.EquipmentStore,
        listeners:{
            itemdblclick:function(view,record,item,index,e,options){
                var data = record.data;
                switch(parseInt(data.equipment_type)){
                    case 0:
                        Idc.index.MachineFormWindow.setTitle('修改服务器');
                        Idc.index.MachineFormWindow.show();
                        Ext.Ajax.request({
                            url: '/idc/machine/jsoninfo/id/'+data.equipment_id,
                            success: function(response) {
                                var mData = Ext.JSON.decode(response.responseText);
                                if(typeof(mData.ips) != 'undefined' && mData.ips.length >0){
                                    Idc.index.TempIpStore.loadData(mData.ips);
                                }
                                var form = Idc.index.MachineFormWindow.down('form');
                                form.getForm().setValues(mData);
                                var combo = form.down('*[name=device_tag]');
                                combo.setValue(mData.device_tag);
                                combo.setRawValue(mData.device_tag);
                            }
                        });
                        break;
                    case 2:
                        Ext.Ajax.request({
                            url: '/idc/networkequipment/jsoninfo/id/'+data.equipment_id,
                            success: function(response) {
                                var form = Idc.index.NetworkEquipmentFormWindow.down('form');
                                var mData = Ext.JSON.decode(response.responseText);
                                form.getForm().setValues(mData);
                                Idc.index.NetworkEquipmentFormWindow.setTitle('修改网络设备');
                                Idc.index.NetworkEquipmentFormWindow.show();

                            }
                        });
                        break;
                    default:
                        alert('无法修改：未知的设备类型'+ Ext.encode(data));
                        break;
                }
            },
            selectionchange:function(view,selections,options){
                if(selections.length > 0){
                    var data = selections[0].data;
                    //记录当前操作数据
                    Idc.index.currentEquipmentDetail = data;

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
                                        '<tr><td>MachineID</td><td> <a href="/idc/machine/jsonexport/id/{id}" target="_blank">{machine_id}</a></td></tr>',
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
                                    machineTpl.overwrite(Idc.index.EastPanel.body,machineData);
                                }
                            });
                            break;
                        case 1:
                            data.typeString='机柜';
                            equipmentTpl.overwrite(Idc.index.EastPanel.body,data);
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
                                    networkEquipmentTpl.overwrite(Idc.index.EastPanel.body,networkEquipmentData);
                                }
                            });
                            break;
                        default:
                            Idc.index.EastPanel.update('未知的设备类型');
                            break;
                    }
                    Idc.index.EastPanel.doComponentLayout();
                    //选择机柜
                    if(parseInt(data.cabinet_id)>0){
                        Idc.index.functionSelectCabinetNavtree(data.cabinet_id);
                    }else{
                        Idc.index.NavTreePanel.getSelectionModel().select(0);
                    }
                    //载入操作日志
                    var logstore = Idc.index.LogInfoPanel.getStore();
                    logstore.getProxy().url = '/idc/log/jsonall/equipment_type/'+data.equipment_type+'/equipment_id/'+data.equipment_id;
                    logstore.load();
                }
            }
        },
        tbar:[{
            iconCls:'icon-search',
            handler:function(btn){
                if(!Ext.isEmpty(Idc.index.currentNavTreeNode)){
                    var currentCabinetId = Idc.index.currentNavTreeNode.data.cabinet_id;
                    //选择机柜
                    if(parseInt(currentCabinetId)>0){
                        cabinetId = currentCabinetId;
                    }else{
                        alert('请先选择机柜');
                        return;
                    }
                }

                var grid = Idc.index.SelectEquipmentWindow.down('gridpanel');
                var store = grid.getStore();
                store.load();
                Idc.index.SelectEquipmentWindow.show();
            }
        },{
            xtype:'combo',
            store: Idc.index.AllEquipmentStore,
            hideLabel: true,
            displayField: 'name',
            valueField:'name',
            typeAhead: false,
            queryMode: 'local',
            autoSelect:false,
            triggerAction: 'all',
            emptyText: '按 主机名 查找',
            width: 135,
            enableKeyEvents:true,
            listeners:{
                specialkey:{
                    fn:function(field,e){
                        if (e.getKey() == e.ENTER) {
                            Idc.index.EquipmentStore.getProxy().url = '/idc/equipment/jsonallequipments/queryType/name/queryWord/'+field.getValue();
                            Etao.msg.info('Search Url', '/idc/equipment/jsonallequipments/queryType/name/queryWord/'+field.getValue());
                            Idc.index.EquipmentStore.load();
                        }
                    }
                }
            }
        },'-',{
            xtype:'combo',
            hideLabel: true,
            store:Idc.index.AllIpStore,
            displayField: 'ip_string',
            valueField:'ip_string',
            typeAhead: false,
            queryMode: 'local',
            triggerAction: 'all',
            emptyText: '按 IP 查找',
            autoSelect:false,
            width: 135,
            enableKeyEvents:true,
            listeners:{
                specialkey:{
                    fn:function(field,e){
                        if (e.getKey() == e.ENTER) {
                            Idc.index.EquipmentStore.getProxy().url = '/idc/equipment/jsonallequipments/queryType/ip/queryWord/'+field.getValue();
                            Etao.msg.info('Search Url', '/idc/equipment/jsonallequipments/queryType/ip/queryWord/'+field.getValue());
                            Idc.index.EquipmentStore.load();
                        }
                    }
                }
            }
        }],
        constructor: function(config) {
            config = Ext.apply({}, config);
            this.callParent([config = config || {}]);
        }
    });
    /****************************************************************************************/
    Idc.index.CabinetDetailPanel = Ext.create('Idc.cabinet.CabinetPanel',{
        //title:'机柜',
        region: 'center',
        autoScroll: true,
        border:0
    });

    /****************************************************************************************/
    Idc.index.LogStore = Ext.create('Ext.data.Store', {
        storeId:'logStore',
        autoLoad:false,
        fields:['id', 'type', 'issystem','username','realname','message',{
            name:'log_time',
            type:'date',
            dateFormat:'timestamp'
        },'equipment_type','equipment_id'],
        proxy: {
            type: 'ajax',
            url:'/idc/log/jsonall',
            reader: {
                type: 'json',
                root: 'rows'
            }
        }
    });
    /****************************************************************************************/
    //日志Form
    Idc.index.LogFormWindow = Ext.create('Ext.window.Window',{
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
        buttons : [{
            text:'<font color="red">删除</font>',
            handler:function(button){
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Idc.index.LogInfoPanel.getSelectionModel().getSelection()[0];
                        var id = node.data.id;
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/idc/log/jsondel',
                            params:{
                                'id':id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                                Idc.index.LogInfoPanel.getStore().load();
                            }
                        });
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
                        url: '/idc/log/jsonsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            win.hide();
                            form.getForm().reset();
                            Idc.index.LogInfoPanel.getStore().load();
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
    Idc.index.LogInfoPanel = Ext.create('Ext.grid.Panel', {
        title:'操作日志',
        region: 'south',
        height:200,
        autoScroll: true,
        border:0,
        store: Ext.data.StoreManager.lookup('logStore'),
        columns: [
        {
            header: '日期',
            xtype:'datecolumn',
            width:120,
            format:'Y-m-d H:i',
            dataIndex: 'log_time'
        },
        {
            header: '发起者',
            width:60,
            dataIndex: 'realname'
        },
        {
            header: '事件',
            flex:1,
            dataIndex: 'message'
        }
        ],
        listeners:{
            itemdblclick:function(view,record,item,index,e,options){
                var form = Idc.index.LogFormWindow.down('form');
                form.getForm().setValues(record.data);
                var btns = Idc.index.LogFormWindow.query('button');
                if(1 == parseInt(record.data.issystem)){
                    //隐藏btn
                    Ext.each(btns, function(btn){
                        btn.hide();
                    });
                }else{
                    //显示btn
                    Ext.each(btns, function(btn){
                        btn.show();
                    });
                }
                Idc.index.LogFormWindow.down('button[text="取消"]').show();
                Idc.index.LogFormWindow.show();

            }
        }
    });
    /****************************************************************************************/
    //中间显示部分，包括机柜信息，搜索
    Idc.index.CenterPanel = Ext.create('Ext.panel.Panel',{
        // title : '机柜信息',
        region: 'center',
        collapsible:false,
        width:250,
        minWidth:100,
        maxWidth:350,
        layout: 'border',
        items:[Idc.index.LogInfoPanel,Idc.index.CabinetDetailPanel]
    });
    /****************************************************************************************/
    //右侧显示部分，包括 机器信息，修改日志等
    Idc.index.EastPanel = Ext.create('Ext.panel.Panel',{
        title : '机器信息',
        region: 'east',
        collapsible:false,
        flex:1,
        border:1,
        bodyPadding:12,
        autoScroll: true,
        tbar:['->',{
            iconCls: 'icon-note_add',
            text:'添加日志',
            handler:function(){
                var equipmentRecord = Idc.index.CabinetDetailPanel.getSelectionModel().getSelection()[0];
                var equipment = equipmentRecord.data;
                var form =  Idc.index.LogFormWindow.down('form');
                form.getForm().reset();
                form.getForm().setValues({
                    'type':3,
                    'issystem':false,
                    'equipment_type':equipment.equipment_type,
                    'equipment_id':equipment.equipment_id,
                    'equipment_name':equipment.equipment_name
                });
                Idc.index.LogFormWindow.down('button[text="保存"]').show();
                Idc.index.LogFormWindow.down('button[text="取消"]').show();
                Idc.index.LogFormWindow.show();
            }
        }]
    });

    /****************************************************************************************/
    Idc.index.SelectEquipmentWindow = Ext.create('Ext.window.Window',{
        title : '选择设备放入机柜',
        layout: 'fit',
        autoShow: false,
        modal:true,
        height:400,
        width: 680,
        closeAction:'hide',
        items:[{
            xtype:'gridpanel',
            columnLines: true,
            loadMask: true,
            stripeRows:true,
            border:0,
            selModel: Ext.create('Ext.selection.CheckboxModel'),
            columns: [
            {
                text: "设备名称",
                flex: 1,
                dataIndex: 'name'
            },
            {
                text: "设备标签",
                flex: 1,
                dataIndex: 'device_tag'
            },
            {
                text: "类型",
                width:90,
                sortable: true,
                dataIndex: 'type_string'
            }],
            store : Ext.create('Ext.data.Store',{
                autoLoad:false,
                fields: ['id','name', 'height','device_tag', 'equipment_type','equipment_id',
                {
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
                    url: '/idc/equipment/jsonfreeequipment',
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
            store: Idc.index.AllEquipmentStore,
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
                            var panel = Idc.index.SelectEquipmentWindow.down('gridpanel');
                            var store = panel.getStore();
                            store.getProxy().url = '/idc/equipment/jsonfreeequipment/queryType/name/queryWord/'+field.getValue();
                            Etao.msg.info('Search Url', '/idc/equipment/jsonfreeequipment/queryType/name/queryWord/'+field.getValue());
                            store.load();
                        }
                    }
                }
            }
        },'-','按设备标签筛选：',{
            xtype:'combo',
            store: Idc.index.AllIpStore,
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
                            var panel = Idc.index.SelectEquipmentWindow.down('gridpanel');
                            var store = panel.getStore();
                            store.getProxy().url = '/idc/equipment/jsonfreeequipment/queryType/device_tag/queryWord/'+field.getValue();
                            Etao.msg.info('Search Url', '/idc/equipment/jsonfreeequipment/queryType/device_tag/queryWord/'+field.getValue());
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
                var win = btn.up('window');
                var grid = win.down('gridpanel');
                var cabinetId;
                if(!Ext.isEmpty(Idc.index.currentNavTreeNode)){
                    var currentCabinetId = Idc.index.currentNavTreeNode.data.cabinet_id;
                    //选择机柜
                    if(parseInt(currentCabinetId)>0){
                        cabinetId = currentCabinetId;
                    }else{
                        alert('请先选择机柜');
                        return;
                    }
                }
                Ext.each(grid.getSelectionModel().getSelection(),function(rec){
                    data = rec.data;
                    Ext.Ajax.request({
                        method:'POST',
                        url: '/idc/cabinet/jsonaddequipment',
                        params:{
                            'equipment_type':data.equipment_type,
                            'equipment_id':data.equipment_id,
                            'cabinet_id':cabinetId
                        },
                        success: function(response) {
                            Etao.msg.info('Success', response.responseText);
                            Idc.index.EquipmentStore.load();
                            win.hide();
                        }
                    });
                });
                win.hide();
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
    Idc.index.MainPanel = Ext.create('Ext.panel.Panel',{
        renderTo:'MainPanel',
        bodyPadding:5,
        height:650,
        width:990,
        layout:'border',
        defaults:{
            collapsible:true,
            split:true
        },
        items:[Idc.index.WestPanel,Idc.index.CenterPanel,Idc.index.EastPanel]
    });

    //选择导航栏
    Idc.index.functionSelectCabinetNavtree = function(cabinetId){
        var root = Idc.index.NavTreePanel.getStore().getRootNode();
        var treeRecord = root.findChild('cabinet_id',cabinetId,true);
        Idc.index.NavTreePanel.getSelectionModel().select(treeRecord);
    };
});