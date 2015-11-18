// user-filter, user-list, User_Select_user-filter
var AdminFilterForm = function (formName, gridViewSelector, formId)
{
	if (this == window) {
		AdminFilterForm.forms[formName] = AdminFilterForm.forms[formName] || new AdminFilterForm(formName, gridViewSelector, formId);
		return AdminFilterForm.forms[formName];
	}

	this.name = formName;

	this.formId = formId || formName;

	this.formSelector = '#' + formName;
	this.gridViewSelector = '#' + gridViewSelector;

	this.ajaxEnabled = true;

	this.formData = null;

	this.reset = function ()
	{
		var link = '?resetFilter_' + this.formId;
		if (document.location.search) {
			link = document.location.search.replace('resetFilter_' + this.formId, '') + '&resetFilter_' + this.formId;

		}
		document.location.href = link;
	}

	this.init = function (params)
	{

		if (params) {
			for (param in params) {
				if (this[param] !== undefined) {
					this[param] = params[param];
				}
			}
		}

		if (this.gridViewSelector && this.ajaxEnabled) {
			$(this.formSelector).on('change', 'input[type=checkbox],input[type=radio],input[type=],select', this._run());
			$(this.formSelector).on('keyup', 'input[type=text]', this._run());
		}
		return this;
	};

	this.run = function ()
	{
		var link = document.location.href.replace('?resetFitler_' + this.formId, "");
		var link = link.replace('&resetFitler_' + formId, "");

		this.formData = $(this.formSelector).serializeArray();

		for (key in this.formData) {
			link = link.replace(this.formData[key].name + '=' + this.formData[key].value, ""); // remove all paramas from query string.
		}

		if (!this.fireEvent('onBeforeAjaxFilter', [this.formData])) return false;

		if (this.excluded.length > 0 || this.excludedByModels.length > 0) {

			$(this.formSelector + ' .excluded').remove();

			if (this.excludedByModels.length > 0) {
				for (model in this.excludedByModels) {
					var hiddenEl = document.createElement('input');
					hiddenEl.type = 'hidden';
					hiddenEl.className = 'excluded';
					hiddenEl.name = 'excluded[' + model + ']';
					hiddenEl.value = this.excludedByModels[model].join(",");
					$(this.formSelector).append(hiddenEl);
				}
			} else {
				var hiddenEl = document.createElement('input');
				hiddenEl.type = 'hidden';
				hiddenEl.className = 'excluded';
				hiddenEl.name = 'excluded';
				hiddenEl.value = this.excluded.join(",");
				$(this.formSelector).append(hiddenEl);
			}
		}

		this.formData = $(this.formSelector).serializeArray();


		var self = this;

		$.get(link, this.formData, function (data)
		{
			$(self.gridViewSelector).replaceWith($(self.gridViewSelector, data));
			self.fireEvent('onAfterAjaxFilter', [self.formData]);
		});
	}

	this.onBeforeAjaxFilter = function (data)
	{
		this.fireEvent('onBeforeAjaxFilter', data);
	}

	this.onAfterAjaxFilter = function (data)
	{
		this.fireEvent('onAfterAjaxFilter', data);
	}

	this.queued = null;

	this._run = function ()
	{
		var self = this;
		return function ()
		{
			if (self.queued) {
				clearTimeout(self.queued);
			}
			self.queued = setTimeout(function ()
									 {
										 AdminFilterForm(self.name).run.call(self)
									 }, 200);
		}
	}

	this.excluded = [];
	this.excludedByModels = {};

	/**
	 * this method will add a hidden fields to explude some records from filtering.
	 * if we use more than one model in filtering it should be passed here.
	 * by default first model will be used;
	 *
	 *
	 * @param keys Array
	 * @param model String
	 */
	this.exclude = function (keys, model)
	{
		if (!model) {
			this.excluded = keys
		} else {
			this.excludedByModels[model] = keys;
		}
	}

	this.unexclude = function (keys, model)
	{
		if (!model) {
			for (var i = 0; i < keys.length; i++) {
				var index = this.excluded.indexOf(keys[i]);
				if (index != -1) {
					this.excluded.splice(index, 1);
				}
			}
		} else {
			for (var i = 0; i < keys.length; i++) {
				var index = this.excludedByModels[model].indexOf(keys[i]);
				if (index != -1) {
					this.excludedByModels[model].splice(index, 1);
				}
			}
		}
	}


	this.events = {};

	this.attachEvent = function (eventName, handler)
	{
		if (this.events.hasOwnProperty(eventName)) {
			this.events[eventName].push(handler);
		} else {
			this.events[eventName] = [handler];
		}
	}


	this.detachEvent = function (eventName, handler)
	{
		if (!this.events.hasOwnProperty(eventName)) {
			return;
		}
		var index = this.events[eventName].indexOf(handler);
		if (index != -1) {
			this.events[eventName].splice(index, 1);
		}
	}


	this.fireEvent = function (name, args)
	{
		if (!this.events.hasOwnProperty(name)) {
			return true; // no listeners for this event
		}

		if (!args || !args.length) args = [];
		var events = this.events[name], l = events.length;
		for (var i = 0; i < l; i++) {
			if (!events[i].apply(null, args)) return false;
		}

		return true;
	}
};
AdminFilterForm.forms = {};

