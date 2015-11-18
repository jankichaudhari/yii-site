
var User = {
	parentWindow : null,
	selectWindow : null,

	init : function (win)
	{
		this.parentWindow = win || null;
		var self = this;
		var listener = function ()
		{
			if (self.selectWindow && self.selectWindow.close) {
				self.selectWindow.close();
			}
		};
		if (window.addEventListener) {
			window.addEventListener('beforeunload', listener, false);
		} else {
			if (window.attachEvent) {
				window.attachEvent('onbeforeunload', listener);
			}
		}
	},

	openSelectScreen : function (width, height)
	{
		width = width || 1000;
		height = height || 800;
		this.selectWindow = window.open('/admin4/User/Select', 'userSelect', 'resizable=1,height=' + height + ' ,width=' + width + ' ,status=no');
	},

	getDataById : function (id, callback)
	{
		$.getJSON('/admin4/User/getJSON', {'User' : id}, callback);
	},

	events : {},

	attachEvent : function (eventName, handler)
	{
		if (this.events.hasOwnProperty(eventName)) {
			this.events[eventName].push(handler);
		} else {
			this.events[eventName] = [handler];
		}
	},

	detachEvent : function (eventName, handler)
	{
		if (!this.events.hasOwnProperty(eventName)) {
			return;
		}
		var index = this.events[eventName].indexOf(handler);
		if (index != -1) {
			this.events[eventName].splice(index, 1);
		}
	},

	fireEvent : function (name, args)
	{
		if (this.parentWindow && this.parentWindow.User) {
			this.parentWindow.User.fireEvent(name, args);
		}
		if (!this.events.hasOwnProperty(name)) {
			return;
		}

		if (!args || !args.length) args = [];
		var events = this.events[name], l = events.length;
		for (var i = 0; i < l; i++) {
			events[i].apply(null, args);
		}
	},

	select : function(userIds) {
		this.fireEvent('onSelect', [userIds]);
	}
};

