Ext.define('B.view.apps.mail.MMailAttachment',{
	extend:'Ext.data.Model',
	fields:[
		{name:'order_no',type:'int'},
		{name:'idmail_box',type:'int'},
		{name:'basename',type:'string'},
		{name:'disposition',type:'string'},
		{name:'type',type:'int'}
	]
});
