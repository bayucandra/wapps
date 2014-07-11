Ext.define('B.view.apps.mail.VMailComposeTb',{
	extend:'Ext.panel.Panel',
	alias:'widget.VMailComposeTb',
	layout:'hbox',
	defaults:{
		xtype:'button',
		margin:'2 3 2 3'
	},
	items:[{
			xtype:'textfield',
			name:'to_addr',
			fieldLabel:'To',
			labelWidth:40
		},{
			xtype:'textfield',
			name:'subject',
			fieldLabel:'Subject',
			labelWidth:70
		},{
			text:'Send'
		},{
			text:'Save draft'
		},{
			text:'Cancel'
		}
	]
}); 
