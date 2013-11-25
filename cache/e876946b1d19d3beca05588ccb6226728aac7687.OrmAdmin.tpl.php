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
  'unifunc' => 'content_527cc46b2eba02_51004284',
  'cache_lifetime' => '120',
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_527cc46b2eba02_51004284')) {function content_527cc46b2eba02_51004284($_smarty_tpl) {?><!DOCTYPE html>
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
			<h3><label>Table:</label>&#160;comerciales</h3>
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
									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>id_comercial</td>
						<td>bigint(20)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>nombre</td>
						<td>varchar(120)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>apellidos</td>
						<td>varchar(120)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<h4 class="orm-panel orm-behaviour-align-center">Clase no generada.</h4>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both' )"><img src="/icons/cog_add.png" /> Generar</button>
			</div>
			<br/><br/>
			<h3><label>Table:</label>&#160;cuentas</h3>
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
									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>id</td>
						<td>bigint(20)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>parentid</td>
						<td>bigint(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>id_empresa</td>
						<td>bigint(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>foto</td>
						<td>varchar(120)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>nombre</td>
						<td>varchar(120)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>apellidos</td>
						<td>varchar(120)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cargo</td>
						<td>varchar(300)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>telefono</td>
						<td>varchar(120)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>email</td>
						<td>varchar(120)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>provincia</td>
						<td>varchar(30)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>localidad</td>
						<td>varchar(120)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>direccion</td>
						<td>varchar(240)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cp</td>
						<td>bigint(10)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>id_comercial</td>
						<td>bigint(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>id_marca</td>
						<td>varchar(250)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>itemtype</td>
						<td>int(11)</td>
						<td>No</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<h4 class="orm-panel orm-behaviour-align-center">Clase no generada.</h4>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both' )"><img src="/icons/cog_add.png" /> Generar</button>
			</div>
			<br/><br/>
			<h3><label>Table:</label>&#160;empresas</h3>
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
									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>id_empresa</td>
						<td>bigint(20)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>empresa</td>
						<td>varchar(120)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<h4 class="orm-panel orm-behaviour-align-center">Clase no generada.</h4>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both' )"><img src="/icons/cog_add.png" /> Generar</button>
			</div>
			<br/><br/>
			<h3><label>Table:</label>&#160;marcas</h3>
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
									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>id_marca</td>
						<td>bigint(20)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>marca</td>
						<td>varchar(120)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<h4 class="orm-panel orm-behaviour-align-center">Clase no generada.</h4>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both' )"><img src="/icons/cog_add.png" /> Generar</button>
			</div>
			<br/><br/>
			<h3><label>Table:</label>&#160;otros_datos</h3>
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
									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>id_otrosdatos</td>
						<td>bigint(20)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>id</td>
						<td>bigint(20)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>id_tipo</td>
						<td>bigint(20)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>valor</td>
						<td>varchar(120)</td>
						<td>No</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<h4 class="orm-panel orm-behaviour-align-center">Clase no generada.</h4>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both' )"><img src="/icons/cog_add.png" /> Generar</button>
			</div>
			<br/><br/>
			<h3><label>Table:</label>&#160;tipos_datos</h3>
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
									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>id_tipo</td>
						<td>bigint(20)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>descripcion</td>
						<td>varchar(120)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<h4 class="orm-panel orm-behaviour-align-center">Clase no generada.</h4>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both' )"><img src="/icons/cog_add.png" /> Generar</button>
			</div>
			<br/><br/>

</body>
</html><?php }} ?>
