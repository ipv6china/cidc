Ext.onReady(function(){
    Ext.QuickTips.init();
    Ext.ns('Tools','Tools.graphviz');
    /****************************************************************************************/
    Tools.graphviz.GraphCodeFormWindow =  Ext.create('Ext.window.Window',{
        title : '图',
        layout: 'fit',
        autoShow: false,
        width: 580,
        closeAction:'hide',
        modal:false,
        items:[{
            xtype: 'form',
            padding: '5 5 0 5',
            border: false,
            frame:false,
            style: 'background-color: #ffffff;',
            layout: 'anchor',
            defaults:{
                labelWidth:60,
                xtype: 'textfield',
                labelAlign:'top',
                allowBlank: true,
                anchor:'-10'
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            },{
                xtype: 'hiddenfield',
                name: 'advanced',
                value:1
            },{
                xtype: 'fieldcontainer',
                labelStyle: 'font-weight:bold;padding:0',
                layout: 'hbox',
                defaultType: 'textfield',
                fieldDefaults: {
                    labelAlign: 'top'
                },
                items: [{
                    fieldLabel:'布局引擎',
                    name:'type',
                    allowBlank:false,
                    xtype: 'combo',
                    store: Ext.create('Ext.data.ArrayStore', {
                        fields: ['value','text'],
                        data: [['dot','dot - 有向图'],['neato','neato - 无向图'],['twopi','twopi - 放射图'],['circo','circo - 环形图'],['fdp','fdp - Spring 模型无向图'],['sfdp','sfdp - Spring 模型无向图']]
                    }),
                    displayField: 'text',
                    valueField: 'value',
                    queryMode: 'local',
                    editable: false,
                    value:'dot'
                },{
                    fieldLabel:'图像格式',
                    margins: '0 0 0 5',
                    name:'file_format',
                    allowBlank:false,
                    xtype: 'combo',
                    store: Ext.create('Ext.data.ArrayStore', {
                        fields: ['value'],
                        data: [['svg'],['png'],['jpeg'],['pdf'],['tif'],['ico'],['plain']]
                    }),
                    displayField: 'value',
                    valueField: 'value',
                    queryMode: 'local',
                    editable: false,
                    value:'svg'
                }]
            },{
                name : 'name',
                fieldLabel: '名称',
                allowBlank:false
            },{
                xtype: 'textarea',
                name : 'code',
                height:500,
                allowBlank:false,
                fieldLabel: '代码'
            },{
                xtype:'displayfield',
                value:'高级模式下，生成图像时，只识别代码框里的代码'
            }
            ]
        }],
        tbar:['->',{
            text:'转换为 【普通模式】',
            handler:function(button){
                var win    = button.up('window');
                Ext.MessageBox.confirm('确认', '转换为 【普通模式】,您当前输入的代码将全部丢失，您确定要这样做吗？', function(btn){
                    if('yes' == btn){
                        var node = Tools.graphviz.GraphPanel.getSelectionModel().getSelection()[0];
                        var id = node.data.id;
                        Ext.Ajax.request({
                            method:'POST',
                            url: '/tools/graphviz/jsongraphsave',
                            params:{
                                'id':id,
                                'advanced':0
                            },
                            success: function(response) {
                                Etao.msg.info('success','转换成功');
                                Tools.graphviz.GraphStore.load();
                            }
                        });
                    }
                    win.hide();
                });
            }
        }],
        buttons : [{
            text:'删除',
            handler:function(button){
                var win    = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Tools.graphviz.GraphPanel.getSelectionModel().getSelection()[0];
                        var id = node.data.id;
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/tools/graphviz/jsongraphdel',
                            params:{
                                'id':id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                                Tools.graphviz.GraphStore.load();
                            }
                        });
                    }
                    win.hide();
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
                        url: '/tools/graphviz/jsongraphsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            //win.hide();
                            //form.getForm().reset();
                            Tools.graphviz.GraphStore.load();
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
    Tools.graphviz.GraphFormWindow =  Ext.create('Ext.window.Window',{
        title : '图',
        layout: 'fit',
        autoShow: false,
        width: 580,
        closeAction:'hide',
        modal:false,
        items:[{
            xtype: 'form',
            padding: '5 5 0 5',
            border: false,
            style: 'background-color: #fff;',
            layout: 'anchor',
            defaults:{
                labelWidth:60,
                xtype: 'textfield',
                labelAlign:'right',
                allowBlank: true,
                anchor:'-10'
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            },{
                xtype: 'hiddenfield',
                name: 'advanced',
                value:0
            },{
                xtype: 'radiogroup',
                fieldLabel: '严格模式',
                qtip:'严格模式下会合并两个节点间的相同连线',
                items: [
                {
                    boxLabel: '是',
                    name: 'strict',
                    inputValue: 1
                },
                {
                    boxLabel: '否',
                    name: 'strict',
                    inputValue: 0,
                    checked: true
                }
                ]
            },{
                xtype: 'radiogroup',
                fieldLabel: '图类型',
                items: [
                {
                    boxLabel: '无向图',
                    name: 'directed',
                    inputValue: 0
                },{
                    boxLabel: '有向图',
                    name: 'directed',
                    inputValue: 1,
                    checked: true
                }
                ]
            },{
                fieldLabel:'布局引擎',
                name:'type',
                allowBlank:false,
                xtype: 'combo',
                store: Ext.create('Ext.data.ArrayStore', {
                    fields: ['value','text'],
                    data: [['dot','dot - 有向图'],['neato','neato - 无向图'],['twopi','twopi - 放射图'],['circo','circo - 环形图'],['fdp','fdp - Spring 模型无向图'],['sfdp','sfdp - Spring 模型无向图']]
                }),
                displayField: 'text',
                valueField: 'value',
                queryMode: 'local',
                editable: false,
                value:'dot'
            },{
                fieldLabel:'图像格式',
                name:'file_format',
                allowBlank:false,
                xtype: 'combo',
                store: Ext.create('Ext.data.ArrayStore', {
                    fields: ['value'],
                    data: [['svg'],['png'],['jpeg'],['pdf'],['tif'],['ico'],['plain']]
                }),
                displayField: 'value',
                valueField: 'value',
                queryMode: 'local',
                editable: false,
                value:'svg'
            },{
                name : 'name',
                fieldLabel: '名称',
                allowBlank:false
            },{
                name : 'label',
                xtype: 'textarea',
                fieldLabel: '标签'
            },{
                xtype: 'textarea',
                name : 'attrs',
                fieldLabel: '属性'
            }
            ]
        }],
        buttons : [{
            text:'删除',
            handler:function(button){
                var win    = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Tools.graphviz.GraphPanel.getSelectionModel().getSelection()[0];
                        var id = node.data.id;
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/tools/graphviz/jsongraphdel',
                            params:{
                                'id':id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                                Tools.graphviz.GraphStore.load();
                            }
                        });
                    }
                    win.hide();
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
                        url: '/tools/graphviz/jsongraphsave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            //win.hide();
                            //form.getForm().reset();
                            Tools.graphviz.GraphStore.load();
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
        }]
        ,
        tbar:['->',{
            text:'转换为 【高级模式】',
            handler:function(button){
                var win    = button.up('window');
                Ext.MessageBox.confirm('确认', '转换为 【高级模式】后,针对“关系”和“节点”的编辑将全部失效，您确定要这样做吗？', function(btn){
                    if('yes' == btn){
                        var node = Tools.graphviz.GraphPanel.getSelectionModel().getSelection()[0];
                        var id = node.data.id;
                        Ext.Ajax.request({
                            method:'POST',
                            url: '/tools/graphviz/jsongraphsave',
                            params:{
                                'id':id,
                                'advanced':1
                            },
                            success: function(response) {
                                Etao.msg.info('success','转换成功');
                                Tools.graphviz.GraphStore.load();
                            }
                        });
                    }
                    win.hide();
                });
            }
        }]
    });
    /****************************************************************************************/
    Tools.graphviz.NodeFormWindow =  Ext.create('Ext.window.Window',{
        title : '节点',
        layout: 'fit',
        autoShow: false,
        width: 580,
        closeAction:'hide',
        modal:false,
        items:[{
            xtype: 'form',
            padding: '5 5 0 5',
            border: false,
            style: 'background-color: #fff;',
            layout: 'anchor',
            defaults:{
                labelWidth:60,
                xtype: 'textfield',
                labelAlign:'right',
                allowBlank: true,
                anchor:'-10'
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            },{
                xtype: 'hiddenfield',
                name: 'gid'
            },{
                name : 'name',
                fieldLabel: '名称',
                allowBlank:false
            },{
                name : 'label',
                xtype: 'textarea',
                fieldLabel: '标签'
            },{
                xtype: 'textarea',
                name : 'attrs',
                fieldLabel: '属性'
            }
            ]
        }],
        buttons : [{
            text:'删除',
            handler:function(button){
                var win    = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Tools.graphviz.NodePanel.getSelectionModel().getSelection()[0];
                        var id = node.data.id;
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/tools/graphviz/jsonnodedel',
                            params:{
                                'id':id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                                Tools.graphviz.NodeStore.load();
                            }
                        });
                    }
                    win.hide();
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
                        url: '/tools/graphviz/jsonnodesave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            //win.hide();
                            //form.getForm().reset();
                            Tools.graphviz.EdgeStore.load();
                            Tools.graphviz.NodeStore.load();
                            Tools.graphviz.functionUpdateImageAndcode();
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
    Tools.graphviz.EdgeFormWindow =  Ext.create('Ext.window.Window',{
        title : '边',
        layout: 'fit',
        autoShow: false,
        width: 580,
        closeAction:'hide',
        modal:false,
        items:[{
            xtype: 'form',
            padding: '5 5 0 5',
            border: false,
            style: 'background-color: #fff;',
            layout: 'anchor',
            defaults:{
                labelWidth:60,
                xtype: 'textfield',
                labelAlign:'right',
                allowBlank: true,
                anchor:'-10'
            },
            items: [{
                xtype: 'hiddenfield',
                name: 'id'
            },{
                xtype: 'hiddenfield',
                name: 'gid'
            },{
                fieldLabel:'类型',
                xtype: 'combo',
                name:'type',
                store: Ext.create('Ext.data.ArrayStore', {
                    fields: ['value','text' ],
                    data: [
                    [1,'一对一'],
                    [2,'一对多'],
                    [3,'链式']
                    ]
                }),
                displayField: 'text',
                valueField: 'value',
                queryMode: 'local',
                editable: false
            } ,{
                name : 'node1',
                fieldLabel: '节点1',
                allowBlank:false
            },{
                name : 'node2',
                allowBlank:false,
                fieldLabel: '节点2'
            },{
                name : 'label',
                xtype: 'textarea',
                fieldLabel: '标签'
            },{
                xtype: 'textarea',
                name : 'attrs',
                fieldLabel: '属性'
            }
            ]
        }],
        buttons : [{
            text:'删除',
            handler:function(button){
                var win    = button.up('window');
                Ext.MessageBox.confirm('确认', '删除后不能恢复，您确定要删除该条记录吗？', function(btn){
                    if('yes' == btn){
                        var node = Tools.graphviz.EdgePanel.getSelectionModel().getSelection()[0];
                        var id = node.data.id;
                        Ext.Ajax.request({
                            method:'GET',
                            url: '/tools/graphviz/jsonedgedel',
                            params:{
                                'id':id
                            },
                            success: function(response) {
                                Etao.msg.info('success',response.responseText);
                                Tools.graphviz.EdgeStore.load();
                            }
                        });
                    }
                    win.hide();
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
                        url: '/tools/graphviz/jsonedgesave',
                        submitEmptyText: false,
                        waitMsg: 'Saving Data...',
                        success: function(f, action) {
                            Etao.msg.info('Success', action.result.msg);
                            //win.hide();
                            //form.getForm().reset();
                            Tools.graphviz.EdgeStore.load();
                            Tools.graphviz.functionUpdateImageAndcode();
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
});