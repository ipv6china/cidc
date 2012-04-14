Ext.onReady(function(){
    Ext.ns('Idc','Idc.domain');
    /****************************************************************************************/
    Idc.domain.DomainRecordStore = Ext.create('Ext.data.Store',{
        fields: ['id','zone_id','zone_name','name','address','type','priority','active','description'],
        autoLoad:false,
        proxy: {
            type: 'ajax',
            url: '/idc/domain/jsondomainrecordlist',
            reader:{
                type:'json',
                root:'rows'
            }
        }
    });
    /****************************************************************************************/
    Idc.domain.DomainZoneStore = Ext.create('Ext.data.Store',{
        fields: ['id','name','ns','server','admin_username','admin_realname','parent','description'],
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: '/idc/domain/jsondomainzonelist',
            reader:{
                type:'json',
                root:'rows'
            }
        }
    });
    /****************************************************************************************/
    //域名Form
    Idc.domain.DomainZoneFormWindow = Ext.create('Ext.window.Window',{
        title : '域名表单',
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
            items:  [
            {
                xtype: 'hiddenfield',
                name: 'id'
            },{
                fieldLabel: '域名',
                name: 'name',
                allowBlank:false
            },{
                fieldLabel: 'NS地址',
                name: 'ns',
                allowBlank:true
            },{
                fieldLabel: '管理员',
                name: 'admin_username',
                allowBlank:true
            },{
                fieldLabel: '备注',
                xtype:'textarea',
                allowBlank:true,
                name: 'description'
            }]
        }],
        buttons : [
        {
            text:'删除',
            handler:function(button){
                var win = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Idc.domain.DomainZonePanel.getSelectionModel().getSelection()[0];
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/idc/domain/jsondeldomainzone',
                            params:{
                                'id':node.data.id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                                node.remove();
                            }
                        });
                        //删除之后选中第一个节点
                        Idc.domain.DomainZonePanel.getSelectionModel().select(0);
                        win.close();
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
                        url: '/idc/domain/jsondomainzonesave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            win.hide();
                            //刷新树
                            Idc.domain.DomainZonePanel.getRootNode().removeAll();
                            Idc.domain.DomainZonePanel.getStore().load();
                            Idc.domain.DomainZonePanel.getView().refresh();
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
    //域名记录 form
    Idc.domain.DomainRecordFormWindow = Ext.create('Ext.window.Window',{
        title : '域名记录表单',
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
                labelWidth:80,
                xtype: 'textfield',
                anchor:'-10',
                labelAlign:'right'
            },
            items:  [
            {
                xtype: 'hiddenfield',
                name: 'id'
            },{
                xtype: 'hiddenfield',
                name: 'zone_id'
            },{
                fieldLabel: '主域名',
                xtype: 'hiddenfield',
                name: 'zone_name',
                allowBlank:false,
                editAble:false
            },{
                fieldLabel: '主机记录',
                name: 'name',
                allowBlank: false
            },{
                fieldLabel: '记录值',
                name: 'address',
                allowBlank:false
            },{
                fieldLabel:'类型',
                name:'type',
                allowBlank:false,
                xtype: 'combo',
                store: Ext.create('Ext.data.ArrayStore', {
                    fields: ['value'],
                    data: [['A'],['CNAME'],['MX'],['NS'],['TXT'],['AAAA'],['SRV'],['URL']]
                }),
                displayField: 'value',
                valueField: 'value',
                queryMode: 'local',
                editable: false,
                value:'A'
            } ,{
                fieldLabel: 'Priority',
                name: 'priority',
                value:10,
                allowBlank:false
            },{
                fieldLabel: 'active',
                allowBlank:false,
                xtype: 'radiogroup',
                items: [{
                    boxLabel: '是',
                    name: 'active',
                    inputValue:1,
                    checked:true
                },
                {
                    boxLabel: '否',
                    name: 'active',
                    inputValue:0
                }]
            },{
                fieldLabel: '描述',
                xtype:'textarea',
                name: 'description'
            }]
        }],
        buttons : [
        {
            text:'删除',
            handler:function(button){
                var win = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Idc.domain.DomainRecordPanel.getSelectionModel().getSelection()[0];
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/idc/domain/jsondeldomainrecord',
                            params:{
                                'id':node.data.id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                            }
                        });
                        //删除之后选中第一个节点
                        Idc.domain.DomainRecordStore.load();
                        Idc.domain.DomainRecordPanel.getSelectionModel().select(0);
                        var record = Idc.domain.DomainRecordPanel.getSelectionModel().getSelection()[0];
                        Idc.domain.DomainRecordPanel.fireEvent('itemclick', '',record, 0);
                        win.close();
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
                        url: '/idc/domain/jsondomainrecordsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            win.hide();
                            //刷新树
                            Idc.domain.DomainRecordPanel.getStore().load();
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
    Idc.domain.DomainZonePanel = Ext.create('Ext.tree.Panel',{
        title : '域名',
        collapsible:true,
        region:'west',
        width:200,
        minWidth:100,
        maxWidth:350,
        border:0,
        useArrows: true,
        rootVisible: true,
        store: {
            xtype:'store',
            proxy: {
                type: 'ajax',
                url: '/idc/domain/jsondomainzonetree'
            },
            folderSort: true
        },
        multiSelect: false,
        singleExpand: false,
        root: {
            text: 'ComsenzIDC',
            id: 'root',
            expanded: true
        },
        tbar:[{
            text:'添加',
            iconCls: 'icon-add',
            handler:function(){
                Idc.domain.DomainZoneFormWindow.setTitle('添加域名');
                Idc.domain.DomainZoneFormWindow.down('button[text="删除"]').hide();
                Idc.domain.DomainZoneFormWindow.down('form').getForm().reset();
                Idc.domain.DomainZoneFormWindow.show();

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
                Idc.domain.currentDomainZone = record;
                Idc.domain.currentDomainZoneIndex = index;
                Idc.domain.CurrentDomainZoneId = record.data.id;
                if(!record.data.root){
                    Idc.domain.DomainRecordStore.getProxy().url = '/idc/domain/jsondomainrecordlist/domainId/'+record.data.id;
                    Idc.domain.DomainRecordStore.load();
                }
            },
            selectionchange:function(view,selects,options){
            },
            load:function(){
                //选中上次选中的节点，默认选中第一个节点
                var id,record;
                var root = Idc.domain.DomainZonePanel.getStore().getRootNode();
                if(Idc.domain.currentDomainZoneIndex){
                    id = Idc.domain.currentDomainZone.data.id;
                    record = root.findChild('id',id,true);
                }else{
                    record = root;
                }

                Idc.domain.DomainZonePanel.getSelectionModel().select(record);
                Idc.domain.DomainZonePanel.fireEvent('itemclick', this,record);
            },
            itemdblclick:function(view,record,item,index,event,options){
                if(!record.data.root){
                    var form = Idc.domain.DomainZoneFormWindow.down('form');
                    Idc.domain.DomainZoneFormWindow.setTitle('修改域名');
                    Idc.domain.DomainZoneFormWindow.down('button[text="删除"]').show();
                    Idc.domain.DomainZoneStore.load();
                    var domainzoneData = Idc.domain.DomainZoneStore.findRecord('id',record.data.id);
                    var tdata;
                    if(domainzoneData){
                        tdata = domainzoneData.data;
                    }
                    form.getForm().setValues(tdata);
                    Idc.domain.DomainZoneFormWindow.show();
                }
            }
        }
    });
    /****************************************************************************************/
    Idc.domain.DomainRecordPanel = Ext.create('Ext.grid.Panel',{
        title : '域名记录',
        border:0,
        region:'center',
        cmargins:'5 0 0 0',
        store : Idc.domain.DomainRecordStore,
        multiSelect: false,
        columns: [{
            text: '记录',
            sortable: true,
            dataIndex: 'name'
        },{
            text: '类型',
            dataIndex: 'type',
            width:60,
            sortable: true
        },{
            text: '记录值',
            width:150,
            dataIndex: 'address'
        },{
            text: '优先级',
            width:45,
            dataIndex: 'priority'
        },{
            text: '状态',
            width:40,
            dataIndex: 'active'
        },{
            text: '备注',
            flex:1,
            dataIndex: 'description'
        }],
        tbar:[{
            text:'添加',
            iconCls: 'icon-add',
            handler:function(){
                var zoneNode = Idc.domain.DomainZonePanel.getSelectionModel().getSelection()[0];
                if(zoneNode){
                    var domainId = zoneNode.data.id;
                    Idc.domain.DomainRecordFormWindow.setTitle('添加域名');
                    Idc.domain.DomainRecordFormWindow.down('button[text="删除"]').hide();
                    var form = Idc.domain.DomainRecordFormWindow.down('form').getForm();
                    form.reset();
                    //设置表单初始值
                    var zone = Idc.domain.DomainZoneStore.findRecord('id',domainId).data;
                    form.setValues({
                        zone_id:zone.id,
                        zone_name:zone.name
                    });
                    Idc.domain.DomainRecordFormWindow.show();

                }else{
                    Ext.Msg.alert('错误','请先选择一个域名');
                }
            }
        },{
            iconCls: 'icon-image',
            text:'DNS 关系图',
            handler : function(button) {
                button.el.insertHtml(
                    'beforeBegin',
                    '<form action="/idc/domain/image/id/'+Idc.domain.CurrentDomainZoneId+'/t'+(new Date().getTime())+'" target="_blank" method="get" style="display:none"></form>'
                    ).submit();
            }
        }],
        listeners:{
            selectionchange:function(view,selects,options){

            },
            itemdblclick:function(view,record,item,index,e,options){
                var form = Idc.domain.DomainRecordFormWindow.down('form');
                form.getForm().setValues(record.data);
                Idc.domain.DomainRecordFormWindow.down('button[text="删除"]').show();
                Idc.domain.DomainRecordFormWindow.show();

            }
        }
    });
    /****************************************************************************************/
    Idc.domain.MainPanel = Ext.create('Ext.panel.Panel', {
        renderTo:'MainPanel',
        height:650,
        width:990,
        layout:'border',
        defaults:{
            collapsible:true,
            split:true
        },
        items:[
        Idc.domain.DomainZonePanel,
        Idc.domain.DomainRecordPanel
        ]
    });

});