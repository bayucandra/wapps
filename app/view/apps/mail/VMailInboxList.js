Ext.define('B.view.apps.mail.VMailInboxList',{
	extend:'Ext.grid.Panel',
	border:false,
	alias:'widget.VMailInboxList',
	requires:[
		'B.view.apps.mail.MailController'
	],
	store:'SMailInboxList',
	scroll:'both',
	controller:'mail',
	columns:{
		defaults:{
			dragable:false,
			hideable:false
		},
		items:[
// 			Ext.create('Ext.grid.RowNumberer'),
			{text:'Message',flex:6,dataIndex:'subject'
				,renderer:function(value,p,record){
						var att_icon='';
						if(record.get('att_count')>0){
							att_icon='<img alt="Has attachment" src="res/images/ui-icons/att-18.png"/>';
						}
						var content_short=Ext.util.Format.ellipsis(record.get('message_plain'),255);
						return Ext.String.format('<div class="mail-att-icon">{0}</div><div><div class="funderline">{1}</div><div class="fgray9">{2}</div></div>'
							,att_icon,record.get('subject')
							,content_short);
					}
			},
			{text:'From',flex:2,dataIndex:'addr_from'},
			{text:'Date received',flex:2,dataIndex:'message_date'}
		]
	},
	initComponent:function(){
		Ext.getStore('SMailInboxList').reload();
		this.callParent();
	},
	listeners:{
		itemdblclick:function(th,rec,itm,idx,e,opts){
			var VMailInbox=th.up('VMailInbox');
			var msg=rec.get('message_plain');
			var title=Ext.util.Format.ellipsis(rec.get('subject'),20);
			if(rec.get('message_html')!=''){
				msg=rec.get('message_html');
			}
// 			Ext.Msg.alert('Test',VMailInbox.items.items.length);
			var tab_itemId='id-'+rec.get('idmail_box');
			var inbox_reader=VMailInbox.getComponent(tab_itemId);
			var css_bg_header='#f4f4f4'
			var msg_items=[{
						xtype:'panel',
						region:'center',
						overflowY:'auto',
						html:'<div style="padding:10px;">'+msg+'</div>'
					}];
			if(rec.get('att_count')>0){
				msg_items.push({
					xtype:'VMailAttachment',
					region:'east',
					idmail_box:rec.get('idmail_box'),
					split:true,
					collapsible:true
				});
			}
			if(bisnull(inbox_reader)){
				VMailInbox.add({
					xtype:'panel',
					itemId:tab_itemId,
					closable:true,
					defaults:{
						border:false
					},
					title:title,
					layout:'border',
					msg:msg,
					items:msg_items,
					dockedItems:[{
						dock:'top',
						xtype:'panel',
						layout:'vbox',
						bodyPadding:5,
						bodyStyle:'background-color:'+css_bg_header+';margin:1px 0 0 0',
						items:[{
							xtype:'panel',
							border:false,
							bodyStyle:'background-color:'+css_bg_header,
							defaults:{
								xtype:'button',
								style:{
									margin:'0 3px 0 3px'
								}
							},
							items:[{
								text:'Reply',
								handler:'composeMail'
							},{
								text:'Reply All',
								handler:'notImplemented'
							},{
								text:'Forward',
								handler:'notImplemented'
							}]
						},{
							xtype:'VMailAddr',
							width:'100%',
							border:false,
							addr_from:rec.get('addr_from'),
							subject:rec.get('subject'),
							idmail_box:rec.get('idmail_box'),
							bodyStyle:'background-color:'+css_bg_header
						}]
					}]
				});
			}else{
				VMailInbox.setActiveTab(inbox_reader);
			}
		}
	}
}); 
