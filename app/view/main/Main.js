/**
 * This class is the main view for the application. It is specified in app.js as the
 * "autoCreateViewport" property. That setting automatically applies the "viewport"
 * plugin to promote that instance of this class to the body element.
 *
 * TODO - Replace this content of this view to suite the needs of your application.
 */
Ext.define('B.view.main.Main', {
    extend: 'Ext.container.Container',

    xtype: 'app-main',
    
    controller: 'main',
    viewModel: {
        type: 'main'
    },

    layout: {
        type: 'border'
    },

    items: [{
      xtype:'panel',
      region:'north',
      bodyStyle:{"background-color":"#DFE8F6"},
      bodyPadding: 2,
      html:'<div id="app-title" class="farial fbold f20"><img style="float:left;margin:0 0 0 5px;" src="res/images/wisanka.png" /><div style="float:left;margin:5px 0 0 5px;">'+app_detail.app_name+' <span style="font-size:12px">V.'+app_detail.app_version+'</span></div><div class="logout"><a href="?logout=true">Logout</a></div></div>'
    },{
        xtype: 'panel',
        title: 'Main menu',
        region: 'west',
        width: 200,
	minWidth:150,
	maxWidth:350,
        split: true,
	collapsible:true,
        items:[
		{xtype:'MenuMail'}
	],
	layout:'accordion'
    },{
        region: 'center',
        xtype: 'panel',
	id:'main-content',
        html:'<h1>Wisanka Applications</h1>',
	layout:'fit'
    }]
});
