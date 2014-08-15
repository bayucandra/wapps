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
	composeMail:function(){
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
				tabPosition:'right',
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
		if((is_init_collapsed===false)||(is_init_tab_empty===true)||(is_tab_ready_empty===true))
			this.composeTabGen();

	},
	composeTabGen:function(){
		var mail_compose_tab=Ext.getCmp('mail_compose_tab');
		var tab_count=mail_compose_tab.items.items.length;
		var next_tab_idx=tab_count;
		
		compose_idx++;
		var next_compose_idx=compose_idx+1;
			mail_compose_tab.add({
				xtype:'panel',
				title:"-NO SUBJECT-"+compose_idx.toString(),
				itemId:'mail_compose_tab_'+compose_idx.toString(),
				layout:'hbox',
				items:[{
					xtype:'VMailComposeTb',
					width:'30%'
				},{
					xtype:'panel',
					width:'70%',
					flex:0,
					html:'<div id="mail_compose_tab_'+compose_idx.toString()+'">'+loading_html+'</div>'
				}],
				closable:true,
				listeners:{
					beforeclose:'mailComposeTabBeforeClose'
				}
			});
			mail_compose_tab.setActiveTab(next_tab_idx);
	},
	tabAdd:function(th,ad){
		ad.on("afterrender","mailComposeEditor");
	},
	mailComposeEditor:function(th){
// 		var tab_count=th.items.items.length;
// 		if(tab_count>0){
			var active_tab_id=th.getItemId();
			CKEDITOR.on("instanceReady",this.mailComposeDoLayout);
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
	},
	mailComposeDoLayout:function(e){
		mail_compose_tab=Ext.getCmp('mail_compose_tab');
		mail_compose_tab.up().doLayout();
		e.editor.setData('');
	},
	mailComposeSend:function(btn){
		var panel_container=btn.up('panel[itemId^=mail_compose_tab_]');
		var tab_itemId=panel_container.getItemId();
		var ckeditor_val=CKEDITOR.instances[tab_itemId].getData();
		alert(ckeditor_val);
	},
	mailComposeTabBeforeClose:function(pnl){
		var itemId=pnl.getItemId();
		if(!bisnull(CKEDITOR.instances[itemId])){
			CKEDITOR.instances[itemId].destroy();
		}
	}
}); 
