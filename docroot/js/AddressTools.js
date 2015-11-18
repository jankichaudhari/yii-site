var AddressTools = function (id) {
    if (this == window) {
        AddressTools.addresses[id] = AddressTools.addresses[id] || new AddressTools(id);
        return AddressTools.addresses[id];
    }
    AddressTools.addresses[id] = this; // in case we create new instance by new AddressTools(id)

    var self = this;

    this.id = id;

    this.addressIdField = $('#' + this.id + '_addressId');

    this.container = $('#' + this.id + '_container');

    this.addressStringContainer = $('#' + this.id + '_addressStringContainer');
    this.addressStringElement = $('#' + this.id + '_addressStringElement');
    this.changeButton = $('#' + this.id + '_changeButton');
    this.showOnMapButton = $('#' + this.id + '_showOnMapButton');

    this.searchContainer = $('#' + this.id + '_searchAddressContainer');
    this.searchAddressField = $('#' + this.id + '_searchAddressField');

    this.postcodeInputField = $('#' + this.id + '_postcodeInputField');
    this.saveAddressButton = $('#' + this.id + '_saveAddressButton');


    this.manualAddContainer = $('.' + this.id + '_manualAddContainer');
    this.lineFields = $('.' + this.id + '_lineField');
    this.addManuallyButton = $('#' + this.id + '_addManuallyButton');

    this.lookupContainer = $('#' + this.id + '_lookupContainer');
    this.lookupButton = $('#' + this.id + '_lookupButton');
    this.lookupResultSelect = $('#' + this.id + '_lookupResultSelect');
    this.errorContainer = $('#' + this.id + '_errorContainer');


    this.showLookupForm = function () {
        this.lookupContainer.show();
        this.postcodeInputField.focus();
    }

    this.selectAddress = function (address) {
        this.addressIdField.val(address.id);
        this.addressStringElement.html(address.address);

        this.addressStringContainer.show();
        this.changeButton.show();
        this.showOnMapButton.show();
        this.searchContainer.hide();
        this.lookupContainer.hide();
        this.manualAddContainer.hide();
    };

    this.fetchAndSelect = function () {
        var id = self.lookupResultSelect.val();
        $.getJSON('/admin4/Address/fetchAndSelect/', {'id': id}, function (result) {
            if (result.errorCode == 0) {
                if (result.data.length == 1) {
                    self.selectAddress(result.data[0]);
                }
            } else {
                if (result.errorCode == 2) {
                    // address already exists
                } else {
                    // serious error
                }
            }
        });
    };

    this.init = function () {

        this.changeButton.on('click', function () {
            self.searchContainer.show();
            self.searchAddressField.val('').focus();
            self.changeButton.hide();
        });

        this.showOnMapButton.on('click', function () {
            if (!self.addressIdField.val()) {
                return alert('An error occured; show on map button should only appear if address is selected. please contact administrator.');
            }
            new Popup('/admin4/Address/showOnMap/id/' + self.addressIdField.val()).open();
        })

        this.searchAddressField.autocomplete({
            source: function (request, response) {
                $.getJSON('/admin4/Address/autocomplete/search/' + request.term, function (data) {
                    data.push({'label': 'Add New Address...', 'value': '', 'id': 'new'});
                    response(data);
                });
            },

            select: function (event, ui) {
                if (ui.item.id != 'new') {
                    self.selectAddress({
                        'id': ui.item.id,
                        'address': ui.item.value
                    });
                } else {
                    self.showLookupForm();
                }
            }
        });


        this.postcodeInputField.on('keypress', function (event) {
            if (event.keyCode == 13) {
                self.lookup();
                event.preventDefault();
            }
        });
        this.lookupButton.on('click', this.lookup);

        this.addManuallyButton.on('click', function () {
            self.lookupResultSelect.hide();
            self.manualAddContainer.show();
            self.lineFields[0].focus();
            self.addManuallyButton.hide();
        });

        this.saveAddressButton.on('click', function () {
            var str = {};

            self.lineFields.each(function () {
                str[this.name] = this.value;
            });
            str['postcode'] = self.postcodeInputField.val();

            $.getJSON('/admin4/address/createAndSelect', str, function (result) {
                console.log(result);
                if (result.errorCode == 0) {
                    if (result.data.length == 1) {
                        self.selectAddress(result.data[0]);
                    }
                } else {
                    if (result.errorCode == 2) {
                        for (var i = 0; i < result.data.length; i++) {

                        }
                    } else {

                        self.errorCotainer.html('Oops! an error has occured');
                    }
                }
            });
        });

        this.lookupResultSelect.on('keypress', function (event) {
            if (event.keyCode == 13) {
                self.fetchAndSelect();
                event.preventDefault();
            }
        });

        $('body').on('dblclick', '.' + this.id + '_address-option', function () {
            self.fetchAndSelect();
        });
    };


    this.lookup = function () {
        self.errorContainer.hide();
        var postcode = self.postcodeInputField.val();
        $.getJSON('/admin4/Address/LookupNew/', {'postcode': postcode}, function (data) {
            if (data.errorCode == 0) {
                var options = '';
                for (var i = 0; i < data.items.length; i++) {
                    options += '<option class="' + self.id + '_address-option" value="' + data.items[i].id + ' ">' + data.items[i].value + '</option>';
                }
                self.lookupResultSelect.html(options).show().focus();
            } else {
                var html = 'Oops! an error has occured!';
                html += '<ul>';
                for (var i = 0; i < data.errors.length; i++) {
                    html += '<li>' + data.errors[i] + '</li>';
                }
                html += '</ul>';
                self.errorContainer.html(html);
                self.errorContainer.show();
            }
        });
    };
};


AddressTools.addresses = {};
AddressTools.exists = function (name) {
    if (AddressTools.addresses[name]) {
        return true;
    }
    return false;
}


var withEvents = function () {
    this.events = {};

    this.attachEvent = function (eventName, handler) {
        if (this.events.hasOwnProperty(eventName)) {
            this.events[eventName].push(handler);
        } else {
            this.events[eventName] = [handler];
        }
    }


    this.detachEvent = function (eventName, handler) {
        if (!this.events.hasOwnProperty(eventName)) {
            return;
        }
        var index = this.events[eventName].indexOf(handler);
        if (index != -1) {
            this.events[eventName].splice(index, 1);
        }
    }


    /**
     * Fires all events. if any event returns false. it breaks the sequence and all next events will not be executed;
     * @param name
     * @param args
     */
    this.fireEvent = function (name, args) {
        if (!this.events.hasOwnProperty(name)) {
            return;
        }

        if (!args || !args.length) args = [];
        var events = this.events[name], l = events.length;
        for (var i = 0; i < l; i++) {
            if (events[i].apply(null, args) == false) {
                break;
            }
        }
    }
}


//extend(AddressTools.prototype, 'withEvents');