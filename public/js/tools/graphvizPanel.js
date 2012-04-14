Ext.onReady(function(){
    Ext.ns('Tools','Tools.graphviz');
    /****************************************************************************************/
    Tools.graphviz.GraphPanel = Ext.create('Ext.grid.Panel', {
        region:'west',
        width:160,
        title:'图',
        collapsible:true,
        store: Tools.graphviz.GraphStore,
        hideHeaders:true,
        columns: [{
            text: '名称',
            flex:1,
            dataIndex: 'name'
        }],
        tbar:[
        {
            iconCls: 'icon-add',
            text:'新建',
            menu : {
                items: [{
                    text:'普通模式',
                    handler:function(){
                        var win = Tools.graphviz.GraphFormWindow;
                        var form =  win.down('form');
                        win.setTitle('新建');
                        form.getForm().reset();
                        form.getForm().setValues({
                            });
                        win.down('button[text="保存"]').show();
                        win.down('button[text="取消"]').show();
                        win.down('button[text="删除"]').hide();
                        win.show();
                    }
                },{
                    text:'高级模式',
                    handler:function(){
                        var win = Tools.graphviz.GraphCodeFormWindow;
                        var form =  win.down('form');
                        win.setTitle('新建');
                        form.getForm().reset();
                        form.getForm().setValues({
                            });
                        win.down('button[text="保存"]').show();
                        win.down('button[text="取消"]').show();
                        win.down('button[text="删除"]').hide();
                        win.show();
                    }
                }]
            }

        },{
            iconCls: 'icon-import',
            text:'属性',
            handler:function(btn){
                var win = Tools.graphviz.GraphAttrWindow;
                var p = Tools.graphviz.CenterPanel.getPosition();
                win.setPosition(p[0],p[1]);
                var  grid   = win.down('propertygrid');
                var selects = Tools.graphviz.GraphPanel.getSelectionModel().getSelection();
                if(selects.length == 0){
                    alert('请先选择一个图');
                    return;
                }else{
                    var data = selects[0].data.attrs;
                    if(data.length > 0) {
                        grid.setSource(Ext.decode(data));
                    }
                    win.setTitle('编辑图属性');
                    win.show();
                }
            }
        },'->',{
            tooltip:'刷新',
            iconCls: 'icon-refresh',
            handler:function(btn){
                var panel = btn.up('gridpanel'),store;
                store = panel.getStore();
                store.load();
                Etao.msg.info('刷新成功','载入 '+ store.getTotalCount() + ' 条数据');
            }
        }
        ],
        dockedItems:[{
            xtype: 'pagingtoolbar',
            prependButtons:false,
            store: Tools.graphviz.GraphStore,
            dock: 'bottom',
            displayInfo : false,
            beforePageText:'',
            afterPageText:'',
            inputItemWidth:16
        }],
        listeners:{
            itemdblclick:function(view,record,item,index,e,options){
                var win = Tools.graphviz.GraphFormWindow;
                win.setTitle('编辑图 [普通模式]');
                if(parseInt(record.data.advanced) == 1){
                    win = Tools.graphviz.GraphCodeFormWindow;
                    win.setTitle('编辑图 [高级模式]');
                }
                var form = win.down('form');

                form.getForm().setValues(record.data);
                win.down('button[text="删除"]').show();
                win.show();
            },
            selectionchange:function(view,selections,options){
                if(selections.length > 0){
            }
            },
            itemclick: function(view, record, item, index, e){
                //点击节点时记录下节点和 index
                Tools.graphviz.currentGraphRecord = record;
                var gid = Tools.graphviz.currentGid = record.data.id;
                Tools.graphviz.functionUpdateImageAndcode();
                //选择节点时改变右侧Grid的内容

                Ext.apply(Tools.graphviz.EdgeStore.proxy.extraParams,{
                    gid:gid
                });
                Tools.graphviz.EdgeStore.load();
                Ext.apply(Tools.graphviz.NodeStore.proxy.extraParams,{
                    gid:gid
                });
                Tools.graphviz.NodeStore.load();

            }
        }
    });
    /****************************************************************************************/
    Tools.graphviz.NodePanel = Ext.create('Ext.grid.Panel', {
        title:'节点',
        collapsible:true,
        store: Tools.graphviz.NodeStore,
        hideHeaders:false,
        columns: [{
            text: '名称',
            width:80,
            dataIndex: 'name'
        },{
            text: '标签',
            flex:1,
            dataIndex: 'label'
        },{
            text:'引用',
            width:40,
            dataIndex:'use_count'
        }],
        tbar:[
        {
            iconCls: 'icon-add',
            text:'新建节点',
            handler:function(){
                var win = Tools.graphviz.NodeFormWindow;
                var form =  win.down('form');
                win.setTitle('新建节点');
                form.getForm().reset();
                form.getForm().setValues({
                    });
                win.down('button[text="保存"]').show();
                win.down('button[text="删除"]').hide();
                win.show();
            }
        },{
            iconCls: 'icon-import',
            text:'属性',
            handler:function(btn){
                var win = Tools.graphviz.NodeAttrWindow;
                var p = Tools.graphviz.MainPanel.getPosition();
                win.setPosition(p[0],p[1]);
                var  grid   = win.down('propertygrid');
                var selects = Tools.graphviz.NodePanel.getSelectionModel().getSelection();
                if(selects.length == 0){
                    alert('请先选择一个节点');
                    return;
                }else{
                    var data = selects[0].data.attrs;
                    if(data.length > 0) {
                        grid.setSource(Ext.decode(data));
                    }
                    win.setTitle('编辑节点属性');
                    win.show();
                }
            }
        },'->',{
            tooltip:'刷新',
            iconCls: 'icon-refresh',
            handler:function(btn){
                var panel = btn.up('gridpanel'),store;
                store = panel.getStore();
                store.load();
                Etao.msg.info('刷新成功','载入 '+ store.getTotalCount() + ' 条数据');
            }
        }
        ],
        dockedItems:[{
            xtype: 'pagingtoolbar',
            prependButtons:false,
            store: Tools.graphviz.NodeStore,
            dock: 'bottom',
            displayInfo : true
        }],
        listeners:{
            itemdblclick:function(view,record,item,index,e,options){
                var win = Tools.graphviz.NodeFormWindow;
                var form = win.down('form');
                win.setTitle('编辑节点');
                form.getForm().setValues(record.data);
                win.down('button[text="删除"]').show();
                win.show();
            },
            beforeactivate:function( panel, options ){
            // panel.getStore().load();
            },
            itemclick: function(view, record, item, index, e){
                //点击节点时记录下节点和 index
                Tools.graphviz.currentNodeRecord = record;
                Tools.graphviz.currentNid = record.data.id;
            }
        }
    });
    /****************************************************************************************/
    Tools.graphviz.EdgePanel = Ext.create('Ext.grid.Panel', {
        title:'关系',
        collapsible:true,
        store: Tools.graphviz.EdgeStore,
        hideHeaders:false,
        columns: [{
            text:'类型',
            dataIndex:'typeString',
            align:'center',
            width:50,
            sortable:false
        },{
            text: 'Node1',
            width:80,
            dataIndex: 'node1'
        },{
            text: 'Node2',
            width:100,
            dataIndex: 'node2'
        },{
            text: '标签',
            flex:1,
            dataIndex: 'label'
        }],
        tbar:[
        {
            iconCls: 'icon-add',
            text:'增加关系',
            handler:function(){
                var win = Tools.graphviz.EdgeFormWindow;
                var form =  win.down('form');
                win.setTitle('新建一条对应关系');
                form.getForm().reset();
                form.getForm().setValues({
                    'type':1,
                    'gid':Tools.graphviz.currentGid
                });
                var type = form.down('*[name=type]');
                type.show();
                win.down('button[text="保存"]').show();
                win.down('button[text="取消"]').show();
                win.down('button[text="删除"]').hide();
                win.show();
            }
        },{
            iconCls: 'icon-import',
            text:'属性',
            handler:function(btn){
                var win = Tools.graphviz.EdgeAttrWindow;
                var p = Tools.graphviz.MainPanel.getPosition();
                win.setPosition(p[0],p[1]);
                var  grid   = win.down('propertygrid');
                var selects = Tools.graphviz.EdgePanel.getSelectionModel().getSelection();
                if(selects.length == 0){
                    alert('请先选择一条边');
                    return;
                }else{
                    var data = selects[0].data.attrs;
                    if(data.length > 0) {
                        grid.setSource(Ext.decode(data));
                    }
                    win.setTitle('修改边属性');
                    win.show();
                }
            }
        },'->',{
            tooltip:'刷新',
            iconCls: 'icon-refresh',
            handler:function(btn){
                var panel = btn.up('gridpanel'),store;
                store = panel.getStore();
                store.load();
                Etao.msg.info('刷新成功','载入 '+ store.getTotalCount() + ' 条数据');
            }
        }
        ],
        dockedItems:[{
            xtype: 'pagingtoolbar',
            prependButtons:false,
            store: Tools.graphviz.EdgeStore,
            dock: 'bottom',
            displayInfo : true
        }],
        listeners:{
            itemdblclick:function(view,record,item,index,e,options){
                var win = Tools.graphviz.EdgeFormWindow;
                var form = win.down('form');
                var type = form.down('*[name=type]');
                type.hide();
                win.setTitle('编辑边 ['+ Tools.graphviz.functionTypeToString(record.data.type)+']');
                form.getForm().setValues(record.data);
                win.down('button[text="删除"]').show();
                win.show();
            },
            itemclick: function(view, record, item, index, e){
                //点击节点时记录下节点和 index
                Tools.graphviz.currentEdgeRecord = record;
                Tools.graphviz.currentEid = record.data.id;
            }
        }
    });
    /****************************************************************************************/
    Tools.graphviz.CodePanel = Ext.create('Ext.panel.Panel', {
        title:'代码',
        region:'south',
        height:200,
        bodyPadding:5,
        tbar:[{
            iconCls: 'icon-save',
            text:'保存',
            handler : function(button) {
                button.el.insertHtml(
                    'beforeBegin',
                    '<form action="/tools/graphviz/code/id/'+Tools.graphviz.currentGid+'/t'+(new Date().getTime())+'" target="_blank" method="get" style="display:none"></form>'
                    ).submit();
            }
        },'->',{
            iconCls: 'icon-refresh',
            tooltip:'刷新',
            handler:function(btn){
                var panel = btn.up('panel');
                Ext.Ajax.request({
                    method:'GET',
                    url: '/tools/graphviz/code',
                    params:{
                        'id':Tools.graphviz.currentGid
                    },
                    success: function(response) {
                        panel.update(response.responseText);
                    }
                });

            }
        }]
    });
    /****************************************************************************************/
    Tools.graphviz.ImagePanel = Ext.create('Ext.panel.Panel', {
        title:'图片',
        region:'center',
        collapsible:true,
        tbar:[{
            iconCls: 'icon-image',
            text:'打开',
            handler : function(button) {
                button.el.insertHtml(
                    'beforeBegin',
                    '<form action="/tools/graphviz/image/id/'+Tools.graphviz.currentGid+'/t'+(new Date().getTime())+'" target="_blank" method="get" style="display:none"></form>'
                    ).submit();
            }
        },'->',{
            iconCls: 'icon-refresh',
            tooltip:'刷新',
            handler:function(btn){
                var panel = btn.up('panel');
                panel.update('<a href="/tools/graphviz/image/id/'+Tools.graphviz.currentGid+'/t'+(new Date().getTime())+'" target="_blank">'
                    +'<img width="498" src="/tools/graphviz/image/id/'+Tools.graphviz.currentGid+'/t'+(new Date().getTime())+'" /></a>');
            }
        }]
    });
    /****************************************************************************************/
    Tools.graphviz.EastPanel = Ext.create('Ext.panel.Panel', {
        region:'east',
        //title:'预览',
        width:500,
        border:0,
        layout:'border',
        defaults:{
            collapsible:true,
            split:true,
            autoScroll: true
        },
        items:[Tools.graphviz.ImagePanel,Tools.graphviz.CodePanel]
    });
    /****************************************************************************************/
    Tools.graphviz.CenterPanel = Ext.create('Ext.tab.Panel', {
        region:'center',
        flex:1,
        items:[Tools.graphviz.EdgePanel,Tools.graphviz.NodePanel]
    });
    /****************************************************************************************/
    Tools.graphviz.MainPanel = Ext.create('Ext.panel.Panel', {
        renderTo:'MainPanel',
        height:650,
        bodyPadding:5,
        layout:'border',
        defaults:{
            collapsible:true,
            split:true,
            autoScroll: true
        },
        items:[Tools.graphviz.GraphPanel,Tools.graphviz.CenterPanel,Tools.graphviz.EastPanel]
    });
    /****************************************************************************************/
    Tools.graphviz.functionUpdateImageAndcode = function (){
        Tools.graphviz.ImagePanel.update('<a href="/tools/graphviz/image/id/'+Tools.graphviz.currentGid+'/t'+(new Date().getTime())+'" target="_blank">'
            +'<img width="498" src="/tools/graphviz/image/id/'+Tools.graphviz.currentGid+'/t'+(new Date().getTime())+'" /></a>');

        Ext.Ajax.request({
            method:'GET',
            url: '/tools/graphviz/code',
            params:{
                'id':Tools.graphviz.currentGid
            },
            success: function(response) {
                Tools.graphviz.CodePanel.update(response.responseText);
            }
        });
    };
    /****************************************************************************************/
    //初始化
    setTimeout(Tools.graphviz.functionUpdateImageAndcode,1000);
/****************************************************************************************/
});