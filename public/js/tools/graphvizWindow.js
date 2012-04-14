Ext.onReady(function(){
    Ext.ns('Tools','Tools.graphviz');
    /****************************************************************************************/
    Tools.graphviz.GraphAttrWindow = Ext.create('Ext.window.Window',{
        title : '图扩展属性',
        layout: 'fit',
        autoShow: false,
        width: 480,
        height:500,
        closeAction:'hide',
        modal:false,
        items:[{
            xtype:'propertygrid',
            border:0,
            nameColumnWidth:160,
            customEditors:{
                color: {
                    xtype:'colorfield'
                },
                bgcolor:{
                    xtype:'colorfield'
                },
                ratio:{
                    xtype:'combo',
                    editable: true,
                    store: [[ 'auto', 'Auto - 自动' ],[ 'compress', 'Compress - 压缩' ], ['fill', 'Fill - 填充' ]]
                },
                rankdir:{
                    xtype:'combo',
                    editable: false,
                    store: [[ 'LR', 'LR' ], ['RL', 'RL' ], ['BT', 'BT' ]]
                },
                splines:{
                    xtype:'combo',
                    editable: false,
                    store: [[ 'true', 'TRUE' ], ['false', 'FALSE' ], ['line', 'Line' ]]
                }
            },
            customRenderers: {
                color: function(v){
                    return '<span style="color: '+v+';">'+v+'</span>';
                },
                bgcolor: function(v){
                    return '<span style="background-color: '+v+';">'+v+'</span>';
                }
            },
            propertyNames: {
                color: 'color - 前景色',
                bgcolor: 'bgcolor -背景色',
                size:'size - 尺寸(宽,高)',
                ratio:'ratio - 纵横比',
                margin:'margin - 边距',
                nodesep:'nodesep - 节点纵向间隔',
                ranksep:'ranksep - 节点横向间隔',
                rankdir:'rankdir - 绘图方向',
                rotate:'rotate - 旋转',
                href:'href - 超链接',
                splines:'splines - 曲线'
            },
            source:{
                'color':'',
                'bgcolor':'',
                'size':'',
                'ratio':'',
                'margin':'',
                'nodesep':'',
                'ranksep':'',
                'rankdir':'',
                'rotate':'',
                'href':'',
                'splines':''

            }
        }],
        buttons : [
        {
            text: '保存',
            handler:function(button){
                var win    = button.up('window'),
                grid   = win.down('propertygrid');
                var gid = Tools.graphviz.currentGid;
                if(gid){
                    Ext.Ajax.request({
                        method:'POST',
                        url: '/tools/graphviz/jsongraphsaveattr',
                        params:{
                            'id':gid,
                            'attrs':Ext.encode(grid.getSource())
                        },
                        success: function(response) {
                            var result = Ext.decode(response.responseText);
                            Etao.msg.info('success',result.msg);
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
    Tools.graphviz.EdgeAttrWindow = Ext.create('Ext.window.Window',{
        title : '边扩展属性',
        layout: 'fit',
        autoShow: false,
        width: 480,
        height:500,
        closeAction:'hide',
        modal:false,
        items:[{
            xtype:'propertygrid',
            border:0,
            nameColumnWidth:160,
            customEditors:{
                style:{
                    xtype:'combo',
                    editable: false,
                    store: [['solid', 'solid' ]
                    , ['dashed', 'dashed' ]
                    , ['dotted', 'dotted' ]
                    , ['bold', 'bold' ]
                    , ['invis', 'invis' ]]
                },
                dir:{
                    xtype:'combo',
                    editable: false,
                    store: [['forward', 'forward' ]
                    , ['back', 'back' ]
                    , ['both', 'both' ]
                    , ['none', 'none' ]]
                },
                tailfclip:{
                    xtype:'combo',
                    editable: false,
                    store: [[ 'true', 'TRUE' ], ['false', 'FALSE' ]]
                },
                headclip:{
                    xtype:'combo',
                    editable: false,
                    store: [[ 'true', 'TRUE' ], ['false', 'FALSE' ]]
                },
                arrowhead:{
                    xtype:'combo',
                    editable: false,
                    store: Tools.graphviz.ArrowStyle
                },
                arrowtail:{
                    xtype:'combo',
                    editable: false,
                    store: Tools.graphviz.ArrowStyle
                },
                decorate:{
                    xtype:'combo',
                    editable: false,
                    store: [[ 'true', 'TRUE' ], ['false', 'FALSE' ]]
                },
                color: {
                    xtype:'colorfield'
                },
                fontcolor: {
                    xtype:'colorfield'
                }
            },
            customRenderers: {
                color: function(v){
                    return '<span style="color: '+v+';">'+v+'</span>';
                },
                fontcolor: function(v){
                    return '<span style="color: '+v+';">'+v+'</span>';
                }
            },
            propertyNames: {
                color: 'color - 颜色',
                minlen: 'minlen - 最小长度',
                fontsize:'fontsize - 标签字号',
                fontname:'fontname - 标签字体',
                fontcolor:'fontcolor - 标签颜色',
                style:'style - 样式',
                dir:'dir - 箭头方向',
                tailfclip:'tailfclip - 不显示尾部箭头',
                headclip:'headclip - 不显示头部箭头',
                href:'href - 超链接',
                target:'target - 超链接窗口',
                tooltip:'tooltip',
                arrowhead:'arrowhead - 头部箭头',
                arrowtail:'arrowtail - 尾部箭头',
                arrowsize:'arrowsize - 箭头大小',
                headlabel:'headlabel - 头部标签',
                taillabel:'taillabel - 尾部标签',
                headhref:'headhref - 头部链接',
                tailhref:'tailhref - 尾部链接',
                headtarget:'headtarget',
                tailtarget:'tailtarget',
                headtooltip:'headtooltip',
                tailtooltip:'tailtooltip',
                labeldistance:'labeldistance - 标签间距',
                decorate:'decorate - 连接标签',
                w:'w - neato-specific',
                len:'len - neato-specific',
                weight:'weight - fdp-specific'
            },
            source:{
                'minlen':'',
                'fontsize':'',
                'fontname':'',
                'fontcolor':'',
                'style':'',
                'color':'',
                'dir':'',
                'tailclip':'',
                'headclip':'',
                'href':'',
                'target':'',
                'tooltip':'',
                'arrowhead':'',
                'arrowtail':'',
                'arrowsize':'',
                'headlabel':'',
                'taillabel':'',
                'headhref':'',
                'headtarget':'',
                'headtooltip':'',
                'tailhref':'',
                'tailtarget':'',
                'tailtooltip':'',
                'labeldistance':'',
                'port_label_distance':'',
                'decorate':'',
                'samehead':'',
                'sametail':'',
                'constraint':'',
                'w':'',
                'len':'',
                'weight':''
            }
        }],
        buttons : [
        {
            text: '保存',
            handler:function(button){
                var win    = button.up('window'),
                grid   = win.down('propertygrid');
                var selects = Tools.graphviz.EdgePanel.getSelectionModel().getSelection();
                if(selects.length == 0){
                    alert('请先选择一条边');
                    return;
                }
                var record = selects[0];
                var id = record.data.id;
                if(id){
                    Ext.Ajax.request({
                        method:'POST',
                        url: '/tools/graphviz/jsonedgesaveattr',
                        params:{
                            'id':id,
                            'attrs':Ext.encode(grid.getSource())
                        },
                        success: function(response) {
                            var result = Ext.decode(response.responseText);
                            Etao.msg.info('success',result.msg);
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
    Tools.graphviz.NodeAttrWindow = Ext.create('Ext.window.Window',{
        title : '节点扩展属性',
        layout: 'fit',
        autoShow: false,
        width: 480,
        height:500,
        closeAction:'hide',
        modal:false,
        items:[{
            xtype:'propertygrid',
            border:0,
            nameColumnWidth:160,
            customEditors:{
                style:{
                    xtype:'combo',
                    editable: true,
                    store: [[ 'filled', 'filled - 填充' ]
                    , ['solid', 'solid' ]
                    , ['dashed', 'dashed' ]
                    , ['dotted', 'dotted' ]
                    , ['bold', 'bold' ]
                    , ['invis', 'invis' ]]
                },
                shape:{
                    xtype:'combo',
                    editable: true,
                    store: [[ 'box', 'box - 矩形' ]
                    , ['polygon', 'polygon - 多边形' ]
                    , ['plaintext', 'plaintext - 文字' ]
                    , ['oval', 'oval - 椭圆形' ]
                    , ['circle', 'circle - 圆形' ]
                    , ['diamond', 'diamond - 菱形' ]
                    , ['triangle', 'triangle - 三角形' ]
                    , ['trapezium', 'trapezium - 梯形' ]
                    , ['egg', 'egg - 蛋形' ]
                    , ['parallelogram', 'parallelogram - 平行四边形' ]
                    , ['hexagon', 'hexagon - 六边形' ]
                    , ['octagon', 'octagon - 八角形' ]
                    , ['house', 'house' ]
                    , ['note', 'note' ]
                    , ['tab', 'tab' ]
                    , ['box3d', 'box' ]
                    , ['component', 'component' ]]
                },
                root:{
                    xtype:'combo',
                    editable: false,
                    store: [[ 'true', 'TRUE' ], ['false', 'FALSE' ]]
                },
                color: {
                    xtype:'colorfield'
                },
                fontcolor: {
                    xtype:'colorfield'
                },
                fillcolor: {
                    xtype:'colorfield'
                }
            },
            customRenderers: {
                color: function(v){
                    return '<span style="color: '+v+';">'+v+'</span>';
                },
                fontcolor: function(v){
                    return '<span style="color: '+v+';">'+v+'</span>';
                },
                fillcolor: function(v){
                    return '<span style="background-color: '+v+';">'+v+'</span>';
                }
            },
            propertyNames: {
                color: 'color - 边颜色',
                fillcolor: 'fillcolor - 填充颜色',
                style: 'style - 样式',
                shape: 'shape - 形状',
                sides: 'sides - 多边形边数',
                width: 'width - 宽度',
                height:'height - 高度',
                distortion:'distortion - 扭曲',
                href:'href - 超链接',
                peripheries:'peripheries - 边数 ',
                pin:'pin - fdp Only ',
                root:'root - circo Only '
            },
            source:{
                "color":'',
                "style":'',
                'height':'',
                'width':'',
                'shape':'',
                'fontsize':'',
                'fontname':'',
                'fillcolor':'',
                'fontcolor':'',
                'regular':'',
                'peripheries':'',
                'sides':'',
                'orientation':'',
                'distortion':'',
                'skew':'',
                'href':'',
                'target':'',
                'tooltip':'',
                'root':'',
                'pin':''
            }
        }],
        buttons : [
        {
            text: '保存',
            handler:function(button){
                var win    = button.up('window'),
                grid   = win.down('propertygrid');
                var selects = Tools.graphviz.NodePanel.getSelectionModel().getSelection();
                if(selects.length == 0){
                    alert('请先选择一个节点');
                    return;
                }
                var record = selects[0];
                var id = record.data.id;
                if(id){
                    Ext.Ajax.request({
                        method:'POST',
                        url: '/tools/graphviz/jsonnodesaveattr',
                        params:{
                            'id':id,
                            'attrs':Ext.encode(grid.getSource())
                        },
                        success: function(response) {
                            var result = Ext.decode(response.responseText);
                            Etao.msg.info('success',result.msg);
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
});