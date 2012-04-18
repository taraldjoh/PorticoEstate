YAHOO.namespace ("INVOICE");


YAHOO.INVOICE.DEBUG = false;
YAHOO.INVOICE.LOG_ELEMENT = null;

YAHOO.INVOICE.Log = function( html )
{
	if( !YAHOO.INVOICE.DEBUG )
	{
		return;
	}

	if( YAHOO.INVOICE.LOG_ELEMENT == null )
	{
		YAHOO.INVOICE.LOG_ELEMENT = document.getElementById('debug');
	}

	if( YAHOO.INVOICE.LOG_ELEMENT )
	{
		YAHOO.INVOICE.LOG_ELEMENT.innerHTML += html;
	}
};


YAHOO.INVOICE.Store = function(location, data)
{
	var	handleSuccess = function(o)
	{
			YAHOO.INVOICE.Log( "<strong>Success:</strong><br>" );
			YAHOO.INVOICE.Log( "TID: " + o.tId + ", HTTP Status: " + o.status + ", Message: " + o.StatusText );
			YAHOO.INVOICE.Log( "<br><br>" );
	}

	var	handleFailure = function(o)
	{
			YAHOO.INVOICE.Log( "<strong>Failure:</strong><br>" );
			YAHOO.INVOICE.Log( "TID: " + o.tId + ", HTTP Status: " + o.status + ", Message: " + o.StatusText );
			YAHOO.INVOICE.Log( "<br><br>" );
	}

	var callback =
	{
		success: handleSuccess,
		failure: handleFailure
	};

	var sUrl = phpGWLink('index.php',
	{
    	menuaction: 'phpgwapi.template_portico.store',
        phpgw_return_as: 'json',
        location: location
	});

	var postData = 'data=' + JSON.stringify( data );
	YAHOO.INVOICE.Log( "<strong>Sending payload:</strong><pre>" + JSON.stringify( data ) + "</pre>" );
    var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);

};


YAHOO.INVOICE.BorderLayout = function()
{

	if(invoice_layout_config != 'undefined')
	{
		this.config = invoice_layout_config.length == 0 ? {} : invoice_layout_config;
	}
	else
	{
		this.config = {};
	}

	var self = this;

	this.buildWidget = function()
	{
		var DOM = YAHOO.util.Dom;
//		var layouts = Array( 'north', 'west', 'center', 'south' );
		var layouts = Array( 'north', 'west', 'center' );
		var layout = Array();

		// Collect layout units for border layout
		var layoutDom = document.getElementById('invoice-layout');
		for( i=0; i<layouts.length; i++ )
		{
			layout[ layouts[i] ] = DOM.getElementsByClassName( 'layout-' + layouts[i], 'div', layoutDom )[0];
		}

		if( typeof this.config.unitLeftWidth == 'undefined' )
		{
			this.config.unitLeftWidth = 800;
		}

		if( typeof this.config.unitRightWidth == 'undefined' )
		{
			this.config.unitRightWidth = 6;
		}	
		
		this.layout = new YAHOO.widget.Layout({
			minWidth: 600,
			minHeight: 400,
            units: [
				{ position: 'top', body: layout['north'], height: 26 },
				{ position: 'left', header: this.getHeader( layout['west'] ), body: layout['west'], width: this.config.unitLeftWidth, resize: true, scroll: true, gutter: "5px", collapse: true,  maxWidth: 1200, minWidth: 400 },
                { position: 'center', header: this.getHeader( layout['center'] ), body: layout['center'], scroll: true, gutter: "5px 0px" }
            ]
        });

        this.layout.render();

		this.layout.on('resize', this.handleResize );
	};

	this.handleResize = function() {
		
//		var collapsed = self.layout.getUnitByPosition('left')._collapsed;
		var unitLeftWidth = self.layout.getUnitByPosition('left').getSizes().wrap.w + 10;
		var unitRightWidth = 1;//Dummy//self.layout.getUnitByPosition('right').getSizes().wrap.w + 10;

		if( unitLeftWidth != self.config.unitLeftWidth ||
//			collapsed != self.config.collapsed ||
			unitRightWidth != self.config.unitRightWidth )
		{
			self.config.unitLeftWidth = unitLeftWidth;
			self.config.unitRightWidth = unitRightWidth;
//			self.config.collapsed = collapsed;

			YAHOO.INVOICE.Store( 'invoice_layout_config',
				self.config
			);
		}

	}

	// Helper function to find DIV.header inside a layout unit and return text of h2 element
	this.getHeader = function( node )
	{
		var title="";

		try
		{
			var div	= YAHOO.util.Dom.getElementsByClassName( 'header', 'div', node )[0];
			var header = div.getElementsByTagName('h2')[0];
			title = header.innerHTML;
		}
		catch (e)
		{
		}
		return title;
	};

	// Call "constructor"
	self.buildWidget();
};

YAHOO.util.Event.onDOMReady( YAHOO.INVOICE.BorderLayout );

	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center>"+oData+"</center>";
	}


