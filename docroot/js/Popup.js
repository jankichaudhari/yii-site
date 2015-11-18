/**
 * Created with JetBrains PhpStorm.
 * User: vitaly.suhanov
 * Date: 16/07/12
 * Time: 14:04
 * To change this template use File | Settings | File Templates.
 */
var Popup = function (url, width, height)
{

	this._width = width || 1000;
	this._height = height || 800;
	this._closeOnUnload = true;

	this.popupWindow = null;

	this.width = function (value)
	{
		if (!value) {
			return this._width;
		}

		this._width = value;
		return this;
	}

	this.height = function (value)
	{
		if (!value) {
			return this._height;
		}

		this._height = value;
		return this;
	}


	this.closeOnUnload = function (value)
	{
		if (!value) {
			return this._closeOnUnload;
		}
		this._closeOnUnload = value;
		return this;
	}

	this.open = function ()
	{
		this.popupWindow = window.open(url, '', 'height=' + this._height + ' ,width=' + this._width + ' ,status=no,resizable=yes,scrollbars=yes');

		var self = this;

		if (this._closeOnUnload) {
			var listener = function ()
			{
				if (self.popupWindow && self.popupWindow.close) {
					self.popupWindow.close();
				}
			};
			if (window.addEventListener) {
				window.addEventListener('beforeunload', listener, false);
			} else {
				if (window.attachEvent) {
					window.attachEvent('onbeforeunload', listener);
				}
			}
		}
	}

	this.close = function() {
		if(this.popupWindow) {
			this.popupWindow.close();
		}
	}
}
