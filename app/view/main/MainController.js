/**
 * This class is the main view for the application. It is specified in app.js as the
 * "autoCreateViewport" property. That setting automatically applies the "viewport"
 * plugin to promote that instance of this class to the body element.
 *
 * TODO - Replace this content of this view to suite the needs of your application.
 */
Ext.define('B.view.main.MainController', {
    extend: 'Ext.app.ViewController',

    requires: [
        'Ext.MessageBox'
    ],

    alias: 'controller.main',
    
    init: function(){
		this.control({
			'MenuMail':{
				selectionchange:'onSelectChangeBMenuMain'
			}
		});
	},
	onSelectChangeBMenuMain:function(selModel, records) {
		var record = records[0],
			text = record.get('text'),
			xtype = record.get('id'),
			alias = 'widget.'+xtype,
			contentPanel = Ext.getCmp('main-content'),
			cmp;
		if(xtype){//Make sure if has 'id'
			contentPanel.removeAll(true);
			var className=Ext.ClassManager.getNameByAlias(alias);
			var ViewClass=Ext.ClassManager.get(className);
			if(bisnull(ViewClass)){
				Ext.Msg.alert("Unimplemented","Sorry, not implemented yet.");
			}else{
				cmp=new ViewClass();
				contentPanel.add(cmp);
			}
		}
	},
	logout:function(){
		B.app.getController('Root').logout();
	}
});
