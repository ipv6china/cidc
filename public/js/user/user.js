Ext.onReady(function(){
    Ext.ns('User','User.user');

    User.user.UserStore = Ext.create('Ext.data.Store',{
        fields: [
        'id',
        'username',
        'department',
        'realname',
        'password',
        'mobilephone',
        'email',
        'rtx',
        'qq',
        'status',
        'issuperadmin',
        'allow_admin_user',
        {
            name:'last_login_time',
            type:'date',
            dateFormat:'timestamp'
        },
        'last_login_ip',
        'logintimes',
        'description'],
        autoLoad:true,
        totalProperty: 'totalCount',
        idProperty: 'id',
        pageSize:25,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: '/user/user/jsonlist',
            reader:{
                type:'json',
                root:'rows'
            }
        }
    });

    User.user.MainPanel = Ext.create('Ext.grid.Panel', {
        extend: 'Ext.grid.Panel',
        renderTo:'MainPanel',
        title : '用户管理',
        store : User.user.UserStore,
        multiSelect: false,
        columns: [{
            text: '用户名',
            sortable: true,
            dataIndex: 'username'
        },{
            text: '姓名',
            dataIndex: 'realname',
            sortable: true
        },{
            text: 'Email',
            dataIndex: 'email',
            width:200
        },{
            text: '部门',
            width:145,
            dataIndex: 'department'
        },{
            text: '最后登陆时间',
            dataIndex: 'last_login_time',
            xtype:'datecolumn',
            width:145,
            format:'Y-m-d H:i:s'
        },{
            text: '备注',
            flex:1,
            dataIndex: 'description'
        }],
        dockedItems:[{
            xtype: 'pagingtoolbar',
            store: User.user.UserStore,
            displayInfo : true,
            dock: 'bottom',
            displayMsg : '第{0} 到 {1} 条数据 共{2}条'
        }]
    });


});