<?php /* Smarty version Smarty-3.1.15, created on 2013-11-08 12:00:59
         compiled from "template\OrmAdmin.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2774527cc46b1ab4c1-00918020%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e876946b1d19d3beca05588ccb6226728aac7687' => 
    array (
      0 => 'template\\OrmAdmin.tpl',
      1 => 1383908274,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2774527cc46b1ab4c1-00918020',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'model' => 0,
    'tables' => 0,
    'table' => 0,
    'columns' => 0,
    'column' => 0,
    'item' => 0,
    'marty' => 0,
    'param' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_527cc46b2b4f08_15259132',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_527cc46b2b4f08_15259132')) {function content_527cc46b2b4f08_15259132($_smarty_tpl) {?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
	<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.js"></script>
	<script type="text/javascript" src="js/orm.js"></script>
	<script type="text/javascript" src="js/mvc.js"></script>
	<script type="text/javascript" src="js/jquery.form.js"></script>
	<script type="text/javascript">
		$(document).ready(function (e) {
			mvc.buttons();
			mvc.hints();
			$(".hover").hover(
					function() {
						$(this).addClass("ui-state-hover");
					}
					, function() {
						$(this).removeClass("ui-state-hover");
					}
			);
			$("input[type='text']").bind({
				focusin: function() { $(this).toggleClass( "ui-state-focus" ); }
				, focusout: function() { $(this).toggleClass( "ui-state-focus" ); }
			});
		});
	</script>

</head>

<body>
<?php  $_smarty_tpl->tpl_vars['tables'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tables']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['model']->value->data->tables; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tables']->key => $_smarty_tpl->tpl_vars['tables']->value) {
$_smarty_tpl->tpl_vars['tables']->_loop = true;
?>
	<?php  $_smarty_tpl->tpl_vars['table'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['table']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tables']->value->table; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['table']->key => $_smarty_tpl->tpl_vars['table']->value) {
$_smarty_tpl->tpl_vars['table']->_loop = true;
?>
		<h3><label>Table:</label>&#160;<?php echo cObj($_smarty_tpl->tpl_vars['table']->value['name']);?>
</h3>
		<?php  $_smarty_tpl->tpl_vars['columns'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['columns']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['table']->value->columns; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['columns']->key => $_smarty_tpl->tpl_vars['columns']->value) {
$_smarty_tpl->tpl_vars['columns']->_loop = true;
?>
			<table class="orm-table orm-table-small">
				<thead>
				<tr>
					<td style="width: 20%">Column</td>
					<td style="width: 20%">Type</td>
					<td style="width: 10%">Null</td>
					<td style="width: 10%">Key</td>
				</tr>
				</thead>
				<tbody>
				<?php  $_smarty_tpl->tpl_vars['column'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['column']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['column']->key => $_smarty_tpl->tpl_vars['column']->value) {
$_smarty_tpl->tpl_vars['column']->_loop = true;
?>
					<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td><?php echo cObj($_smarty_tpl->tpl_vars['column']->value['name']);?>
</td>
						<td><?php echo cObj($_smarty_tpl->tpl_vars['column']->value['type']);?>
</td>
						<td><?php echo cObj($_smarty_tpl->tpl_vars['column']->value['nullstring']);?>
</td>
						<td><?php echo cObj($_smarty_tpl->tpl_vars['column']->value['primary-key']);?>
</td>
					</tr>

				<?php } ?>
				</tbody>
			</table>
		<?php } ?>
		<?php if (!(is_null($_smarty_tpl->tpl_vars['table']->value->class['name']))) {?>
			<h4><label>Class:</label>&#160;<?php echo cObj($_smarty_tpl->tpl_vars['table']->value->class['full-name']);?>
</h4>
			<h4><label>File:</label>&#160;<?php echo cObj($_smarty_tpl->tpl_vars['table']->value->class['file']);?>
</h4>
			<h4><label>Base:</label>&#160;<?php echo cObj($_smarty_tpl->tpl_vars['table']->value->class['name']);?>
</h4>
			<h4><label>Updated:</label>&#160;<?php echo cObj($_smarty_tpl->tpl_vars['table']->value->class['date']);?>
</h4>
		<?php }?>
		<?php if (!(is_null($_smarty_tpl->tpl_vars['table']->value->class['full-name']))) {?>
			<table class="orm-table orm-table-small">
				<thead>
				<tr>
					<td style="width: 30%">Propiedades</td>
					<td style="width: 70%">Metodos</td>
				</tr>
				</thead>
				<tbody>
				<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['table']->value->class->field; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
					<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">;<?php echo cObj($_smarty_tpl->tpl_vars['item']->value['name']);?>
</td>
							<td style="border-left: 1px dotted black">
								<ul class="orm-list">
									<li style="border-bottom: 1px dotted black">
										<b> <?php if (cObj($_smarty_tpl->tpl_vars['item']->value['abstract'])=='true') {?>abstract &#160;<?php }?>
											<?php if (cObj($_smarty_tpl->tpl_vars['item']->value['static'])=='true') {?>static &#160;<?php }?>
											<?php echo cObj($_smarty_tpl->tpl_vars['item']->value['access']);?>
&#160;
											<?php echo cObj($_smarty_tpl->tpl_vars['item']->value['name']);?>
</b>
										(
										<?php  $_smarty_tpl->tpl_vars['param'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['param']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['item']->value->params; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['param']->key => $_smarty_tpl->tpl_vars['param']->value) {
$_smarty_tpl->tpl_vars['param']->_loop = true;
?>
											<?php if (!$_smarty_tpl->tpl_vars['marty']->value['foreach']['bucleparametros']['first']) {?>, <?php }?>
											$<?php echo cObj($_smarty_tpl->tpl_vars['param']->value['name']);?>
 =
												<?php if (cObj($_smarty_tpl->tpl_vars['item']->value['@default'])=='false'||cObj($_smarty_tpl->tpl_vars['item']->value['@default'])=='true'||cObj($_smarty_tpl->tpl_vars['item']->value['@default'])=='null') {?>
													<?php echo cObj($_smarty_tpl->tpl_vars['item']->value['@default']);?>

												<?php } else { ?>
													'<?php echo cObj($_smarty_tpl->tpl_vars['item']->value['@default']);?>
'
												<?php }?>
										<?php } ?>
										)
									</li>

								</ul>
							</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			<div class="orm-button-panel">
				<button onclick="orm.confirm_orm( 'orm' )"><img src="/icons/cog_error.png" /> Regenerar ORM</button>
				&#160;
				<button onclick="orm.show_class_form( 'base' )"><img src="/icons/cog_edit.png" /> Regenerar BASE</button>
			</div>
		<?php } else { ?>
			<h4 class="orm-panel orm-behaviour-align-center">Clase no generada.</h4>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both' )"><img src="/icons/cog_add.png" /> Generar</button>
			</div>
		<?php }?>
	<?php } ?>
<br/><br/>
<?php } ?>

</body>
</html><?php }} ?>
