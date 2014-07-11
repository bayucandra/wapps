Ext.define('B.view.apps.mail.MailController',{
	extend:'Ext.app.ViewController',
	alias:'controller.mail',
	composeMail:function(){
		var statics=this.statics();
		var mail_compose_tab=Ext.getCmp('mail_compose_tab');
		if(!mail_compose_tab){
			var panel_mail_content=Ext.getCmp('panel_mail_content');
			
			var className=Ext.ClassManager.getNameByAlias('widget.VMailComposeTab');
			var ViewClass=Ext.ClassManager.get(className);
			var mail_compose_tab=new ViewClass;
			this.composeTabGen();
			
			Ext.apply(mail_compose_tab,{
				region:'north',
				split:true,
				flex:7
			});
			var mail_list_tab=Ext.getCmp('mail_list_tab');
// 			mail_list_tab.flex=2;
			Ext.apply(mail_list_tab,{
				flex:2
			});
			
			panel_mail_content.add(mail_compose_tab);
			panel_mail_content.doLayout();
		}
	},
	composeTabGen:function(){
		var mail_compose_tab=Ext.getCmp('mail_compose_tab');
		var tab_count=mail_compose_tab.items.items.length;
		if(tab_count==0){
			mail_compose_tab.add({
				title:compose_arr[0].subject,
				itemId:'mail_compose_tab_'+compose_arr[0].id.toString(),
				html:'<div id="mail_compose_tab_'+compose_arr[0].id.toString()+'"></div>',
				closable:true,
				dockedItems:[
					{
						dock:'top',
						xtype:'VMailComposeTb'
					}
				]
			});
			mail_compose_tab.setActiveTab(0);
			mail_compose_tab.on("afterrender",'tabChange');
// 			CKEDITOR.replace('mail_compose_tab_0');
		}
	},
	tabChange:function(th){
		var active_tab_id=th.getActiveTab().getItemId();
		CKEDITOR.replace(active_tab_id,{
			toolbarGroups:[
				{ name: 'tools' },
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
				{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
				{ name: 'links' },
				'/',
				{ name: 'styles' },{ name: 'colors' },
				{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] }
			]
		});
	}
}); 
