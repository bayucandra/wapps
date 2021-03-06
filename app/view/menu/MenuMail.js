Ext.define('B.view.menu.MenuMail',{
	extend:'Ext.tree.Panel',
	alias:'widget.MenuMail',
	xtype:'MenuMail',
	title:'Mail',
	useArrows:true,
	rootVisible:false,
	initComponent:function(){
		var node_children=[];
		node_children.push({id:'VMail',text:'E-Mail',leaf:true});
		node_children.push({id:'VMailSetting',text:'Setting',leaf:true});
		Ext.apply(this,{
			store: new Ext.data.TreeStore({
				root:{
					id:'src',
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
}); 
