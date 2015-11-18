/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbar = 'Basic',
	config.uiColor = 'transparent',
	config.skin    = 'v2',
	config.removePlugins = 'elementspath',
	config.resize_enabled = false,
	config.EnterMode = 'p',
	config.ShiftEnterMode = 'br'
};
