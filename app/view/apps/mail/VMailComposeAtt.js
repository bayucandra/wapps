Ext.define('B.view.apps.mail.VMailComposeAtt',{
	extend:'Ext.grid.Panel',
	height:150,
// 	hideHeaders:true,
	compose_itemId:'',
	alias:'widget.VMailComposeAtt',
	store:'SMailComposeAtt',
	columns:{
		defaults:{dragable:false,hideable:false},
		items:[
			{text:'File name',dataIndex:'file_name',flex:7},
			{text:'Status',dataIndex:'status',flex:2},
			{xtype:'actioncolumn',width:30,sortable:false,menuDisabled:true,
				items:[{
					icon:'res/images/btn-icons/def_delete.png',
					tooltip:'Delete attachment',
					scope:this,
					handler:'notImplemented'
				}]
			}
		]
	},
	listeners:{
		afterrender:function(th,opts){
			var docked=th.getDockedComponent(1).getForm();
			docked.setValues({compose_itemId:th.compose_itemId});
		}
	},
	dockedItems:[{
		dock:'top',
		xtype:'form',
		border:false,
		layout:'hbox',
		bodyPadding:5,
		items:[{
			xtype:'hidden',
			name:'compose_itemId'
		},{
			xtype:'filefield',
			buttonOnly:true,
			hideLabel:true,
			name:'file_attach',
			buttonText:'Attach file',
			listeners:{
				'change':function(th,v){
					var form=th.up('form').getForm();
					var grid=th.up('grid');
					var store=grid.getStore();
					
					var rec=new B.view.apps.mail.MMailComposeAtt({
						file_name:v,
						status:'uploading...'
					});
					grid.getStore().insert(0,rec);
					form.submit({
						url:'res/php/email.php?section=upload_att',
						success:function(fp,o){
// 							Ext.Msg.alert('Tst',o.result.message);
							var tmp_str='';/*
							Ext.each(store.query('file_name',o.result.file_name).items,function(x){
								Ext.Msg.alert('Tst',x.data.file_name);
							});*/
// 							console.log(store.getRange());
// 							Ext.Msg.alert('Tst',store.getRange().file_name);
							store.each(function(record,id){
								if(record.get('file_name')==v){
									record.set('status','Attached');
								}
							});
							store.sync();
						},
						failure:function(){
							Ext.Msg.show({
								title:'Error',
								msg:this.response.responseText,
								icon:Ext.Msg.ERROR
							});
						}
					});
				}
			}
		}]
	}]
}); 
