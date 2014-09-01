Ext.define('B.view.apps.mail.VMailAddr',{
	alias:'widget.VMailAddr',
	extend:'Ext.panel.Panel',
	border:false,
	personal_from:'',
	addr_from:'',
	subject:'',
	idmail_box:-1,
// 	init_html:'',
	layout:'hbox',
	defaults:{
		bodyStyle:'background:none',
		border:false
	},
	items:[{
			xtype:'panel',
			itemId:'addr_from',
			flex:4
		},{
			xtype:'panel',
			itemId:'addr_to',
			flex:6
		}],
	initComponent:function(){
// 		var addr_from_rfc822=((this.personal_from!='')&&(this.personal_from.length>0))?this.personal_from+' &lt;'+this.addr_from+'&gt;':this.addr_from;
		/*
		if((this.personal_from!='')&&(this.personal_from.length>0)){
			addr_from_rfc822=this.personal_from+' <'+this.addr_from+'>'
		}else{
			addr_from_rfc822=this.addr_from;
		}*/
		var addr_list='<table class="inbox-header-tbl"><tr><td class="label">From</td><td>'+this.addr_from+'</td></tr>';
		addr_list=addr_list+'<tr><td class="label">Subject</td><td style="font-weight:bold;">'+this.subject+'</td></tr>';
		addr_list=addr_list+'</table>';
		
// 		this.init_html=addr_list;
		this.callParent();
	},
	listeners:{
		afterrender:function(){
			var addr_list='<table class="inbox-header-tbl"><tr><td class="label">From</td><td>'+this.addr_from+'</td></tr>';
			addr_list=addr_list+'<tr><td class="label">Subject</td><td style="font-weight:bold;">'+this.subject+'</td></tr>';
			addr_list=addr_list+'</table>';
			this.getComponent('addr_from').update(addr_list);
			Ext.Ajax.request({
				url:'res/php/crud.php',
				scope:this,
				params:{
					section:'mail',
					subsection:'addr_list',
					idmail_box:this.idmail_box
				},
				success:function(response,opts){
// 					Ext.Msg.alert('Msg',response.responseText);
					var obj=Ext.decode(response.responseText);
					var table_addr='<table class="inbox-header-tbl">';
						var table_addr=table_addr+'<tr>';
							var table_addr=table_addr+'<td class="label">To</td>';
							var table_addr=table_addr+'<td><div class="addr-wrapper">';
								var tmp_addrs_to='';
								for(var i=0;i<obj.addr_to.length;i++){
									tmp_addrs_to=tmp_addrs_to+obj.addr_to[i]+', ';
								}
								tmp_addrs_to=tmp_addrs_to.substring(0,(tmp_addrs_to.length-2));
							var table_addr=table_addr+tmp_addrs_to+'</div></td>';
						var table_addr=table_addr+'</tr>';
						if(obj.addr_cc.length>0){
							var table_addr=table_addr+'<tr>';
								var table_addr=table_addr+'<td class="label">CC</td>';
								var table_addr=table_addr+'<td><div class="addr-wrapper">';
									var tmp_addrs_cc='';
									for(var i=0;i<obj.addr_cc.length;i++){
										tmp_addrs_cc=tmp_addrs_cc+obj.addr_cc[i]+', ';
									}
									tmp_addrs_cc=tmp_addrs_cc.substring(0,(tmp_addrs_cc.length-2));
								var table_addr=table_addr+tmp_addrs_cc+'</div></td>';
							var table_addr=table_addr+'</tr>';
						}
					var table_addr=table_addr+'</table>';
					this.getComponent('addr_to').update(table_addr);
				}
			});
// 			this.update(this.html+);
		}
	}
});
