Ext.onReady(function(){
    Ext.ns('Idc','Idc.log');
    /****************************************************************************************/

    Idc.log.LogStore = Ext.create('Ext.data.Store',{
        fields: [
        'id',
        'priority',
        'type',
        'issystem',
        'username',
        'realname',
        'message',
        {
            name:'log_time',
            type:'date',
            dateFormat:'timestamp'
        },
        'equipment_type',
        'equipment_id',
        'equipment_name',
        {
            name:'userString',
            convert:function(value,r){
                var result = r.get('realname')+'('+r.get('username')+')';
                return result;
            }
        }
        ],
        autoLoad:false,
        totalProperty: 'totalCount',
        idProperty: 'id',
        pageSize:25,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: '/idc/log/jsonlist',
            reader:{
                type:'json',
                root:'rows'
            }
        },
        listeners:{
            'beforeload':function(store,options){
                var new_params = {
                    equipment_name: Ext.getCmp('search_equipment_name').getValue()
                };
                Ext.apply(store.proxy.extraParams, new_params);
            }
        }
    });
    /****************************************************************************************/
    //日志Form
    Idc.log.LogFormWindow = Ext.create('Ext.window.Window',{
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
                name : 'username',
                fieldLabel: '用户名'
            },{
                xtype: 'hiddenfield',
                name : 'realname',
                fieldLabel: '姓名'
            },{
                xtype: 'hiddenfield',
                name : 'issystem',
                fieldLabel: '系统日志'
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
            text:'删除',
            handler:function(button){
                 var win    = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Idc.log.MainPanel.getSelectionModel().getSelection()[0];
                        var id = node.data.id;
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/idc/log/jsondel',
                            params:{
                                'id':id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                                Idc.log.LogStore.load();
                            }
                        });
                        win.hide();
                    }
                });
            }
        },'->',
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
                            Idc.log.LogStore.load();
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
    Idc.log.MainPanel = Ext.create('Ext.grid.Panel', {
        extend: 'Ext.grid.Panel',
        renderTo:'MainPanel',
        //title : '操作日志',
        store : Idc.log.LogStore,
        multiSelect: false,
        columns: [{
            header:'ID',
            width:40,
            dataIndex:'id'
        },{
            header: '时间',
            xtype:'datecolumn',
            width:145,
            format:'Y-m-d H:i:s',
            dataIndex: 'log_time'
        },{
            text: '用户',
            width:130,
            dataIndex: 'userString'
        },{
            text: '设备',
            width:130,
            dataIndex: 'equipment_name'
        },{
            text: '级别',
            width:130,
            dataIndex: 'priority'
        },{
            text: '消息',
            flex:1,
            dataIndex: 'message'
        }],
        tbar:[{
            iconCls:'icon-search'
        },{
            fieldLabel:'日志类型',
            labelWidth:60,
            xtype: 'combo',
            name:'issystem',
            id:'search_log_issystem',
            store: Ext.create('Ext.data.ArrayStore', {
                fields: ['value','text' ],
                data: [
                [0,'用户日志'],
                [1,'系统日志']
                ]
            }),
            displayField: 'text',
            valueField: 'value',
            queryMode: 'local',
            editable: false
        },{
            xtype:'combo',
            store: {
                xtype:'store',
                fields: ['id','name', 'height', 'type','description'],
                storeId:'choseEquipmentHostnameStore',
                autoLoad:true,
                proxy: {
                    type: 'ajax',
                    url: '/idc/equipment/jsonallequipments',
                    reader:{
                        type:'json',
                        root:'rows'
                    }
                }
            },
            id:'search_equipment_name',
            fieldLabel:'主机名',
            labelWidth:40,
            width: 225,
            labelAlign:'right',
            hideLabel: false,
            displayField: 'name',
            valueField:'name',
            typeAhead: false,
            queryMode: 'local',
            autoSelect:false,
            triggerAction: 'all',
            enableKeyEvents:true,
            listeners:{
                specialkey:{
                    fn:function(field,e){
                        if (e.getKey() == e.ENTER) {
                            var store = Idc.log.LogStore;
                            Ext.apply(store.proxy.extraParams, {
                                'equipment_name':field.getValue()
                            });
                            store.load();
                        }
                    }
                }
            }
        },{
            name : 'log_time_start',
            id:'search_log_time_start',
            fieldLabel: '起始日期',
            xtype:'datefield',
            labelAlign:'right',
            labelWidth:60,
            format: 'Y-m-d'
        },{
            name : 'log_time_end',
            id:'search_log_time_end',
            fieldLabel: '结束日期',
            xtype:'datefield',
            labelAlign:'right',
            labelWidth:60,
            format: 'Y-m-d'
        },{
            type:'button',
            border:2,
            text:'搜索',
            handler:function(){
                var store = Idc.log.LogStore;
                Ext.apply(store.proxy.extraParams, {
                    'issystem':Ext.getCmp('search_log_issystem').getValue(),
                    'equipment_name':Ext.getCmp('search_equipment_name').getValue(),
                    'log_time_start':Ext.getCmp('search_log_time_start').getValue(),
                    'log_time_end':Ext.getCmp('search_log_time_end').getValue()
                });
                store.load();
            }
        }],
        dockedItems: [{
            xtype:'buttongroup',
            items: [{
                text: '添加日志',
                iconCls: 'icon-add',
                scale: 'small',
                handler:function(){
                    var form =  Idc.log.LogFormWindow.down('form');
                    form.getForm().reset();
                    form.getForm().setValues({
                        'type':3,
                        'issystem':false
                    //'equipment_type':equipment.equipment_type,
                    //'equipment_id':equipment.equipment_id
                    });
                    Idc.log.LogFormWindow.down('button[text="保存"]').show();
                    Idc.log.LogFormWindow.down('button[text="取消"]').show();
                    Idc.log.LogFormWindow.down('button[text="删除"]').hide();
                    Idc.log.LogFormWindow.show();
                }
            }]
        },{
            xtype: 'pagingtoolbar',
            store: Idc.log.LogStore,
            dock: 'bottom',
            displayInfo : true,
            displayMsg : '第{0} 到 {1} 条数据 共{2}条'
        }],
        listeners:{
            itemdblclick:function(view,record,item,index,e,options){
                var form = Idc.log.LogFormWindow.down('form');
                form.getForm().setValues(record.data);
                var btns = Idc.log.LogFormWindow.query('button');
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
                Idc.log.LogFormWindow.down('button[text="取消"]').show();
                Idc.log.LogFormWindow.show();
            }
        }
    });
    /****************************************************************************************/
    var logStore = Idc.log.LogStore;
    Ext.getCmp('search_log_issystem').setValue(0);
    Ext.apply(logStore.proxy.extraParams, {
        'issystem':Ext.getCmp('search_log_issystem').getValue()
    });
    logStore.load();
/****************************************************************************************/

});