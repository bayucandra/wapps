Ext.define('B.view.apps.mail.VMailInbox',{
	extend:'Ext.tab.Panel',
	alias:'widget.VMailInbox',
	defaults:{
		bodyPadding:5
	},
	layout:'fit',
	items:[{
		title:'Mail list',
		icon:'res/images/ui-icons/mail-list.png',
		layout:'fit',
		items:[{
			xtype:'VMailInboxList'
		}]
	}]
}); 
