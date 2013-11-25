{literal}
<!DOCTYPE html>
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
{/literal}
<body>
<div id="ormadmin">
{foreach item=tables from=$model->data->tables}
	{foreach item=table from=$tables->table}
		<h3><label>Tabla:</label>&#160;&#160;&#160;{cObj($table['name'])}</h3>
		<div>
		{foreach item=columns from=$table->columns}
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
				{foreach item=column from=$columns}
					<tr class="orm-row" style="border-bottom: 1px dotted black">
						<td>{cObj($column['name'])}</td>
						<td>{cObj($column['type'])}</td>
						<td>{cObj($column['nullstring'])}</td>
						<td>{cObj($column['primary-key'])}</td>
					</tr>

				{/foreach}
				</tbody>
			</table>
		{/foreach}
		{if !(is_null($table->class['name']))}
			<h4><label>Class:</label>&#160;{cObj($table->class['full-name'])}</h4>
			<h4><label>File:</label>&#160;{cObj($table->class['file'])}</h4>
			<h4><label>Base:</label>&#160;{cObj($table->class['name'])}</h4>
			<h4><label>Updated:</label>&#160;{cObj($table->class['date'])}</h4>
		{/if}
		{if !(is_null($table->class['full-name']))}
			<table class="orm-table orm-table-small">
				<thead>
				<tr>
					<td style="width: 30%">Propiedades</td>
					<td style="width: 70%">Metodos</td>
				</tr>
				</thead>
				<tbody>
				{foreach item=field from=$table->class->field}
					<tr class="orm-row">
						<td style="border-bottom: 1px dotted black">{cObj($field['name'])}</td>
						{if $field@first}
							{foreach item=method from=$table->class->method}
							<td rowspan="{$field@total}" style="border-left: 1px dotted black">
								<ul class="orm-list">
									<li style="border-bottom: 1px dotted black">
										<b> {if cObj($method['abstract']) == 'true'}abstract &#160;{/if}
											{if cObj($method['static']) == 'true'}static &#160;{/if}
											{cObj($method['access'])}&#160;
											{cObj($method['name'])}</b>
										(
										{foreach name=bucleparametros item=param from=$method->params->param}
											{if !$smarty.foreach.bucleparametros.first}, {/if}
											${cObj($param['name'])} =
												{if cObj($param['default']) == 'false' or cObj($param['default']) == 'true' or cObj($param['default']) == 'null'}
													{cObj($param['default'])}
												{else}
													'{cObj($param['default'])}'
												{/if}
										{/foreach}
										)
									</li>

								</ul>
							</td>
							{/foreach}
						{/if}
					</tr>
				{/foreach}
				</tbody>
			</table>
			<div class="orm-button-panel">
				<button onclick="orm.confirm_orm( 'orm' )"><img src="../icons/cog_error.png" /> Regenerar ORM</button>
				&#160;
				<button onclick="orm.show_class_form( 'base','{cObj($table['name'])}' )"><img src="../icons/cog_edit.png" /> Regenerar BASE</button>
			</div>

			<div id="ConfirmOrm" class="orm-behaviour-hidden">
				<img src="../icons/cog_error.png" />
				Confirm si desea regenerar el ORM: (Todos los cambios realizados anteriormente se perderan)
				<br /><br />
				Fichero: <strong>{cObj($table['name'])}</strong>
			</div>
		{else}
			<div class="orm-panel orm-behaviour-align-center">Clase no generada.</div>
			<div class="orm-button-panel">
				<button onclick="orm.show_class_form( 'both','{cObj($table['name'])}' )"><img src="../icons/cog_add.png" /> Generar</button>
			</div>
		{/if}
		<form id="ClassOptionsForm_{cObj($table['name'])}" class="orm-form orm-behaviour-hidden ui-widget" method="post">
			<fieldset class="ui-widget-content">
				<legend class="ui-widget-header ui-corner-all">Generar Clase</legend>
				<table>
					<tr>
						<td width="30%"><label>Class Name:</label></td>
						<td width="20%"></td>
						<td width="50%">
							{if cObj($table['name'])!==''}
									<input type="text" name="_class_name" class="hover text ui-state-default ui-corner-all" value="{cObj($table['name'])}" disabled="disabled"/>
									<input type="hidden" name="class_name" value="{cObj($table['name'])}" />
							{else}
									<input type="text" name="class_name" class="hover text ui-state-default ui-corner-all" value="" />
							{/if}
						</td>
					</tr>
				</table>
				<input type="hidden" name="schema" value="{cObj($table['schema'])}" />
				<input type="hidden" name="table" value="{cObj($table['name'])}" />
			</fieldset>
		</form>
	</div>
	{/foreach}
{/foreach}
</div>
</body>
</html>