/**
 * The main application controller. This is a good place to handle things like routes.
 */
Ext.define('B.controller.Root', {
	extend: 'Ext.app.Controller',
	requires:[
		'B.view.login.Login'
	],
	onLaunch:function(){
		if(Ext.isIE8){
			Ext.Msg.alert('Not Supported', 'Your browser is not suppoted to open this App. Please get newest browser available');
			return;
		}
		Ext.Ajax.request({
			url:'res/php/ajax_session.php',
			params:{
				act:'get'
			},
			scope:this,
			success:function(response,opts){
				var obj=Ext.decode(response.responseText);
				if(!bisnull(obj.idmail_account)){
					this.showUI();
				}else{
					this.loginShow();
				}
			}
		});
		var wapps_loading=document.getElementById('wapps_loading');
		wapps_loading.parentNode.removeChild(wapps_loading);
	},
	showUI:function(){
		this.viewport=new B.view.main.Main;
	},
	phpSession:function(p_key,p_val){
		Ext.Ajax.request({
			url:'res/php/ajax_session.php',
			params:{
				act:'set',
				key:p_key,
				val:p_val
			}
		});
	},
	loginShow:function(){
		this.login=new B.view.login.Login({
			autoShow:true
		});
		var login_form=this.login.down('form').getForm();
		var username=login_form.findField('username');
		username.focus();
	},
	logout:function(){
		Ext.Ajax.request({
			url:'res/php/ajax_session.php',
			params:{
				act:'destroy'
			},
			scope:this,
			success:function(){
				window.location.reload();
			}
		});
	}
});
