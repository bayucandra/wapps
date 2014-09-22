Ext.define('B.view.apps.mail.WMailAddrInput',{
	extend:'Ext.window.Window',
	requires:[
		'B.view.apps.mail.MailController'
	],
	controller:'mail',
	closeAction:'hide',
	id:'mail_addr_input',
	border:false,
	title:'Email address input',
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
				{text:'Contact Name',dataIndex:'contact_name',flex:3},
				{xtype:'actioncolumn',width:30,sortable:false,menuDisabled:true,
					items:[{
						icon:'res/images/btn-icons/def_delete.png',
						tooltip:'Delete email address',
						handler:'mailAddrBookInputDel'
					}]
				}
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
			inputId:'inp_to_addr',
			typeAhead:true,
			hideLabel:true,
			hideTrigger:true,
			minChars:2,
			queryMode:'remote',
			name:'rfc822_addr',
			flex:1,
			store:'SMailAddrBook',
//			displayField:'val',
			emptyText:'Input an email address then ENTER',
			listConfig:{
				getInnerTpl:function(){
					return '{addr}';
				}
			},
			listeners:{
				specialKey:'inpEnter',
				change:function(th,nv,ov,opts){
				    var store=Ext.getStore('SMailAddrBook');
				    if(store.getCount()<1){
					th.collapse();
				    }
				}
			},
			pageSize:extjs_conf.addrs_book_page_size
		},{
		    xtype:'button',
		    width:30,
		    text:'Add'
		}]
	}],
	listeners:{
		show:function(th,opts){
			var store_items=[];
			var input_value=th.obj_input.getValue();
			if(!bisnull(input_value)){
				var arr_rfc822_addrs=input_value.split(',');
				for(var i=0;i<arr_rfc822_addrs.length;i++){
					var arr_rfc822_addr=addr_rfc822_parsing(arr_rfc822_addrs[i]);
					store_items.push(arr_rfc822_addr);
				}
			}
			Ext.getStore('SMailAddrInput').loadData(store_items);
			th.down('form').reset();
			var field_label=th.obj_input.getFieldLabel();
			th.setTitle('\''+field_label+'\''+' email addresses input');
			th.center();
		},
		beforehide:function(th,opts){
			var rfc822_addrs_arr=[];
			Ext.getStore('SMailAddrInput').each(function(record,id){
				var rfc822_addr_str=record.get('email_address');
				if(!bisnull(record.get('contact_name'))){
					rfc822_addr_str=record.get('contact_name')+' <'+rfc822_addr_str+'>';
				}
				rfc822_addrs_arr[rfc822_addrs_arr.length]=rfc822_addr_str;
			});
			rfc822_addrs_arr.reverse();
			
			var rfc822_addrs_str='';
			for(var i=0;i<rfc822_addrs_arr.length;i++){
				rfc822_addrs_str=rfc822_addrs_str+rfc822_addrs_arr[i]+',';
			}
			rfc822_addrs_str=rfc822_addrs_str.substring(0,rfc822_addrs_str.length-1);
			th.obj_input.setValue(rfc822_addrs_str);
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
