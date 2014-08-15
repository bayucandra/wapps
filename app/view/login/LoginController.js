Ext.define('B.view.login.LoginController',{
	extend:'Ext.app.ViewController',
	id:'controller_login',
	alias:'controller.login',
	onLogin:function(){
// 		B.app.getController('Root').onLog();
		this.login();
	},
	login:function(){
		var form=this.getView().down('form');
		var username=form.getValues(false,false,false,true).username;
		var password=form.getValues(false,false,false,true).password;
		if(!form.isValid()){
			Ext.Msg.show({
				title:'Form invalid',
				message:'Please fill the fields correctly',
				buttons:Ext.Msg.OK,
				icon:Ext.Msg.ERROR
			});
			return;
		}
		Ext.Msg.show({
			closable:false,
			title:'Loging in',
			message:'Please wait...',
			wait:true
		});
		Ext.Ajax.request({
			url:'res/php/user.php',
			scope:this,
			params:{
				login:true,
				email:username,
				password:password
			},
			success:function(response,opts){
				var obj=Ext.decode(response.responseText);
				var success=obj.success;
				var idmail_account=obj.idmail_account;
				var error_msg=obj.error_msg;
				Ext.Msg.hide();
				if(success===false){
					Ext.Msg.show({
						title:'Invalid login',
						message:error_msg,
						buttons:Ext.Msg.OK,
						icon:Ext.Msg.ERROR
					});
				}else{
					B.app.getController('Root').phpSession('idmail_account',idmail_account);
					B.app.getController('Root').showUI();
					var loginWindow=Ext.getCmp('mail_login_window');
					loginWindow.destroy();
				}
			},
			failure:function(response,opts){
				Ext.Msg.alert("Error","Error when loging in.");
			}
		});
	},
	onSpecialKey:function(field, e){
		if(e.getKey()===e.ENTER){
			this.onLogin();
// 		alert('test');
		}
	}
}); 
