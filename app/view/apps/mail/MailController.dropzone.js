Ext.define('B.view.apps.mail.MailController',{
	extend:'Ext.app.ViewController',
	alias:'controller.mail',
	init:function(){
			this.control({
				'VMailTree':{
					selectionchange:'onSelectMailBox'
				}
			});
	},
	onSelectMailBox:function(selModel,records){
		var record = records[0],
			text = record.get('text'),
			xtype = record.get('id'),
			is_leaf=record.get('leaf'),
			alias = 'widget.'+xtype,
			contentPanel = Ext.getCmp('panel_mail_content'),
			cmp;
		if(!is_leaf)return false;
		if(xtype){//Make sure if has 'id'
			contentPanel.removeAll(true);
			var className=Ext.ClassManager.getNameByAlias(alias);
			var ViewClass=Ext.ClassManager.get(className);
			if(bisnull(ViewClass)){
				Ext.Msg.alert("Unimplemented","Sorry, not implemented yet.");
			}else{
				cmp=new ViewClass();
				contentPanel.add(cmp);
			}
		}
	},
	composeMail:function(btn){
		var panel_mail_compose=Ext.getCmp('panel_mail_compose');
		var is_init_collapsed=panel_mail_compose.getCollapsed();
		var is_init_tab_empty=false;
		var is_tab_ready_empty=false;
		panel_mail_compose.expand();
		var mail_compose_tab=Ext.getCmp('mail_compose_tab');
		if(!mail_compose_tab){
			is_init_tab_empty=true;
			mail_compose_tab=Ext.create('Ext.tab.Panel',{
				id:'mail_compose_tab',
				border:false,
				tabPosition:'right',
				titleAlign:'left',
				tabRotation:0
			});
			mail_compose_tab.on("add","tabAdd");
			
			panel_mail_compose.add(mail_compose_tab);
			panel_mail_compose.up().doLayout();
		}else{
			var tab_count=mail_compose_tab.items.items.length;
			if(tab_count===0)
				is_tab_ready_empty=true;
		}
		var compose_operation={};
		if(btn.getText()==='Reply'){
			var panel_reader=btn.up('panel[itemId^=id]');
			var reader_tab_itemId=panel_reader.getItemId();
			var msg=panel_reader.msg;
			compose_operation={'operation':'reply','ref_itemId':reader_tab_itemId,'msg':msg};
		}
		if((is_init_collapsed===false)||(is_init_tab_empty===true)||(is_tab_ready_empty===true)||(compose_operation.operation=='reply'))
			this.composeTabGen(compose_operation);
	},
	composeTabGen:function(p_obj){
		var operation='compose';
		var ref_itemId='';
		var msg='';
		if(!bisnull(p_obj.operation)){
			operation=p_obj.operation;
			ref_itemId=p_obj.ref_itemId;
			msg='<br><blockquote>'+p_obj.msg+'</blockquote>';
		}
		
		var mail_compose_tab=Ext.getCmp('mail_compose_tab');
		var tab_count=mail_compose_tab.items.items.length;
		var next_tab_idx=tab_count;
		
// 		var next_compose_idx=compose_idx+1;
		var reply_exist=false;
		var reply_exist_pos=-1;
		if(operation=='reply'){
			for(var i=0;i<tab_count;i++){
				var panel_ref_itemId=mail_compose_tab.getComponent(i).ref_itemId;
				var panel_ref_operation=mail_compose_tab.getComponent(i).operation;
				if((ref_itemId==panel_ref_itemId)&&(panel_ref_operation=='reply')){
					reply_exist=true;
					reply_exist_pos=i;
				}
			}
		}
		if(reply_exist){
			mail_compose_tab.setActiveTab(reply_exist_pos);
		}else{
			compose_idx++;//GLOBAL VARIABLE, increase only when create new compose tab
			var item_id='mail_compose_tab_'+compose_idx.toString();
			mail_compose_tab.add({
				xtype:'panel',
				operation:operation,
				ref_itemId:ref_itemId,
				title:'-NO SUBJECT-'+compose_idx.toString(),
				itemId:item_id,
				layout:{type:'hbox',align:'stretch'},
				items:[{
					xtype:'panel',
					flex:3,
					layout:'vbox',
					items:[{
						xtype:'VMailComposeTb',
						width:'100%',
						flex:4
					},{
						xtype:'panel',
						title:'Attachments',
						overflowY:'scroll',
						width:'100%',
						flex:6,
						html:'<div id="bdz_'+item_id+'" class="dropzone"></div>',
						listeners:{
							afterrender:function(th){
								var panel_itemId=th.up('panel[itemId^=mail_compose_tab_]').getItemId();
								var bdz_id='bdz_'+panel_itemId;
// 								Ext.Msg.alert('Msg',bdz_id);
								var bdz=new Dropzone("div#"+bdz_id,{
									paramName:'file_attach',
									url:'res/php/email.php?section=upload_att&&compose_itemId='+panel_itemId,
									addRemoveLinks:true,
									init:function(){
										var files_arr=[];
										this.on("complete",function(file){
											var current_height=th.up('panel[itemId^=mail_compose_tab_]').getHeight();
											th.doLayout();
											th.up('panel[itemId^=mail_compose_tab_]').setHeight(current_height);
											
											files_arr[files_arr.length]={'file_name':file.name,'size':file.size};
											
	// 										alert(th.up('panel').up('panel').getItemId());
											if (file._removeLink) {
												return file._removeLink.textContent = this.options.dictRemoveFile;
											}
										});
									}/*
									complete:function(file){
										var current_height=th.up('panel').up('panel').getHeight();
										th.doLayout();
										th.up('panel').up('panel').setHeight(current_height);
// 										alert(th.up('panel').up('panel').getItemId());
										if (file._removeLink) {
										return file._removeLink.textContent = this.options.dictRemoveFile;
										}
									}*/
								});
							}
						}
					}]
				},{
					xtype:'panel',
					flex:7,
					html:'<div id="mail_compose_tab_'+compose_idx.toString()+'">'+msg+'</div>'
				}],
				closable:true,
				listeners:{
					beforeclose:'mailComposeTabBeforeClose'/*,
					added:function(th,cont,pos,opts){
						Ext.Msg.alert('Tst',th.up('tabpanel').getComponent(0).getItemId());
					}*/
				}
			});
			mail_compose_tab.setActiveTab(next_tab_idx);
		}
	},
	tabAdd:function(th,added){/*
		if(added.operation==='reply'){
			var VMailInbox=Ext.getCmp('vmail-inbox');
			var ref_component=VMailInbox.getComponent(added.ref_itemId);
			Ext.Msg.alert('Tst',ref_component.down('VMailAddr').subject);
		}*/
		added.on("afterrender","mailComposeEditor");
	},
	mailComposeEditor:function(th){
// 		var tab_count=th.items.items.length;
// 		if(tab_count>0){
	
			if(th.operation==='reply'){
				var VMailInbox=Ext.getCmp('vmail-inbox');
				var ref_component=VMailInbox.getComponent(th.ref_itemId);
				var ref_VMailAddr=ref_component.down('VMailAddr');
				var subject_reply=ref_VMailAddr.subject;
				if(subject_reply.substr(0,3)!='Re:'){
					subject_reply='Re: '+subject_reply;
				}
				
				var form=th.down('form').getForm();/*
				var addr_from=ref_VMailAddr.addr_from.replace(/&lt;/gi,"<");
				addr_from=addr_from.replace(/&gt;/gi,">");*/
				form.setValues({
					subject:subject_reply,
					addr_to:addr_rfc822_htmldecode(ref_VMailAddr.addr_from)
				});
			}
			
			CKEDITOR.on("instanceReady",this.mailDoLayout);
			var active_tab_id=th.getItemId();
			CKEDITOR.replace(active_tab_id,{
				toolbarGroups:[
					{ name: 'tools' },
					{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
					{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align'/*, 'bidi'*/ ] },
					{ name: 'links' },
					'/',
					{ name: 'styles' },{ name: 'colors' },
					{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] }
				],
				height:'150px'
			});
// 		}
	},/*
	mailComposeData:function(e){
		this.mailDoLayout;
		e.editor.setData('');
	},*/
	mailDoLayout:function(e){
		mail_compose_tab=Ext.getCmp('mail_compose_tab');
		mail_compose_tab.up().doLayout();
		e.editor.focus();
	},
	mailComposeSend:function(btn){
		var panel_container=btn.up('panel[itemId^=mail_compose_tab_]');
		var tab_itemId=panel_container.getItemId();
		var ckeditor_val=CKEDITOR.instances[tab_itemId].getData();
		
		var bdz_id='bdz_'+tab_itemId;
		var bdz_obj=Dropzone.forElement('div#'+bdz_id);
		alert(bdz_obj.getAcceptedFiles()[1].size);
	},
	mailComposeTabBeforeClose:function(pnl){
		var itemId=pnl.getItemId();
		if(!bisnull(CKEDITOR.instances[itemId])){
			CKEDITOR.instances[itemId].destroy();
		}
	},
	mailAddrInputUI:function(p_obj_input,evt,opts){
		var mail_addr_input=Ext.getCmp('mail_addr_input');
		if(bisnull(mail_addr_input)){
			mail_addr_input=Ext.create('B.view.apps.mail.WMailAddrInput');
		}
		mail_addr_input.setObjInput(p_obj_input);
		mail_addr_input.show();
	},
	inpEnter:function(field,e){
		if(e.getKey()===e.ENTER){
			switch(field.getInputId()){
				case 'inp_to_addr':
					this.mailAddrBookInputAdd(field);
					break;
			}
		}
	},
	mailAddrBookInputAdd:function(p_field_ref){
		var str_addr=p_field_ref.getValue();
		if((str_addr==='')||(bisnull(str_addr))){
			return;
		}
		var arr_rfc822_addr=addr_rfc822_parsing(str_addr);
// 		Ext.Msg.alert('MSG',p_str_addr+'===='+arr_rfc822_addr['contact_name']+'====='+arr_rfc822_addr['email_address']);
		if(arr_rfc822_addr['email_address']===''){
			Ext.Msg.show({
				title:'Error',
				message:'Please input correct email address format',
				buttons:Ext.Msg.OK,
				icon:Ext.Msg.ERROR
			});
			return;
		}
		
		var rec=new B.view.apps.mail.MMailAddrInput({
			contact_name:arr_rfc822_addr['contact_name'],
			email_address:arr_rfc822_addr['email_address']
		});
		var grid_addresses=Ext.getCmp('mail_addr_input').getComponent('grid_addresses');
		if(grid_addresses.getStore().findRecord('email_address',arr_rfc822_addr['email_address'])===null)
			grid_addresses.getStore().insert(0,rec);
		else
			Ext.Msg.show({
				title:'Error',
				message:'Email address already exist at record list',
				buttons:Ext.Msg.OK,
				icon:Ext.Msg.ERROR
			});
		p_field_ref.setValue('');
	},
	mailAddrBookInputDel:function(grid, rowIndex){
		grid.getStore().removeAt(rowIndex);
	},mailSaveDraft:function(){
		
	},
	notImplemented:function(){
		Ext.Msg.show({
			title:'Not implemented',
			message:'Sorry, Handler function not implemented yet',
			buttons:Ext.Msg.OK,
			icon:Ext.Msg.ERROR
		});
	}
}); 
