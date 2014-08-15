Ext.define('B.view.apps.mail.VMailComposeTb',{
	extend:'Ext.panel.Panel',
	alias:'widget.VMailComposeTb',
	layout:'vbox',
	bodyPadding:3,
	defaults:{
		xtype:'textfield',
		labelWidth:60
	},
	items:[
		{
			name:'to_addr',
			fieldLabel:'To',
			width:'100%'
		},{
			name:'cc',
			fieldLabel:'CC',
			width:'100%'
		},{
			xtype:'textarea',
			name:'subject',
			fieldLabel:'Subject',
			height:40,
			width:'100%',
			maxLength:100,
			enforceMaxLength:true,
			maxLengthText:'Subject field is limited to {0} of characters only'
		},{
			xtype:'panel',
			defaults:{
				xtype:'button',
				margin:'2 3 2 3'
			},
			layout:'hbox',
			items:[{
					text:'Send',
					handler:'mailComposeSend'
				},{
					text:'Save draft'
				},{
					text:'Cancel'
				
			}]
		}
	]
}); 
