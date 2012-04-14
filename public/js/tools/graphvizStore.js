Ext.onReady(function(){
    Ext.ns('Tools','Tools.graphviz');
    /****************************************************************************************/
    Tools.graphviz.GraphStore = Ext.create('Ext.data.Store',{
        fields: ['id','name','type','label','file_format','directed','strict','attrs','advanced','code'],
        autoLoad:true,
        totalProperty: 'totalCount',
        idProperty: 'id',
        pageSize:25,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: '/tools/graphviz/jsongraphlist',
            reader:{
                type:'json',
                root:'rows'
            }
        },
        listeners:{
            load:function(store,records,success,operation,options){
                if(store.getTotalCount() > 0){
                    var record;
                    var panel = Tools.graphviz.GraphPanel;
                    if(!Ext.isEmpty(Tools.graphviz.currentGraphRecord)){
                        var data = Tools.graphviz.currentGraphRecord.data;
                        if(parseInt(data.id) >0){
                            record =  store.findRecord('id',data.id);
                            if(!Ext.isEmpty(record)){
                                panel.getSelectionModel().select(record);
                                panel.fireEvent('itemclick', '',record, 0);
                                return;
                            }
                        }
                    }
                    //默认选中id=1的节点
                    panel.getSelectionModel().select(0);
                    record = panel.getSelectionModel().getSelection()[0];
                    panel.fireEvent('itemclick', '',record, 0);
                }
            }
        }
    });
    /****************************************************************************************/
    Tools.graphviz.NodeStore = Ext.create('Ext.data.Store',{
        fields: ['id','gid','name','label','attrs','use_count'],
        autoLoad:false,
        totalProperty: 'totalCount',
        idProperty: 'id',
        pageSize:25,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: '/tools/graphviz/jsonnodelist',
            reader:{
                type:'json',
                root:'rows'
            }
        },
        listeners:{
            load:function(store,records,success,operation,options){
                if(store.getTotalCount() > 0){
                    var record;
                    var panel = Tools.graphviz.NodePanel;
                    if(!Ext.isEmpty(Tools.graphviz.currentNodeRecord)){
                        var data = Tools.graphviz.currentNodeRecord.data;
                        if(parseInt(data.id) > 0){
                            record =  store.findRecord('id',data.id);
                            if(!Ext.isEmpty(record)){
                                panel.getSelectionModel().select(record);
                                panel.fireEvent('itemclick', '',record, 0);
                                return;
                            }
                        }
                    }
                //默认选中id=1的节点
                //panel.getSelectionModel().select(0);
                //record = panel.getSelectionModel().getSelection()[0];
                //panel.fireEvent('itemclick', '',record, 0);
                }
            }
        }
    });
    /****************************************************************************************/
    Tools.graphviz.functionTypeToString = function(value){
        switch(parseInt(value)){
            case 1:
                return '一对一';
                break;
            case 2:
                return '一对多';
                break;
            case 3:
                return '链式';
                break;
            default:
                return '未知';
        }
    };
    /****************************************************************************************/
    Tools.graphviz.EdgeStore = Ext.create('Ext.data.Store',{
        fields: ['id','gid','type','node1','node2','label','attrs',{
            name:'typeString',
            convert:function(value,record){
                return Tools.graphviz.functionTypeToString(record.get('type'));
            }
        }],
        autoLoad:false,
        totalProperty: 'totalCount',
        idProperty: 'id',
        pageSize:25,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: '/tools/graphviz/jsonedgelist',
            reader:{
                type:'json',
                root:'rows'
            }
        },
        listeners:{
            load:function(store,records,success,operation,options){
                if(store.getTotalCount() > 0){
                    var record;
                    var panel = Tools.graphviz.EdgePanel;
                    if(!Ext.isEmpty(Tools.graphviz.currentEdgeRecord)){
                        var data = Tools.graphviz.currentEdgeRecord.data;
                        if(parseInt(data.id) >0){
                            record =  store.findRecord('id',data.id);
                            if(!Ext.isEmpty(record)){
                                panel.getSelectionModel().select(record);
                                panel.fireEvent('itemclick', '',record, 0);
                                return;
                            }
                        }
                    }
                    //默认选中id=1的节点
                    panel.getSelectionModel().select(0);
                    record = panel.getSelectionModel().getSelection()[0];
                    panel.fireEvent('itemclick', '',record, 0);
                }
            }
        }
    });
    /****************************************************************************************/
    Tools.graphviz.ArrowStyle = [
    [ 'none', 'none' ],
    ['normal', 'normal'],
    ['inv', 'inv'],
    ['dot', 'dot - 园'],
    ['odot', 'odot - 空心圆'],
    ['invdot', 'invdot'],
    ['invodot', 'invodot'],
    ['tee', 'tee'],
    ['empty', 'empty'],
    ['invempty', 'invempty'],
    ['open', 'open'],
    ['halfopen', 'halfopen'],
    ['diamond', 'diamond - 菱形'],
    ['odiamond', 'odiamond - 空心菱形'],
    ['box', 'box - 正方形'],
    ['obox', 'obox - 空心正方形'],
    ['crow', 'crow']
    ];
/****************************************************************************************/
});