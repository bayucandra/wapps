Ext.define('B.store.SMailInboxList',{
	extend:'Ext.data.Store',
	model:'B.view.apps.mail.MMailInboxList',
	remoteSort:true,
	autoLoad:true
});