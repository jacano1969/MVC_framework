var orm = new function() {
	this.open_table = function( schema, table ) {
		mvc.modal( "table?schema="+schema+"&table=" + table, 1000, 500 );
	}

	this.generate_both = function( schema, table, cls, regen ) {
		this.generate_main( schema, table, cls, regen, true );
	}

	this.generate_main = function( schema, table, cls, regen, both ) {
		var orm = this;
		if ( !cls ) {

		} else {
			if ( regen ) {
				if ( !confirm( "Are you sure? Class " + cls + " will be overwritten" ) ) {
					return;
				}
			}
			$.ajax({
				url: "table.generate.json?schema="+schema+"&table="+table+"&class="+cls+"&orm=true"
				, dataType: "json"
				, success: function( data ) {
					mvc.message( data.messages.message.msg, data.messages.message.type );
				}
				, error: function( req, error, status ) {
					mvc.message( error, mvc.MSG_ERROR );
				}
			});
			if ( both ) {
				orm.generate_base( schema, table, cls );
			}
		}
	}

	this.validate_class_form = function( data, forms, options ) {
		form = forms[0];
		msgs = new Array();
		$(form).find( "input" ).removeClass( "ui-state-error" );


		if ( !form.class_name.value ) {
			$(form.class_name).addClass( "ui-state-error" );
			msgs.push( "Class name is required" );
		}

		$(form).find( "input" ).each(function() {
			if ( this.name && this.name.match( /fk\[(.*)\]/ ) ) {
				fk = RegExp.$1;
				if ( this.checked ) {
					fk_name = $(form).find( "input[name='fk_name["+fk+"]']" );
					if ( !fk_name.val() ) {
						fk_name.addClass("ui-state-error");
						msgs.push( "Foreign Key name for " + fk + " is required" );
					}
				}
			}

		});

		if ( msgs.length > 0 ) {
			mvc.message( "Form validation errors: " + msgs.join(", " ), mvc.MSG_ERROR, "#ClassOptionsMessages" );
			return false;
		}
		return true;
	}

	this.confirm_orm = function() {
		orm = this;
		$("#ConfirmOrm").dialog({
			height: 150
			, width: 600
			, modal: true
			, resizable: false
			, buttons: {
				"Regenerar": function() {
					orm.submit_class_form( 'orm', $("#ConfirmOrm").dialog("destroy"), $("#ConfirmOrm") );
				}
				, "Cancel": function() {
					$(this).dialog("destroy");
				}
			}
		});
	}

	this.submit_class_form = function( type, callback, container ) {
		container.ajaxSubmit({
			url: "table.generate.json?type=" + type
			, dataType: "json"
			, beforeSubmit: this.validate_class_form
			, success: function( data ) {
				console.log(data);
				console.log(data.messages.message.type);
				if ( data.messages.message.type == mvc.MSG_OK ) {
					mvc.message( data.messages.message.msg, data.messages.message.type );
					if ( callback ) callback();
				} else {
					mvc.message( "Error generating class: " + data.messages.message.msg, data.messages.message.type, container );
				}
			}
		});
	}

	this.show_class_form = function( type,form_id ) {
		var orm = this;
		$("#ClassOptionsForm_"+form_id).dialog({
			height: 300
			, width: 600
			, modal: true
			, buttons: {
				"Generar": function() {
					orm.submit_class_form( type, function() {
						if ( type == "both" ) {
							document.location.reload();
						} else {
							$(this).dialog("destroy");
						}
					}, $("#ClassOptionsForm_"+form_id) );
				}
				, "Cancel": function() {
					$(this).dialog("destroy");
				}
			}
		});
	}

	this.generate_base = function( schema, table, cls, change ) {

	}
}
