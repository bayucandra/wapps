Ext.define('B.view.apps.mail.MMailAddrBook',{
	extend:'Ext.data.Model',
	fields:[
		{name:'addr',type:'string'},
		{name:'val',type:'string'}
	],
	proxy:{
		type:'ajax',
		api:{
			create:'res/php/crud.php',
			read:'res/php/crud.php?section=mail&&subsection=addr_book',
			update:'res/php/crud.php',
			destroy:'res/php/crud.php'
		},
		reader:{
			type:'json',
			rootProperty:'records',
			totalProperty:'totalCount'
		}
	}
}); 
