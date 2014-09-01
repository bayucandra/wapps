 Ext.define('B.view.apps.mail.VMailAttachment',{
	extend:'Ext.grid.Panel',
	requires:[
		'B.view.apps.mail.MMailAttachment'
	],
	alias:'widget.VMailAttachment',
	title:'Attachments',
// 	store:'SMailInboxList',
	width:'30%',
	idmail_box:-1,
	columns:{
		defaults:{
			dragable:false,
			hideable:false
		},
		items:[
			{text:'Filename',flex:4,dataIndex:'basename'},
			{xtype:'actioncolumn',width:80,sortable:false,menuDisabled:true,
				items:[{
					icon:'res/images/ui-icons/save.png',
					tooltip:'Save attachment',
					handler:function(grid,rowIndex,colIndex){
						var selected_record=grid.getStore().getAt(rowIndex);
						var url_saving = document.URL+'res/php/email/att_save.php?order_no='+selected_record.get('order_no')+'&&idmail_box='+selected_record.get('idmail_box')+'&&basename='+selected_record.get('basename')+'&&disposition='+selected_record.get('disposition')+'&&type='+selected_record.get('type');
						window.frames['bsaving'].location.replace(url_saving);
					}
				}]
			}
		]
	},
	initComponent:function(){
		var att_store=Ext.create('Ext.data.Store',{
			model:'B.view.apps.mail.MMailAttachment',
			proxy:{
				type:'ajax',
				url:'res/php/crud.php?section=mail&&subsection=attachment&&idmail_box='+this.idmail_box,
				reader:{
					type:'json',
					rootProperty:'records'
				}
			}
		});
		att_store.load();
		Ext.apply(this,{
			store:att_store
		});
		this.callParent();
	}
});
