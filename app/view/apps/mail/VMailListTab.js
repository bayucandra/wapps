Ext.define('B.view.apps.mail.VMailListTab',{
	extend:'Ext.tab.Panel',
	alias:'widget.VMailListTab',
	defaults:{
		bodyPadding:10
	},
	border:false,
	items:[{
		title:'Mail list',
		html:'Mail Box'
	},{
		title:'Read',
		html:'Tst',
		closable: true
	}]
}); 
