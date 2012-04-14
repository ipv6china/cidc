Ext.Loader.setConfig({
    enabled: true
});

Ext.Loader.setPath('Ext.ux', '../js/ext/examples/ux');

Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.util.*'
    ]);
Ext.ns('Idc','Idc.network');

Ext.onReady(function(){
    /****************************************************************************************/
    Idc.network.NetworkStore = Ext.create('Ext.data.Store',{
        fields: ['id','network', 'network_string','netmask','netmask_string','parent','description'],
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: '/idc/network/jsonlist',
            reader:{
                type:'json',
                root:'rows'
            }
        }
    });
    /****************************************************************************************/
    Idc.network.IpGraphicStore = Ext.create('Ext.data.Store',{
        fields: ['ip','color'],
        autoLoad:false,
        proxy: {
            type: 'ajax',
            url: '/idc/ip/jsonipgraphic/network/124.238.252.0/netmask/255.255.255.0',
            reader:{
                type:'json'
            }
        }
    });
    /****************************************************************************************/
    Idc.network.IpStore = Ext.create('Ext.data.Store',{
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
    //网络Form
    Idc.network.NetworkFormWindow = Ext.create('Ext.window.Window',{
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
            items: [
            {
                xtype: 'hiddenfield',
                name: 'id'
            },{
                fieldLabel: 'Network',
                name: 'network_string',
                allowBlank:false
            },{
                fieldLabel: 'NetMask',
                name: 'netmask_string',
                value:'255.255.255.0',
                allowBlank:false
            },{
                name : 'parent',
                fieldLabel: '父节点',
                xtype: 'treecombobox',
                treeUrl:'/idc/network/jsontree',
                valueField: 'id',
                displayField: 'text'
            },{
                fieldLabel: '备注',
                xtype:'htmleditor',
                name: 'description'
            }]
        }],
        buttons : [
        {
            text:'<font color="red">删除</font>',
            handler:function(button){
                var win = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Idc.network.NetworkPanel.getSelectionModel().getSelection()[0];
                        var id = node.data.id;
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/idc/network/jsondel',
                            params:{
                                'id':id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                                node.remove();
                            }
                        });
                        //删除之后选中第一个节点
                        Idc.network.NetworkPanel.getSelectionModel().select(2);
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
                        url: '/idc/network/jsonsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            win.hide();
                            //刷新树
                            Idc.network.NetworkPanel.getRootNode().removeAll();
                            Idc.network.NetworkPanel.getStore().load();
                            Idc.network.NetworkPanel.getView().refresh();
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
    Idc.network.NetworkPanel =  Ext.create('Ext.tree.Panel', {
        rootVisible: false,
        form:null,
        title:'网络',
        collapsible:true,
        region:'north',
        height:500,
        border:0,
        store: Ext.create('Ext.data.TreeStore',{
            root: {
                text: '网络',
                id: "0",
                expanded: true
            },
            proxy: {
                type: 'ajax',
                url: '/idc/network/jsontree'
            }
        }),
        tools : [{
            type:'plus',
            handler:function(e,toolel,panel){
                var form =  Idc.network.NetworkFormWindow.down('form');
                form.getForm().reset();
                var selections = Idc.network.NetworkPanel.getSelectionModel().getSelection();
                if(selections.length > 0){
                    var node = selections[0];
                    var id = node.data.id;
                    form.getForm().setValues({
                        'parent':id
                    });
                }
                Idc.network.NetworkFormWindow.show();
            }
        },{
            type : 'refresh',
            qtip : '刷新',
            handler : function(e,toolel,panel) {
                var tree = panel.up('treepanel');
                tree.getStore().load();
            }
        }],
        listeners:{
            itemclick: function(view, record, item, index, e)
            {
                var networkId = record.data.id;
                var data = Idc.network.NetworkStore.findRecord('id',networkId).data;
                Idc.network.IpStore.getProxy().url = '/idc/ip/jsonallips/network/'+data.network_string+
                '/netmask/'+data.netmask_string;
                Idc.network.IpStore.load();

                Idc.network.NetworkGraphicPanel.update('');
                Idc.network.IpGraphicStore.getProxy().url = '/idc/ip/jsonipgraphic/network/'+data.network_string+
                '/netmask/'+data.netmask_string;
                Idc.network.IpGraphicStore.load();
            },
            selectionchange:function(view,selects,options){
                if(selects.length > 0){
                    var networkId = selects[0].data.id;
                    var networkRecord = Idc.network.NetworkStore.findRecord('id',networkId);
                    if(networkRecord){
                        var networkData = networkRecord.data;
                        Idc.network.NetworkDetailPanel.update(networkData.description);
                    }
                }

            },
            itemdblclick:function(view,record,item,index,event,options){
                if(!record.data.root){
                    var form = Idc.network.NetworkFormWindow.down('form');
                    Idc.network.NetworkFormWindow.setTitle('修改');
                    Idc.network.NetworkStore.load();
                    var nData = Idc.network.NetworkStore.findRecord('id',record.data.id).data;
                    form.getForm().setValues(nData);
                    Idc.network.NetworkFormWindow.show();

                }
            }
        }
    });
    /****************************************************************************************/
    Idc.network.IpPanel = Ext.create('Ext.grid.Panel', {
        title : '地址池',
        border:0,
        region:'center',
        cmargins:'5 0 0 0',
        store : Idc.network.IpStore,
        multiSelect: false,
        columns: [
        {
            text: 'IP 地址',
            width:130,
            xtype:'templatecolumn',
            tpl:'{ip_string}/{netmask}'
        },{
            text: '主机名',
            flex: 1,
            xtype:'templatecolumn',
            tpl:'<a href="/idc/equipment/info/type/{equipment_type}/id/{equipment_id}" target="_blank">{name}</a>'
        },{
            text: '登录',
            width:130,
            xtype:'templatecolumn',
            tpl:'<a href="#" onclick=\'runPutty("{ip_string}")\'>登录</a>'
        }],
        constructor: function(config) {
            if (config == null)
                config = {};
            Ext.apply(this, config);
            this.callParent([config = config || {}]);
        }
    });
    /****************************************************************************************/
    Idc.network.NetworkGraphicPanel = Ext.create('Ext.panel.Panel',{
        region: 'south',
        title:'IP图表',
        autoScroll: true,
        border:0,
        bodyPadding:'10 5 5 30',
        bodyStyle: 'background:#fafafa;',
        height:260,
        html:'',
        items: {
            xtype:'dataview',
            store:Idc.network.IpGraphicStore,
            tpl: [
            '<tpl for=".">',
            '<div class="ip-color-{color}">{ip}</div>',
            '</tpl>'
            ],
            itemSelector: 'div.ip-color-red'
        }
    });
    /****************************************************************************************/
    Idc.network.NetworkDetailPanel = Ext.create('Ext.panel.Panel',{
        region: 'center',
        title:'详情',
        autoScroll: true,
        border:0,
        bodyPadding:5,
        bodyStyle: 'background:#fafafa;',
        html:''
    });
    /****************************************************************************************/
    //左侧导航
    Idc.network.WestPanel = Ext.create('Ext.panel.Panel',{
        layout: 'border',
        region:'west',
        width:200,
        minWidth:100,
        maxWidth:550,
        items:[Idc.network.NetworkPanel,Idc.network.NetworkDetailPanel]
    });
    /****************************************************************************************/
    //中间部分
    Idc.network.CenterPanel = Ext.create('Ext.panel.Panel',{
        layout: 'border',
        region:'center',
        items:[Idc.network.IpPanel,Idc.network.NetworkGraphicPanel]
    });
    /****************************************************************************************/
    Idc.network.MainPanel = Ext.create('Ext.panel.Panel', {
        renderTo:'MainPanel',
        bodyPadding:5,
        height:650,
        width:990,
        layout:'border',
        defaults:{
            collapsible:true,
            split:true
        },
        items:[
        Idc.network.WestPanel,
        Idc.network.CenterPanel
        ],
        constructor: function(config) {
            config = Ext.apply({}, config);
            this.callParent([config = config || {}]);
        }
    });

});