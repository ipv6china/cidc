<?php /* Smarty version Smarty-3.0.8, created on 2012-04-13 21:09:55
         compiled from "/home/y109/Develop/php/cidc/application/modules/idc/views/scripts/layout/layout.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2690075944f8825a31dddf3-61945535%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bceaf35cae403f6e8f34107d8462c95f423dc6a2' => 
    array (
      0 => '/home/y109/Develop/php/cidc/application/modules/idc/views/scripts/layout/layout.tpl',
      1 => 1322216119,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2690075944f8825a31dddf3-61945535',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php echo $_smarty_tpl->getVariable('this')->value->doctype();?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $_smarty_tpl->getVariable('this')->value->title;?>
 - Comsenz IDC System</title>
        <link rel="shortcut icon" href="/favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('/');?>
js/ext/resources/css/ext-all.css" />
        <link href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('/');?>
style/style.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('/');?>
style/icons.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('/');?>
style/ext-patch.css" media="screen" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('/');?>
js/ext/bootstrap.js"></script>
        <script type="text/javascript" src="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('/');?>
js/ext/locale/ext-lang-zh_CN.js"></script>
        <script type="text/javascript" src="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('/');?>
js/common.js"></script>
        <script type="text/javascript" src="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('/');?>
js/etao.js"></script>
    </head>
    <body>
        <div class="mainBody">
            <div class="mainNav">
                <div class="logo"></div>
                <ul>
                    <li<?php if ("idc"==$_smarty_tpl->getVariable('moduleName')->value&&"index"==$_smarty_tpl->getVariable('controllerName')->value){?> class="current"<?php }?>><a href="<?php echo $_smarty_tpl->getVariable('this')->value->url(array('module'=>'idc','controller'=>'index','action'=>'index'));?>
" class="">首页</a></li>
                    <li<?php if ("idc"==$_smarty_tpl->getVariable('moduleName')->value&&"group"==$_smarty_tpl->getVariable('controllerName')->value){?> class="current"<?php }?>><a href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('idc/group');?>
">分组</a></li>
                    <li<?php if ("idc"==$_smarty_tpl->getVariable('moduleName')->value&&"network"==$_smarty_tpl->getVariable('controllerName')->value){?> class="current"<?php }?>><a href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('idc/network');?>
">网络</a></li>
                    <li<?php if ("idc"==$_smarty_tpl->getVariable('moduleName')->value&&"cabinet"==$_smarty_tpl->getVariable('controllerName')->value){?> class="current"<?php }?>><a href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('idc/cabinet');?>
">设备</a></li>
                    <li style="display:none;" <?php if ("idc"==$_smarty_tpl->getVariable('moduleName')->value&&"machine"==$_smarty_tpl->getVariable('controllerName')->value){?> class="current"<?php }?>><a href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('idc/machine');?>
">服务器</a></li>
                    <li style="display:none;" <?php if ("idc"==$_smarty_tpl->getVariable('moduleName')->value&&"networkequipment"==$_smarty_tpl->getVariable('controllerName')->value){?> class="current"<?php }?>><a href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('idc/networkequipment');?>
">网络设备</a></li>
                    <li<?php if ("idc"==$_smarty_tpl->getVariable('moduleName')->value&&"domain"==$_smarty_tpl->getVariable('controllerName')->value){?> class="current"<?php }?>><a href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('idc/domain');?>
">域名</a></li>
                    <li<?php if ("idc"==$_smarty_tpl->getVariable('moduleName')->value&&"log"==$_smarty_tpl->getVariable('controllerName')->value){?> class="current"<?php }?>><a href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('idc/log');?>
">日志</a></li>
                    <li<?php if ("tools"==$_smarty_tpl->getVariable('moduleName')->value&&"graphviz"==$_smarty_tpl->getVariable('controllerName')->value){?> class="current"<?php }?>><a href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('tools');?>
">工具</a></li>
                    <li<?php if ("user"==$_smarty_tpl->getVariable('moduleName')->value&&"user"==$_smarty_tpl->getVariable('controllerName')->value){?> class="current"<?php }?>><a href="<?php echo $_smarty_tpl->getVariable('this')->value->baseUrl('user');?>
">用户</a></li>
                </ul>
            </div>
            <div id="content">
                <?php if (is_array($_smarty_tpl->getVariable('this')->value->messages)&&count($_smarty_tpl->getVariable('this')->value->messages)>0){?>
                    <div class="message">
                        <?php  $_smarty_tpl->tpl_vars["message"] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars["key"] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('this')->value->messages; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars["message"]->key => $_smarty_tpl->tpl_vars["message"]->value){
 $_smarty_tpl->tpl_vars["key"]->value = $_smarty_tpl->tpl_vars["message"]->key;
?>
                            <div class="info"><?php echo $_smarty_tpl->getVariable('message')->value;?>
</div>
                        <?php }} ?>
                    </div>
                <?php }?>
                <?php echo $_smarty_tpl->getVariable('this')->value->layout()->content;?>

                <div id="footer">
                    <div class="copy">&copy; 2011 Comsenz Inc.</div>
                </div>
            </div>
        </div>
    </body>
</html>
