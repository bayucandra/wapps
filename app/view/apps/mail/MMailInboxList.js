Ext.define('B.view.apps.mail.MMailInboxList',{
	extend:'Ext.data.Model',
	fields:[
		{name:'addr_from',type:'string'},
		{name:'subject',type:'string'},
		{name:'message_date',type:'date',dateFormat:'Y-m-d H:i:s'},
		{name:'message_plain',type:'string'},
		{name:'message_html',type:'string'},
	],
	proxy:{
		type:'ajax',
		api:{
			create:'res/php/crud.php',
			read:'res/php/crud.php?section=mail&&subsection=inbox&&crud=read',
			update:'res/php/crud.php',
			destroy:'res/php/crud.php'
		},
		reader:{
			type:'json',
			rootProperty:'records',
		}
	}
});