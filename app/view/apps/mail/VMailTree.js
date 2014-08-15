Ext.define('B.view.apps.mail.VMailTree',{
	extend:'Ext.tree.Panel',
	alias:'widget.VMailTree',
	useArrows:true,
	tbar:[{
			text:'Compose',
			handler:'composeMail'
		}
	],
	initComponent:function(){
		var node_children=[];
		node_children.push({id:'VMailInbox',text:'Inbox',leaf:true});
		node_children.push({id:'VMail-Sent',text:'Sent',leaf:true});
		Ext.apply(this,{
			store: new Ext.data.TreeStore({
				root:{
					text:'Mail box',
					expanded:true,
					children:node_children
				},
				folderSort:true
			}),
            viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop',
                    containerScroll: true
                }
            }
		});
		this.callParent();
	}
// 	store:'apps.mail.SMailBox'
}); 
