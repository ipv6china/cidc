Ext.onReady(function(){
    Ext.ns('Idc','Idc.cabinet');

    var selectEquipmentWin;
    var cabinetAdminPanel;

    //机柜对象
    Ext.define('Idc.cabinet.CabinetObject', {
        extend: 'Ext.data.Model',
        fields: ['id','name','device_tag','height', 'position','cabinet_id','cabinet_name','equipment_type','equipment_id','equipment_height']
    });

    //机柜面板
    Ext.define('Idc.cabinet.CabinetPanel', {
        extend: 'Ext.grid.Panel',
        columnLines: true,
        loadMask: true,
        title : '机柜',
        height:280,
        width:320,
        stripeRows:true,
        columns: [
        Ext.create('Ext.grid.RowNumberer'),
        {
            text: "设备名称",
            flex: 1,
            sortable: true,
            xtype:'templatecolumn',
            tpl:'<a href="/idc/equipment/info/type/{equipment_type}/id/{equipment_id}" target="_blank">{name}</a>'
        },
        {
            text: "设备标签",
            flex:1,
            sortable: true,
            dataIndex: 'device_tag'
        }
        ],
        margin : '0 1 1 0',
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
        },
        constructor: function(config) {
            config = Ext.apply({}, config);
            this.callParent([config = config || {}]);
        }
    });

    //选择设备窗口
    Ext.define('Idc.cabinet.EquipmentChooseWindow', {
        extend: 'Ext.Window',
        title:'未入柜设备',
        width:600,
        height:500,
        closeAction:'hide',
        items:[new Ext.grid.Panel({
            columnLines: true,
            loadMask: true,
            stripeRows:true,
            border:0,
            columns: [
            Ext.create('Ext.grid.RowNumberer'),
            {
                text: "设备名称",
                flex: 1,
                xtype:'templatecolumn',
                tpl:'<a href="/idc/equipment/info/type/{equipment_type}/id/{equipment_id}" target="_blank">{name}</a>'
            },
            {
                text: "类型",
                width: 35,
                sortable: true,
                dataIndex: 'equipment_type'
            },
            {
                text: "高度",
                width: 35,
                sortable: true,
                dataIndex: 'height'
            }],
            store : Ext.create('Ext.data.Store',{
                model: Ext.define('EquipmentObject', {
                    extend: 'Ext.data.Model',
                    fields: ['id','name', 'height', 'type','equipment_id','equipment_type']
                }),
                storeId:'choseEquipmentStore',
                autoLoad:true,
                proxy: {
                    type: 'ajax',
                    url: '/idc/equipment/jsonfreeequipment',
                    reader:{
                        type:'json',
                        root:'rows'
                    }
                }
            })
        })],
        constructor: function(config) {
            config = Ext.apply({}, config);
            this.callParent([config = config || {}]);
        }
    });
    Ext.define('Idc.cabinet.CabinetRowPanel', {
        extend : 'Ext.Panel',
        layout : {
            type: 'hbox'
        },
        border:0,
        constructor: function(config) {
            config = Ext.apply({}, config);
            this.callParent([config = config || {}]);
        }
    });


    Ext.define('Idc.cabinet.CabinetAdminPanel', {
        extend: 'Ext.Panel',
        frame:false,
        width:990,
        height:650,
        autoScroll: true,
        bodyPadding:1,
        tbar:{
            items: [
            {
                xtype:'combo',
                hideLabel: true,
                store: {
                    xtype:'store',
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
                },
                typeAhead: true,
                queryMode: 'local',
                triggerAction: 'all',
                emptyText: '选择机房',
                selectOnFocus: true,
                width: 135,
                displayField:'name',
                valueField:'id',
                iconCls: 'no-icon',
                listeners: {
                    change:function(combo,newValue,oldValue,options){
                        cabinetAdminPanel.removeAll();
                        Ext.Ajax.request({
                            url: '/idc/cabinet/jsonlist/idc_id/'+newValue,
                            success: function createArray(response) {
                                var obj = Ext.JSON.decode(response.responseText);
                                cabinetAdminPanel.setValues(obj);
                            }
                        });
                    }
                }
            },'-',{
                text: '未入柜设备',
                handler: function(){
                    if(!selectEquipmentWin){
                        selectEquipmentWin = Ext.create('Idc.cabinet.EquipmentChooseWindow',{});
                    }
                    if(!selectEquipmentWin.isVisible()){
                        selectEquipmentWin.show();
                    }else{
                        selectEquipmentWin.hide();
                    }
                }
            },'-',{
                text:'导入服务器',
                handler:function(){
                    window.location = '/idc/machine/import';
                }
            },'->','主机名:',{
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
                hideLabel: true,
                displayField: 'name',
                typeAhead: false,
                queryMode: 'local',
                triggerAction: 'all',
                hideTrigger:true,
                emptyText: '按 主机名 查找',
                selectOnFocus: true,
                width: 135,
                iconCls: 'no-icon',
                listConfig: {
                    getInnerTpl: function() {
                        return '<div class="combo-search"><a class="search-item" href="/idc/equipment/info/type/{type}/id/{id}" target="_blank">' +
                        '<h3>{name}</h3>' +
                        'IP:{ip}, '+
                        '类型:{type}, '+
                        'ID:{id}, '+
                        'Height:{height}, '+
                        '{description}'+
                        '</a></div>';
                    }
                }
            },'-','IP:',{
                xtype:'combo',
                hideLabel: true,
                store: {
                    xtype:'store',
                    fields: ['id','eid','name', 'type','machine_id','interface','ip','ip_string','netmask','netmask_string'],
                    storeId:'choseEquipmentIPStore',
                    autoLoad:true,
                    proxy: {
                        type: 'ajax',
                        url: '/idc/ip/jsonallips',
                        reader:{
                            type:'json',
                            root:'rows'
                        }
                    }
                },
                displayField: 'ip_string',
                typeAhead: true,
                queryMode: 'local',
                triggerAction: 'all',
                emptyText: '按 IP 查找',
                selectOnFocus: true,
                width: 135,
                listConfig: {
                    loadingText: 'Searching...',
                    emptyText: 'No matching posts found.',
                    getInnerTpl: function() {
                        return '<div class="combo-search"><a class="search-item" href="/idc/equipment/info/type/{type}/id/{eid}" target="_blank">' +
                        '<h3>{ip_string}</h3><br />' +
                        '{name}' +
                        '{description}'+
                        '</a></div>';
                    }
                },
                iconCls: 'no-icon'
            }]
        },
        constructor: function(config) {
            config = Ext.apply({}, config);
            this.callParent([config = config || {}]);
        },
        setValues:function(obj){
            Etao.msg.info('提示', '正在载入机柜，请稍候');
            var its = [];
            for(i=0;i<obj.rows.length;i++){
                if(i%3 == 0){
                    $r = Ext.create('Idc.cabinet.CabinetRowPanel',{});
                    its.push($r);
                }
                $r.add(Ext.create('Idc.cabinet.CabinetPanel',{
                    title:obj.rows[i].name,
                    store : Ext.create('Ext.data.Store',{
                        model: 'Idc.cabinet.CabinetObject',
                        autoLoad:true,
                        storeId:'cabinet_'+obj.rows[i].id,
                        proxy: {
                            type: 'ajax',
                            url: '/idc/cabinet/jsoninfo/id/'+obj.rows[i].id,
                            reader:{
                                type:'json',
                                root:'rows'
                            }
                        },
                        listeners:{
                            add:function(store,records,index,options){
                                Ext.Ajax.request({
                                    url: '/idc/cabinet/jsonaddequipment'
                                    +'/cabinet_id/'+store.storeId.substr(8)
                                    +'/equipment_type/'+records[0].get('equipment_type')
                                    +'/position/'+(index+1)
                                    +'/equipment_id/'+records[0].get('equipment_id'),
                                    success: function createArray(response) {
                                    }
                                });


                            },
                            remove:function(store,record,index,options){
                                Ext.Ajax.request({
                                    url: '/cabinet/jsonremoveequipment/id/'+record.get('id'),
                                    success: function createArray(response) {
                                    }
                                });
                            }
                        }
                    })
                }));
            }
            this.add(its);
        }
    });

    cabinetAdminPanel = Ext.create('Idc.cabinet.CabinetAdminPanel',{
        renderTo:'cabinetList'
    });


    //构建机房1的机柜列表
    Ext.Ajax.request({
        url: '/idc/cabinet/jsonlist/idc_id/1',
        success: function createArray(response) {
            var obj = Ext.JSON.decode(response.responseText);
            cabinetAdminPanel.setValues(obj);
        }
    });

});