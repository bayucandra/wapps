/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
   config.filebrowserBrowseUrl = '../include/kcfinder/browse.php?type=files';
   config.filebrowserImageBrowseUrl = '../include/kcfinder/browse.php?type=images';
   config.filebrowserFlashBrowseUrl = '../include/kcfinder/browse.php?type=flash';
   config.filebrowserUploadUrl = '../include/kcfinder/upload.php?type=files';
   config.filebrowserImageUploadUrl = '../include/kcfinder/upload.php?type=images';
   config.filebrowserFlashUploadUrl = '../include/kcfinder/upload.php?type=flash';
};
