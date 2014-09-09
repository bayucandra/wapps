Ext.define('B.view.apps.mail.VMailComposeTb',{
	extend:'Ext.form.Panel',
	alias:'widget.VMailComposeTb',
	bodyPadding:3,
	border:false,
	defaults:{
		xtype:'textfield',
		labelWidth:60
	},
	items:[
		{
			name:'addr_to',
			fieldLabel:'To',
			width:'100%',
			listeners:{
				focus:'mailAddrInputUI'
			}
		},{
			name:'addr_cc',
			fieldLabel:'CC',
			width:'100%',
			listeners:{
				focus:'mailAddrInputUI'
			}
		},{
			xtype:'textarea',
			name:'subject',
			fieldLabel:'Subject',
			height:40,
			width:'100%',
			maxLength:100,
			enforceMaxLength:true,
			maxLengthText:'Subject field is limited to {0} of characters only',
			listeners:{
				change:function(th,nv,ov,opts){
					var title=nv;
					if(bisnull(nv))
						title='-NO SUBJECT-';
					th.up('panel').up('panel').up('panel').setTitle(this.titleGen(title));
				}
			},
			titleGen:function(p_str){
				return Ext.util.Format.ellipsis(p_str,25);
			}
		}
	],
	tbar:[{
			text:'Send',
			handler:'mailComposeSend'
		},{
			text:'Save draft'
		},{
			text:'Cancel'
	}]
}); 
