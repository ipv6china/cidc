<?php /* Smarty version Smarty-3.0.8, created on 2012-04-13 21:25:55
         compiled from "/home/y109/Develop/php/cidc/application/modules/idc/views/scripts/error/error.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6096290944f882963f32758-21602567%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bf6ff380df352e2a0a117568942d66feb42adb91' => 
    array (
      0 => '/home/y109/Develop/php/cidc/application/modules/idc/views/scripts/error/error.tpl',
      1 => 1318140123,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6096290944f882963f32758-21602567',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="mainContent">
    <div class="pageTitle">异常： <?php echo $_smarty_tpl->getVariable('this')->value->message;?>

    </div>
<?php if (isset($_smarty_tpl->getVariable('this',null,true,false)->value->exception)){?>
    <div class="exception">
    <h3>错误:</h3>
    <pre>
        <?php echo $_smarty_tpl->getVariable('this')->value->exception->getMessage();?>

    </pre>
    <h3>堆栈:</h3>
    <pre><?php echo $_smarty_tpl->getVariable('this')->value->exception->getTraceAsString();?>

    </pre>

    <h3>参数:</h3>
    <pre>
        <?php echo var_export($_smarty_tpl->getVariable('this')->value->request);?>

        <!--{var_export($this->request->getParams(),true)}-->
    </pre>
        </div>
<?php }?>
</div>