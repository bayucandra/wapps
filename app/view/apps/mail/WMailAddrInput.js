Ext.define('B.view.apps.mail.WMailAddrInput',{
	extend:'Ext.window.Window',
	closeAction:'hide',
	id:'mail_addr_input',
	border:false,
	header:false,
	layout:'fit',
	modal:true,
	width:400,
	height:200,
	alias:'widget.WMailAddrInput',
	obj_input:false,
	items:[{
		xtype:'grid',
		itemId:'grid_addresses',
		columns:{
			defaults:{
				dragable:false,
				hideable:false
			},
			items:[
				Ext.create('Ext.grid.RowNumberer'),
				{text:'Email address',dataIndex:'email_address',flex:7},
				{text:'Contact Name',dataIndex:'contact_name',flex:3}
			]
		},
		store:'SMailAddrInput'
	}],
	dockedItems:[{
		xtype:'form',
		layout:'hbox',
		cls:'binput',
		items:[{
			xtype:'component',
			width:20,
			height:20,
			cls:'def_btn_new'
		},{
			xtype:'combo',
			name:'person_name',
			flex:7,
			store:'SMailAddrBook',
			valueField:'person_name',
			displayField:'person_name'
		},{
			xtype:'combo',
			name:'email',
			flex:3,
			store:'SMailAddrBook',
			valueField:'email',
			displayField:'email'
		}]
	}],
	listeners:{
		show:function(th,opts){
			var arr_rfc822_addrs=th.obj_input.getValue().split(',');
			var store_items=[];
			for(var i=0;i<arr_rfc822_addrs.length;i++){
				var arr_rfc822_addr=addr_rfc822_parsing(arr_rfc822_addrs[i]);
				store_items.push(arr_rfc822_addr);
			}
			Ext.getStore('SMailAddrInput').loadData(store_items);
		},
		blur:function(th,evt,opts){
			Ext.Msg.alert('Tst','Focus');
			th.hide();
		}
	},
	initComponent:function(){
		var me = this;
		me.callParent(arguments);
		me.mon(Ext.getBody(), 'click', function(el, e){
			me.close(me.closeAction);
		}, me, { delegate: '.x-mask' });
	},
	setObjInput:function(p_obj){
		this.obj_input=p_obj;
	}
}); 
