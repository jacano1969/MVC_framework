var mvc = new function() {

	this.MSG_CUSTOM = 'custom';
	this.MSG_INFO = 'info';
	this.MSG_WARN = 'warn';
	this.MSG_OK = 'ok';
	this.MSG_ERROR = 'error';

	this.$tabs = null;
	this.$dialog = null;

	/**
	 * Calls this.dialog with modal setting true
	 *
	 * @param string url
	 * @param int w
	 * @param int h
	 */
	this.modal = function( url, w, h ) {
		this.dialog( url, w, h, true );
	}

	/**
	 * Opens a dialog for the supplied url, width, height and modal setting.
	 *
	 * The first time this method is called, the dialog is actually instantiated.
	 *
	 * @param string url
	 * @param int w
	 * @param int h
	 * @param boolean modal
	 */
	this.dialog = function( url, w, h, modal ) {
		var mvc = this;
		_h = h ? h : 300;
		_w = w ? w : 400;
		if ( this.$dialog == null ) {
			this.$dialog = $('<div title="Generacion Classes"><iframe id="_DIALOG_FRAME" src="" style="border: 0" frameborder="0"/></div>').dialog({
				height: _h
				, width: "auto"
				, modal: true
				, autoOpen: false
				, autoResize: true
				, resizable: false
			});
		} else {
			this.$dialog.dialog("option", "width", _w );
			this.$dialog.dialog("option", "height", _h );
		}
		$("#_DIALOG_FRAME").attr("src", url).width( _w - 30 ).height( _h - 50 );

		this.$dialog.dialog( "option", "width", _w );
		this.$dialog.dialog("open");
	}

	/**
	 * Generate the tabs for the supplied element
	 *
	 * @param element (Object ID)
	 */
	this.tabs = function( element ) {
		this.$tabs = $(element).tabs({
			ajaxOptions: {
				dataType: "html"
			}
		});
	}

	this.buttons = function() {
		$("button").button();
	}

	this.hints = function() {
		mvc = this;
		$(".mvc-textfield").each(function() {
			mvc.enableHint( $(this) );
		});

		$(".mvc-textfield")
			.focusin( function() {
				mvc.disableHint( $(this) );
			})
			.focusout( function() {
				mvc.enableHint( $(this) );
			});
	}

	this.enableHint = function ( $elem ) {
		if ( $elem.attr("value") == "" && $elem.attr("hint") != "" ) {
			$elem.val( $elem.attr("hint") );
			$elem.addClass( "mvc-state-hint" );
		}
	}

	this.disableHint = function( $elem ) {
		if ( $elem.attr("value") == $elem.attr("hint") ) {
			$elem.val("");
			$elem.removeClass( "mvc-state-hint" );
		}
	}

	this.message = function( msg, type, container ) {
		cls = "mvc-msg-" + type;
		if ( !container ) container = $("body");
		var i = 1;
		container.find(".mvc-msg").each(function() {
				switch( i ) {
					case 1:
						$(this).css( "opacity", "0.6" ).css( "filter", "alpha(opacity=60)" );
						break;
					case 2:
						$(this).css( "opacity", "0.3" ).css( "filter", "alpha(opacity=30)" );
						break;
					case 3:
						$(this).remove();
				}
				i++;
			});
		var div = $("<div class='mvc-msg "+cls+" ui-corner-all' style='display: none'><span class='mvc-msg-close' onclick='mvc.remove_message( this )'/><span style='margin-left: 30px; margin-right: 20px'>"+msg+"</span></div>");

		container.prepend(div);
		div.fadeIn( "fast" );
	}

	this.remove_message = function( elem ) {
		$(elem).parent().remove();
	}
}

$(document).ready(function() {
	mvc.tabs( "#Tabs" );
	mvc.buttons();
});
