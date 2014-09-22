/**
 * The main application class. An instance of this class is created by app.js when it calls
 * Ext.application(). This is the ideal place to handle application launch and initialization
 * details.// Ext.Loader.setPath('Ext.ux','app/ux');
 */
Ext.define('B.Application', {
    extend: 'Ext.app.Application',
    
    name: 'B',

    views: [
        // TODO: add views here
	'main.Main','apps.mail.VMail','apps.mail.VMailTree','apps.mail.VMailInbox', 'apps.mail.VMailInboxList','apps.mail.VMailAddr','apps.mail.VMailAttachment', 'apps.mail.VMailComposeForm', 'apps.mail.VMailComposeAtt', 'apps.mail.WMailAddrInput'
	,'menu.MenuMail'
    ],

    controllers: [
        'Root'
        // TODO: add controllers here
    ],

    stores: [
        // TODO: add stores here
	'SMailInboxList','SMailAddrInput','SMailAddrBook'
    ],
    
    launch: function () {
        // TODO - Launch the application

    }
});
