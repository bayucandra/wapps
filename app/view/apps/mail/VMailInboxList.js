Ext.define('B.view.apps.mail.VMailInboxList',{
	extend:'Ext.grid.Panel',
	alias:'widget.VMailInboxList',
	store:'SMailInboxList',
	scroll:'both',
	columns:{
		defaults:{
			dragable:false,
			hideable:false
		},
		items:[
// 			Ext.create('Ext.grid.RowNumberer'),
			{text:'Message',flex:6,dataIndex:'subject'
				,renderer:function(value,p,record){
						var content_short=Ext.util.Format.ellipsis(record.get('message_plain'),255);
						return Ext.String.format('<div><div class="funderline">{0}</div><div class="fgray9">{1}</div></div>'
							,record.get('subject')
							,content_short);
					}
			},
			{text:'From',flex:2,dataIndex:'addr_from'},
			{text:'Date received',flex:2,dataIndex:'message_date'}
		]
	}
}); 
