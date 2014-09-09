 Ext.define('B.store.SMailAddrBook',{
	extend:'Ext.data.Store',
	idStore:'SMailAddrBook',
	model:'B.view.apps.mail.MMailAddrBook',
	pageSize:extjs_conf.addrs_book_page_size,
	autoLoad:true
});
