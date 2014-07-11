/**
 * The main application class. An instance of this class is created by app.js when it calls
 * Ext.application(). This is the ideal place to handle application launch and initialization
 * details.
 */
Ext.define('B.Application', {
    extend: 'Ext.app.Application',
    
    name: 'B',

    views: [
        // TODO: add views here
	'apps.mail.VMail','apps.mail.VMailBox','apps.mail.VMailListTab', 'apps.mail.VMailComposeTab', 'apps.mail.VMailComposeTb'
	,'menu.MenuMail'
    ],

    controllers: [
        'Root',
        // TODO: add controllers here
    ],

    stores: [
        // TODO: add stores here
	'apps.mail.SMailBox'
    ],
    
    launch: function () {
        // TODO - Launch the application

    }
});
