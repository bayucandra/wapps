Ext.define('B.store.apps.mail.SMailBox',{
	extend:'Ext.data.TreeStore',
	root:{
		expanded:true,
		text:'Mail Box'
	},
	proxy:{
		type:'ajax',
		url:'res/php/crud.php?section=mailbox&&crud=read',
		reader:{
			type:'xml',
			rootProperty:'mailbox',
			record:'node'
		}
	}
});