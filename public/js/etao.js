/****************************************************************************************/
Ext.ns('Etao','Etao.msg');
/****************************************************************************************/
Etao.msg.info = function(title, content){
    if(!Etao.msg.msgCt){
        Etao.msg.msgCt = Ext.core.DomHelper.insertFirst(document.body, {
            id:'msg-div'
        }, true);
    }
    var s = Ext.String.format.apply(String, Array.prototype.slice.call(arguments, 1));
    var m = Ext.core.DomHelper.append(Etao.msg.msgCt, '<div class="msg"><h3>' + title + '</h3><p>' + s + '</p></div>', true);
    m.hide();
    m.slideIn('t').ghost("t", {
        delay: 3000,
        remove: true
    });
};
/****************************************************************************************/
Ext.define('Ext.ux.form.TreeComboBox', {
    extend : 'Ext.form.field.Picker',
    alias : [ 'widget.treecombobox', 'widget.treecombo' ],
    valueField : 'id',
    displayField : 'text',
    editable:false,
    rootVisible : false,
    folderSelectAble:true,//只能选择叶子节点
    initComponent : function() {
        this.addEvents('itemclick');
        Ext.apply(this, {
            pickerAlign : 'tl-bl?'
        });
        //如果设置了 treeUrl 则自动建立 treestore
        if(this.treeUrl){
            this.store = Ext.create('Ext.data.TreeStore', {
                nodeParam: 'id',
                proxy: {
                    type: 'ajax',
                    url: this.treeUrl
                },
                root: {
                    id: '0',
                    text:  this.rootText ? this.rootText : 'root',
                    expanded: true
                }
            })
        }
        this.store.on({
            load:this.setTreeComboValue,
            scope:this
        });
        this.callParent();
    },
    setTreeComboValue:function(){
        var value = this.getValue();
        if(value){
            var root = this.store.getRootNode();
            var record = root.findChild(this.valueField,value,true);
            this.setRawValue(record.get(this.displayField));
        }
    },
    createPicker : function() {
        this.picker = Ext.create('Ext.tree.Panel', {
            height : 200,
            floating : true,
            cls:'x-combo-tree',
            focusOnToFront : false,
            shadow : 'sides',
            ownerCt : this.ownerCt,
            store : this.store,
            singleExpand : true,
            rootVisible : true,
            displayField : this.displayField,
            listeners:{
                itemclick : {
                    //选择树节点时设置 treecombo 的值
                    fn:function(view, record) {
                        if(this.fireEvent('itemclick',this, this.picker, record)===false){
                            return;
                        }
                        if(record.data.leaf==false && !this.folderSelectAble){
                            record.expand();
                            return;
                        }
                        this.isExpanded = false;
                        this.picker.hide();
                        var _value;
                        var _rawValue;
                        record.get(this.valueField).length > 0 ? _value = record.get(this.valueField) : _value =0;
                        record.get(this.displayField).length > 0 ? _rawValue = record.get(this.displayField) : _rawValue = 'Root';
                        this.setValue(_value);
                        this.setRawValue(_rawValue);
                    },
                    scope:this
                },
                show : {
                    //显示 treepanel 时根据 value 选中相应节点
                    fn:function(me) {
                        var value = this.getValue();
                        if(value){
                            var root = me.store.getRootNode();
                            var record = root.findChild(this.valueField,value,true);
                            me.getSelectionModel().select(record);
                            record && this.setRawValue(record.get(this.displayField));
                        }
                    },
                    scope:this
                }
            }
        });
        return this.picker;
    },
    alignPicker : function() {
        var picker, isAbove, aboveSfx = '-above';
        if (this.isExpanded) {
            picker = this.getPicker();
            if (this.matchFieldWidth) {
                picker.setWidth(this.bodyEl.getWidth());
            }
            if (picker.isFloating()) {
                picker.alignTo(this.inputEl, this.pickerAlign,
                    this.pickerOffset);
                isAbove = picker.el.getY() < this.inputEl.getY();
                this.bodyEl[isAbove ? 'addCls' : 'removeCls'](this.openCls
                    + aboveSfx);
                picker.el[isAbove ? 'addCls' : 'removeCls'](picker.baseCls
                    + aboveSfx);
            }
        }
    },
    clearValue : function() {
        this.setValue( []);
    },
    setValue : function(value) {
        var me = this;
        me.callParent(arguments);
        if(value){
            //设置 treecombo 的值
            var root = me.store.getRootNode();
            var record = root.findChild(this.valueField,value,true);
            record && me.setRawValue(record.get(this.displayField));
        }
        return me;
    },
    getValue : function() {
        return this.value;
    },
    getSubmitValue : function() {
        return this.getValue();
    },
    reset : function() {
        this.setValue(null).setRawValue(null);
    }
});
/****************************************************************************************/
Ext.define('Ext.ux.form.ColorComboBox', {
    extend : 'Ext.form.field.Picker',
    alias: 'widget.colorfield',
    editable:true,
    matchFieldWidth:true,//色板宽度与输入框匹配
    initComponent : function() {
        this.addEvents('itemclick');
        Ext.apply(this, {
            pickerAlign : 'tl-bl?'
        });
        this.callParent();
    },
    createPicker : function() {
        var me = this;
        this.picker = Ext.create('Ext.menu.ColorPicker', {
            listeners: {
                select: function(picker,color){
                    me.setValue('#'+color);
                    me.setRawValue(color);
                },
                beforeshow:function(menu,options){
                    if(Ext.Array.indexOf(menu.picker.colors, me.getValue().replace('#','')) > 0){
                        menu.picker.select(me.getValue().replace('#',''));
                    }
                }
            }
        });
        return this.picker;
    },
    alignPicker : function() {
        var picker, isAbove, aboveSfx = '-above';
        if (this.isExpanded) {
            picker = this.getPicker();
            if (this.matchFieldWidth) {
                picker.setWidth(this.bodyEl.getWidth());
            }
            if (picker.isFloating()) {
                picker.alignTo(this.inputEl, this.pickerAlign,
                    this.pickerOffset);
                isAbove = picker.el.getY() < this.inputEl.getY();
                this.bodyEl[isAbove ? 'addCls' : 'removeCls'](this.openCls
                    + aboveSfx);
                picker.el[isAbove ? 'addCls' : 'removeCls'](picker.baseCls
                    + aboveSfx);
            }
        }
    },
    setValue : function(value) {
        var me = this;
        me.callParent(arguments);
        if(value){
            me.setRawValue(value.substring(1));
            me.setColor(value);
        }
        return me;
    },
    setColor : function(hex) {
        this.superclass.setFieldStyle.call(this, {
            'font-color': '#' +hex
        });
    },
    getValue : function() {
        return this.value;
    },
    getSubmitValue : function() {
        return this.getValue();
    },
    reset : function() {
        this.setValue(null).setRawValue(null);
    }
});
/****************************************************************************************/