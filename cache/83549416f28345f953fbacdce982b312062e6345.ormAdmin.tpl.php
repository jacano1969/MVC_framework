<?php /*%%SmartyHeaderCode:38065293680f914063-07384358%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '83549416f28345f953fbacdce982b312062e6345' => 
    array (
      0 => 'template\\ormAdmin.tpl',
      1 => 1384953246,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '38065293680f914063-07384358',
  'variables' => 
  array (
    'model' => 0,
    'tables' => 0,
    'table' => 0,
    'columns' => 0,
    'column' => 0,
    'field' => 0,
    'method' => 0,
    'param' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_52936810829dc0_32382956',
  'cache_lifetime' => '120',
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52936810829dc0_32382956')) {function content_52936810829dc0_32382956($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
  <title></title>
	<link rel='stylesheet' href='../css/jquery-ui.css' type='text/css' />
	<link rel='stylesheet' href='../css/orm.css' type='text/css' />
	<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="../js/orm.js"></script>
	<script type="text/javascript" src="../js/mvc.js"></script>
	<script type="text/javascript" src="../js/jquery.form.js"></script>
	<script type="text/javascript" src="../js/mvc.js"></script>
	<script type="text/javascript">
		$(document).ready(function () {
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
				 focusin : function() { $(this).toggleClass( "ui-state-focus" ); }
				,focusout: function() { $(this).toggleClass( "ui-state-focus" ); }
			});
			$( "#ormadmin" ).accordion({ header: "h3", heightStyle: "content"  });
		});
	</script>

</head>

<body>
<div id="ormadmin">
			<h3><label>Tabla:</label>&#160;&#160;&#160;campanas</h3>
		<div>
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
						<td>idcampana</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idusuario</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>tipo</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>version</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>estado</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>estado_motivo</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>nombre</td>
						<td>varchar(300)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>fecha_inicio</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>fecha_fin</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>peso</td>
						<td>smallint(6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>frecuencia</td>
						<td>smallint(6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>presupuesto_diario</td>
						<td>decimal(10,3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>presupuesto_total</td>
						<td>decimal(10,3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>presupuesto_reparto</td>
						<td>tinyint(1)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>pixel_seguimiento</td>
						<td>tinyint(1)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>pixel_impresion</td>
						<td>varchar(400)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>filtro_horario</td>
						<td>varchar(400)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>filtro_tags</td>
						<td>varchar(500)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>filtro_paginas</td>
						<td>varchar(1000)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>filtro_so</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>filtro_operadores</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>filtro_regiones</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>tipo_campana</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>tipo_pago</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>paga_advertiser</td>
						<td>decimal(8,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>paga_descuento</td>
						<td>decimal(3,2)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>paga_publisher</td>
						<td>decimal(8,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>comercial</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cambio</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
							<h4><label>Class:</label>&#160;\orm\campanas</h4>
			<h4><label>File:</label>&#160;C:\Users\jacano\Dropbox\subversion\josea.cano\clases\orm\campanas.class.php</h4>
			<h4><label>Base:</label>&#160;campanas</h4>
			<h4><label>Updated:</label>&#160;25 Nov 2013 13:16</h4>
							<table class="orm-table orm-table-small">
				<thead>
				<tr>
					<td style="width: 30%">Propiedades</td>
					<td style="width: 70%">Metodos</td>
				</tr>
				</thead>
				<tbody>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">idcampana</td>
																								</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">idusuario</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">tipo</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">version</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">estado</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">estadoMotivo</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">nombre</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">fechaInicio</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">fechaFin</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">peso</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">frecuencia</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">presupuestoDiario</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">presupuestoTotal</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">presupuestoReparto</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">pixelSeguimiento</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">pixelImpresion</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">filtroHorario</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">filtroTags</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">filtroPaginas</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">filtroSo</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">filtroOperadores</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">filtroRegiones</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">tipoCampana</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">tipoPago</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">pagaAdvertiser</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">pagaDescuento</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">pagaPublisher</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">comercial</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">cambio</td>
											</tr>
								</tbody>
			</table>
			<div class="orm-button-panel">
				<button onclick="orm.confirm_orm( 'orm' )"><img src="../icons/cog_error.png" /> Regenerar ORM</button>
				&#160;
				<button onclick="orm.show_class_form( 'base','campanas' )"><img src="../icons/cog_edit.png" /> Regenerar BASE</button>
			</div>

			<div id="ConfirmOrm" class="orm-behaviour-hidden">
				<img src="../icons/cog_error.png" />
				Confirm si desea regenerar el ORM: (Todos los cambios realizados anteriormente se perderan)
				<br /><br />
				Fichero: <strong>campanas</strong>
			</div>
				<form id="ClassOptionsForm_campanas" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="campanas" disabled="disabled"/>
									<input type="hidden" name="class_name" value="campanas" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="campanas" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;campanas_ecpm</h3>
		<div>
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
						<td>idcodigo</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>pais</td>
						<td>char(2)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcampana</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>impresiones</td>
						<td>int(10) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>euros</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ecpm</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','campanas_ecpm' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_campanas_ecpm" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="campanas_ecpm" disabled="disabled"/>
									<input type="hidden" name="class_name" value="campanas_ecpm" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="campanas_ecpm" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;campanas_paises</h3>
		<div>
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
						<td>idcampana</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>pais</td>
						<td>char(3)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cambio</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','campanas_paises' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_campanas_paises" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="campanas_paises" disabled="disabled"/>
									<input type="hidden" name="class_name" value="campanas_paises" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="campanas_paises" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;campanas_stats</h3>
		<div>
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
						<td>idcampana</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>impresiones_dia</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>clicks_dia</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ventas_dia</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>coste_dia</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ingreso_dia</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>impresiones_total</td>
						<td>int(10) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>clicks_total</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ventas_total</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>coste_total</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ingreso_total</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cambio</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','campanas_stats' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_campanas_stats" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="campanas_stats" disabled="disabled"/>
									<input type="hidden" name="class_name" value="campanas_stats" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="campanas_stats" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;campanas_vistas</h3>
		<div>
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
						<td>identificador</td>
						<td>char(32)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ip</td>
						<td>varchar(15)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>pais</td>
						<td>char(2)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcampana</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>last_imp</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>impresiones</td>
						<td>int(10) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>last_click</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>clicks</td>
						<td>int(10) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','campanas_vistas' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_campanas_vistas" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="campanas_vistas" disabled="disabled"/>
									<input type="hidden" name="class_name" value="campanas_vistas" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="campanas_vistas" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;config_formatos</h3>
		<div>
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
						<td>idformato</td>
						<td>char(3)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>nombre</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>version</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','config_formatos' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_config_formatos" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="config_formatos" disabled="disabled"/>
									<input type="hidden" name="class_name" value="config_formatos" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="config_formatos" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;config_tamanos</h3>
		<div>
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
						<td>idtamano</td>
						<td>tinyint(3) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>nombre</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>version</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','config_tamanos' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_config_tamanos" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="config_tamanos" disabled="disabled"/>
									<input type="hidden" name="class_name" value="config_tamanos" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="config_tamanos" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;creatividades</h3>
		<div>
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
						<td>idcreatividad</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcampana</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>estado</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idformato</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idtamano</td>
						<td>tinyint(4)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>datos</td>
						<td>varchar(4000)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>fecha_alta</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cambio</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','creatividades' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_creatividades" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="creatividades" disabled="disabled"/>
									<input type="hidden" name="class_name" value="creatividades" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="creatividades" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;creatividades_ecpm</h3>
		<div>
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
						<td>pais</td>
						<td>char(2)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcreatividad</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>impresiones</td>
						<td>int(10) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>euros</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ecpm</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','creatividades_ecpm' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_creatividades_ecpm" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="creatividades_ecpm" disabled="disabled"/>
									<input type="hidden" name="class_name" value="creatividades_ecpm" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="creatividades_ecpm" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;creatividades_stats</h3>
		<div>
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
						<td>idcreatividad</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>impresiones_dia</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>clicks_dia</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ventas_dia</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>impresiones_total</td>
						<td>int(10) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>clicks_total</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ventas_total</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>coste</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ingreso</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cambio</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','creatividades_stats' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_creatividades_stats" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="creatividades_stats" disabled="disabled"/>
									<input type="hidden" name="class_name" value="creatividades_stats" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="creatividades_stats" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;frontal_campanas_stats</h3>
		<div>
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
						<td>idcampana</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>impresiones</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>clicks</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ventas</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>coste</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ingreso</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','frontal_campanas_stats' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_frontal_campanas_stats" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="frontal_campanas_stats" disabled="disabled"/>
									<input type="hidden" name="class_name" value="frontal_campanas_stats" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="frontal_campanas_stats" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;frontal_creatividades_stats</h3>
		<div>
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
						<td>idcreatividad</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>impresiones</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>clicks</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ventas</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>coste</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ingreso</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','frontal_creatividades_stats' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_frontal_creatividades_stats" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="frontal_creatividades_stats" disabled="disabled"/>
									<input type="hidden" name="class_name" value="frontal_creatividades_stats" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="frontal_creatividades_stats" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;grupos</h3>
		<div>
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
						<td>idgrupo</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idusuario</td>
						<td>int(10) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>nombre</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','grupos' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_grupos" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="grupos" disabled="disabled"/>
									<input type="hidden" name="class_name" value="grupos" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="grupos" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;log_mysql</h3>
		<div>
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
						<td>idusuario</td>
						<td>int(10) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>fecha</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>microsegundos</td>
						<td>decimal(12,2)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>query</td>
						<td>varchar(4000)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>timer</td>
						<td>decimal(10,9)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>error</td>
						<td>varchar(4000)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','log_mysql' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_log_mysql" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="log_mysql" disabled="disabled"/>
									<input type="hidden" name="class_name" value="log_mysql" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="log_mysql" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;monitor_no_campaign</h3>
		<div>
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
						<td>dia</td>
						<td>date</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>hora</td>
						<td>tinyint(3) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcodigo</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>pais</td>
						<td>char(2)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>so</td>
						<td>tinyint(3) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>veces</td>
						<td>int(10) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','monitor_no_campaign' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_monitor_no_campaign" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="monitor_no_campaign" disabled="disabled"/>
									<input type="hidden" name="class_name" value="monitor_no_campaign" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="monitor_no_campaign" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;paginas</h3>
		<div>
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
						<td>idpagina</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idusuario</td>
						<td>int(10) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>estado</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>version</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>tipo</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>url</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>descripcion</td>
						<td>varchar(300)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>tags</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>opciones</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>fecha_alta</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cambio</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','paginas' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_paginas" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="paginas" disabled="disabled"/>
									<input type="hidden" name="class_name" value="paginas" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="paginas" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;paginas_codigos</h3>
		<div>
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
						<td>idcodigo</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idpagina</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idtamano</td>
						<td>tinyint(3) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>estado</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>nombre</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>alternativo</td>
						<td>varchar(300)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>opciones</td>
						<td>varchar(300)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>fecha_alta</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cambio</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','paginas_codigos' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_paginas_codigos" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="paginas_codigos" disabled="disabled"/>
									<input type="hidden" name="class_name" value="paginas_codigos" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="paginas_codigos" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;paises</h3>
		<div>
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
						<td>pais</td>
						<td>char(2)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>padre</td>
						<td>char(2)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','paises' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_paises" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="paises" disabled="disabled"/>
									<input type="hidden" name="class_name" value="paises" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="paises" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;stats_dia</h3>
		<div>
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
						<td>dia</td>
						<td>date</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>pais</td>
						<td>char(2)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcodigo</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcampana</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcreatividad</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>impresiones</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>clicks</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ventas</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>coste</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ingreso</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','stats_dia' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_stats_dia" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="stats_dia" disabled="disabled"/>
									<input type="hidden" name="class_name" value="stats_dia" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="stats_dia" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;stats_hora</h3>
		<div>
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
						<td>dia</td>
						<td>date</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>hora</td>
						<td>int(11)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>pais</td>
						<td>char(2)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcodigo</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcampana</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcreatividad</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>impresiones</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>clicks</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ventas</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>coste</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ingreso</td>
						<td>decimal(12,6)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','stats_hora' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_stats_hora" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="stats_hora" disabled="disabled"/>
									<input type="hidden" name="class_name" value="stats_hora" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="stats_hora" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;usuarios</h3>
		<div>
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
						<td>idusuario</td>
						<td>int(11) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>login</td>
						<td>varchar(50)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>password</td>
						<td>varchar(20)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>estado</td>
						<td>char(3)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>perfil</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>nombre</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>email</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idioma</td>
						<td>char(2)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>opciones</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>monedero_actual</td>
						<td>decimal(10,3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>monedero_facturacion</td>
						<td>decimal(10,3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>tipo</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>empresa</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>direccion</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>localidad</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>provincia</td>
						<td>varchar(60)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>codigo_postal</td>
						<td>varchar(10)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>pais</td>
						<td>char(2)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cif</td>
						<td>varchar(15)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>nif</td>
						<td>varchar(15)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>fecha_registro</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>fecha_ultimo_acceso</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ip_ultimo_acceso</td>
						<td>varchar(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>telefono</td>
						<td>varchar(30)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>movil</td>
						<td>varchar(30)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>url</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cuenta_bancaria</td>
						<td>varchar(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>banco</td>
						<td>varchar(50)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>direccion_banco</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>swift</td>
						<td>varchar(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>iban</td>
						<td>varchar(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cuenta_paypal</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>metodo_pago</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>tipo_pago</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
							<h4><label>Class:</label>&#160;\orm\usuarios</h4>
			<h4><label>File:</label>&#160;C:\Users\jacano\Dropbox\subversion\josea.cano\clases\orm\usuarios.class.php</h4>
			<h4><label>Base:</label>&#160;usuarios</h4>
			<h4><label>Updated:</label>&#160;25 Nov 2013 14:03</h4>
							<table class="orm-table orm-table-small">
				<thead>
				<tr>
					<td style="width: 30%">Propiedades</td>
					<td style="width: 70%">Metodos</td>
				</tr>
				</thead>
				<tbody>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">idusuario</td>
																								</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">login</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">password</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">estado</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">perfil</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">nombre</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">email</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">idioma</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">opciones</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">monederoActual</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">monederoFacturacion</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">tipo</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">empresa</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">direccion</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">localidad</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">provincia</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">codigoPostal</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">pais</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">cif</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">nif</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">fechaRegistro</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">fechaUltimoAcceso</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">ipUltimoAcceso</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">telefono</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">movil</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">url</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">cuentaBancaria</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">banco</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">direccionBanco</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">swift</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">iban</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">cuentaPaypal</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">metodoPago</td>
											</tr>
									<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">tipoPago</td>
											</tr>
								</tbody>
			</table>
			<div class="orm-button-panel">
				<button onclick="orm.confirm_orm( 'orm' )"><img src="../icons/cog_error.png" /> Regenerar ORM</button>
				&#160;
				<button onclick="orm.show_class_form( 'base','usuarios' )"><img src="../icons/cog_edit.png" /> Regenerar BASE</button>
			</div>

			<div id="ConfirmOrm" class="orm-behaviour-hidden">
				<img src="../icons/cog_error.png" />
				Confirm si desea regenerar el ORM: (Todos los cambios realizados anteriormente se perderan)
				<br /><br />
				Fichero: <strong>usuarios</strong>
			</div>
				<form id="ClassOptionsForm_usuarios" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="usuarios" disabled="disabled"/>
									<input type="hidden" name="class_name" value="usuarios" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="usuarios" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;_migracion_campanas</h3>
		<div>
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
						<td>idcampana</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idimportado</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>origen</td>
						<td>char(3)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>datos_originales</td>
						<td>varchar(3000)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','_migracion_campanas' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm__migracion_campanas" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="_migracion_campanas" disabled="disabled"/>
									<input type="hidden" name="class_name" value="_migracion_campanas" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="_migracion_campanas" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;_migracion_codigos</h3>
		<div>
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
						<td>idcodigo</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idimportado</td>
						<td>int(10) unsigned</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>origen</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>datos_originales</td>
						<td>varchar(500)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','_migracion_codigos' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm__migracion_codigos" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="_migracion_codigos" disabled="disabled"/>
									<input type="hidden" name="class_name" value="_migracion_codigos" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="_migracion_codigos" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;_migracion_creatividades</h3>
		<div>
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
						<td>idcreatividad</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idcampanaimportado</td>
						<td>int(11)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idimportado</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>origen</td>
						<td>char(3)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>datos_originales</td>
						<td>varchar(2000)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','_migracion_creatividades' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm__migracion_creatividades" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="_migracion_creatividades" disabled="disabled"/>
									<input type="hidden" name="class_name" value="_migracion_creatividades" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="_migracion_creatividades" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;_migracion_paginas</h3>
		<div>
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
						<td>idpagina</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idimportado</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>origen</td>
						<td>char(3)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>datos_originales</td>
						<td>varchar(500)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','_migracion_paginas' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm__migracion_paginas" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="_migracion_paginas" disabled="disabled"/>
									<input type="hidden" name="class_name" value="_migracion_paginas" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="_migracion_paginas" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;_migracion_usuarios</h3>
		<div>
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
						<td>idusuario</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idimportado</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>origen</td>
						<td>char(3)</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>login</td>
						<td>varchar(50)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>password</td>
						<td>varchar(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>datos_originales</td>
						<td>varchar(500)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','_migracion_usuarios' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm__migracion_usuarios" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="_migracion_usuarios" disabled="disabled"/>
									<input type="hidden" name="class_name" value="_migracion_usuarios" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="_migracion_usuarios" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;____usuarios</h3>
		<div>
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
						<td>idusuario</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>login</td>
						<td>varchar(50)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>password</td>
						<td>varchar(20)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>estado</td>
						<td>char(3)</td>
						<td>No</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>perfil</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>nombre</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>email</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>idioma</td>
						<td>char(2)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>opciones</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>monedero_actual</td>
						<td>decimal(10,3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>monedero_facturacion</td>
						<td>decimal(10,3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','____usuarios' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_____usuarios" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="____usuarios" disabled="disabled"/>
									<input type="hidden" name="class_name" value="____usuarios" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="____usuarios" />
			</fieldset>
		</form>
	</div>
				<h3><label>Tabla:</label>&#160;&#160;&#160;____usuarios_datos</h3>
		<div>
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
						<td>idusuario</td>
						<td>int(10) unsigned</td>
						<td>No</td>
						<td>PRI</td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>tipo</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>empresa</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>direccion</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>localidad</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>provincia</td>
						<td>varchar(60)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>codigo_postal</td>
						<td>varchar(10)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>pais</td>
						<td>char(2)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cif</td>
						<td>varchar(15)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>nif</td>
						<td>varchar(15)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>fecha_registro</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>fecha_ultimo_acceso</td>
						<td>datetime</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>ip_ultimo_acceso</td>
						<td>varchar(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>telefono</td>
						<td>varchar(30)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>movil</td>
						<td>varchar(30)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>url</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cuenta_bancaria</td>
						<td>varchar(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>banco</td>
						<td>varchar(50)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>direccion_banco</td>
						<td>varchar(200)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>swift</td>
						<td>varchar(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>iban</td>
						<td>varchar(20)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>cuenta_paypal</td>
						<td>varchar(100)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>metodo_pago</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

									<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>tipo_pago</td>
						<td>char(3)</td>
						<td>Si</td>
						<td></td>
					</tr>

								</tbody>
			</table>
									<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','____usuarios_datos' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
				<form id="ClassOptionsForm_____usuarios_datos" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
																<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="____usuarios_datos" disabled="disabled"/>
									<input type="hidden" name="class_name" value="____usuarios_datos" />
													</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="impresionesweb" />
				<input type="hidden" name="table" value="____usuarios_datos" />
			</fieldset>
		</form>
	</div>
	</div>
</body>
</html><?php }} ?>
