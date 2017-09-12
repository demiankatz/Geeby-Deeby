/**
 * Constructor
 */
var BaseEditor = function() {
    /*
    // Type of object being edited by this class
    this.type = "Example";

    // Rules for loading and validating form data; property names
    // equal AJAX POST keys, while values define rules for retrieving
    // and validating the values.
    this.saveFields = {
        'exampleName': { 'id': '#Example_Name', emptyError: 'Name cannot be blank.' }
    };

    // If this editor uses dynamic attributes, this is the selector to find them.
    this.attributeSelector = false;

    // Rules for linking to other types of data.
    this.links = {
        // You can do a simple link, where you are linking two IDs through the URI...
        'SimpleLink': {
            'uriField': { 'id': '#Simple_Link_ID' }
        },
        // Or you can do a complex link, where you are building a POST from a form:
        'ComplexLink': {
            'saveFields': {
                'fieldA': { 'id': '#FieldA' },
                'fieldB': { 'id': '#FieldB' }
            }
        }
    };
    */
};

/**
 * Set up the provided class as a subclass of this one.
 */
BaseEditor.prototype.registerSubclass = function(child) {
    child.prototype = Object.create(BaseEditor.prototype);
};

/**
 * Get base URL for editing...
 */
BaseEditor.prototype.getBaseUri = function() {
    // Convention: remove whitespace from type name to create URI:
    return basePath + '/edit/' + this.type.replace(/\s/g, '');
};

/**
 * Edit an instance of the type represented by this class.
 */
BaseEditor.prototype.edit = function(id) {
    // Open the edit dialog box:
    var url = this.getBaseUri() + '/' + encodeURIComponent(id);
    var editor = this;
    this.editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add " + this.type : ("Edit " + this.type + " " + id)),
        modal: true,
        autoOpen: true,
        width: 600,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { editor.editBox.empty(); }
    });
};

/**
 * Get the JQuery selector for the element containing the record ID.
 */
BaseEditor.prototype.getIdSelector = function() {
    // Default convention: underscored type with "_ID" suffix:
    return '#' + this.type.replace(/\s/g, '_') + '_ID';
};

/**
 * Get the JQuery selector for the list of items to edit.
 */
BaseEditor.prototype.getListTarget = function() {
    // Default convention: lowercased, underscored type with "_list" suffix:
    return '#' + this.type.toLowerCase().replace(/\s/g, '_') + '_list';
};

/**
 * Get the JQuery selector for the save button.
 */
BaseEditor.prototype.getSaveButton = function() {
    // Default convention: lowercased, underscored type with "save_" prefix:
    return '#save_' + this.type.toLowerCase().replace(/\s/g, '_');
};

/**
 * Get the JQuery selector for the save status button.
 */
BaseEditor.prototype.getSaveStatusTarget = function() {
    // Default convention: same as save button, with "_status" suffix:
    return this.getSaveButton() + '_status';
};

/**
 * Redraw the list of options on the screen.
 */
BaseEditor.prototype.redrawList = function() {
    // Only make the AJAX call if we have somewhere to send the results:
    var targetSelector = this.getListTarget();
    var listTarget = $(targetSelector);
    if (listTarget.length > 0) {
        var url = this.getBaseUri() + 'List';
        listTarget.load(url);
        if (typeof this.redrawFunction === 'function') {
            this.redrawFunction();
        }
    } else {
        console.log('Cannot find list element: ' + targetSelector);
    }
};

/**
 * Callback hook for redrawing controls after a save operation completes.
 */
BaseEditor.prototype.redrawAfterSave = function() {
    this.redrawList();
};

/**
 * Get the prefix used on attribute element IDs for this type of object.
 */
BaseEditor.prototype.getAttributeIdPrefix = function() {
    return this.type.replace(/\s/g, '_') + '_Attribute_';
};

/**
 * Clear out a save form.
 *
 * @param saveFields Form field configuration.
 */
BaseEditor.prototype.clearSaveData = function(saveFields) {
    for (var key in saveFields) {
        var rules = saveFields[key];
        var current = $(rules.id);
        if (current.prop('type') == 'text' || current.prop('tagName').toLowerCase() == 'textarea') {
            current.val('');
        }
    }
};

/**
 * Retrieve and validate data values to save.
 *
 * @param values            Initial set of values to add further values to
 * @param saveFields        Configuration for retrieving further values
 * @param attributeSelector Selector for pulling dynamic attribute values
 * @param attributeIdPrefix ID prefix for extracting dynamic attribute IDs
 */
BaseEditor.prototype.getSaveData = function(values, saveFields, attributeSelector, attributeIdPrefix) {
    for (var key in saveFields) {
        var rules = saveFields[key];
        var currentElement = $(rules.id);
        var inputType = currentElement.prop('type');
        var current = (inputType === 'checkbox')
            ? (currentElement.is(':checked') ? 1 : 0)
            : currentElement.val();
        if (typeof rules.nonNumericDefault !== 'undefined') {
            current = parseInt(current, 10);
            if (isNaN(current)) {
                current = rules.nonNumericDefault;
            }
        }
        if (typeof rules.emptyError !== 'undefined' && rules.emptyError && current.length == 0) {
            alert(rules.emptyError);
            return false;
        }
        if (typeof rules.customValidator === 'function' && !rules.customValidator(current)) {
            return false;
        }
        values[key] = current;
    }
    if (attributeSelector) {
        var attribElements = $(attributeSelector);
        for (var i = 0; i < attribElements.length; i++) {
            var obj = $(attribElements[i]);
            var attrId = obj.attr('id').replace(attributeIdPrefix, '');
            values['attribs[' + attrId + ']'] = obj.val();
        }
    }
    return values;
};

/**
 * Get the callback function for the AJAX save action.
 */
BaseEditor.prototype.getSaveCallback = function() {
    var editor = this;
    return function(data) {
        // If save was successful...
        if (data.success) {
             // Close the dialog box.
            if (editor.editBox) {
                editor.editBox.dialog('close');
                editor.editBox.dialog('destroy');
                editor.editBox = false;
            }

            // Update the list.
            editor.redrawAfterSave();
       } else {
            // Save failed -- display error message.
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $(editor.getSaveButton()).show();
        $(editor.getSaveStatusTarget()).html('');
    };
};

/**
 * Get the base URI for saving an object.
 */
BaseEditor.prototype.getSaveUri = function() {
    return this.getBaseUri() + '/' + encodeURIComponent($(this.getIdSelector()).val());
};

/**
 * Save an active instance of the type.
 */
BaseEditor.prototype.save = function() {
    var values = this.getSaveData(
        typeof this.saveFieldsFunction === 'function' ? this.saveFieldsFunction() : {},
        this.saveFields,
        typeof this.attributeSelector === 'undefined' ? null : this.attributeSelector,
        this.getAttributeIdPrefix()
    );
    if (!values) {
        return;
    }

    // Hide save button and display status message to avoid duplicate submission:
    $(this.getSaveButton()).hide();
    $(this.getSaveStatusTarget()).html('Saving...');

    // Use AJAX to save the values:
    $.post(this.getSaveUri(), values, this.getSaveCallback(), 'json');
};

/**
 * If a subtype selector is active, get the selected subtype.
 */
BaseEditor.prototype.getSelectedSubtype = function(type) {
    if (typeof this.links[type].subtypeSelector !== 'undefined') {
        return $(this.links[type].subtypeSelector.id).val();
    }
    return '';
};

/**
 * Get the URI to interact with a particular type of link.
 */
BaseEditor.prototype.getLinkUri = function(type, subtype, extra) {
    if (typeof subtype === 'undefined') {
        subtype = this.getSelectedSubtype(type);
    }
    return this.getSaveUri() + "/" + type + subtype + (typeof extra === 'undefined' ? '' : extra);
}

/**
 * Redraw a list of linked information.
 */
BaseEditor.prototype.redrawLinks = function(type, subtype) {
    if (typeof subtype === 'undefined') {
        subtype = this.getSelectedSubtype(type);
    }
    var targetSelector = '#' + type.toLowerCase() + subtype.toLowerCase() + "_list";
    var target = $(targetSelector);
    if (target.length > 0) {
        target.load(this.getLinkUri(type, subtype));
    } else {
        console.log('Cannot find list element: ' + targetSelector);
    }
    // Reset the form inputs since we are redrawing...
    if (typeof this.links[type].saveFields !== 'undefined') {
        this.clearSaveData(this.links[type].saveFields);
    }
    if (typeof this.links[type].uriField !== 'undefined') {
        this.clearSaveData({ 'extra': this.links[type].uriField });
    }
    // If we have a custom redraw function, execute that too...
    if (typeof this.links[type].redrawFunction === 'function') {
        this.links[type].redrawFunction();
    }
};

/**
 * Get the callback function for the AJAX link action.
 */
BaseEditor.prototype.getLinkCallback = function(type, subtype) {
    var editor = this;
    return function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            editor.redrawLinks(type, subtype);
       } else {
            // Save failed -- display error message.
            alert('Error: ' + data.msg);
        }
    };
};

/**
 * Add a piece of linked information.
 */
BaseEditor.prototype.link = function(type) {
    var values = this.getSaveData(
        typeof this.links[type].saveFieldsFunction === 'function' ? this.links[type].saveFieldsFunction() : {},
        this.links[type].saveFields,
        null,
        null
    );
    if (!values) {
        return;
    }
    var uri = this.getLinkUri(type);
    // Check if we need to add an extra value to the URI:
    if (typeof this.links[type].uriField !== 'undefined') {
        var extra = this.getSaveData({}, { 'extra': this.links[type].uriField }, null, null);
        if (!extra) {
            return;
        }
        uri += "/" + encodeURIComponent(extra['extra']);
    }
    $.post(uri, values, this.getLinkCallback(type), 'json');
};

/**
 * Get the selector for the reorder input for a specific type of link matching
 * specific details....
 */
BaseEditor.prototype.getLinkOrderInputSelector = function(type, details) {
    var values = [];
    for (var key in details) {
        values[values.length] = details[key];
    }
    return '#' + type.toLowerCase() + '_order_' + values.join('_');
};

/**
 * Reorder a piece of linked information.
 */
BaseEditor.prototype.reorderLink = function(type, details, subtype) {
    details['pos'] = parseInt($(this.getLinkOrderInputSelector(type, details)).val(), 10);
    $.post(this.getLinkUri(type, subtype, 'Order'), details, this.getLinkCallback(type), 'json');
};

/**
 * Remove a piece of linked information.
 */
BaseEditor.prototype.unlink = function(type, which, subtype) {
    if (!confirm("Are you sure?")) {
        return;
    }
    if (typeof subtype === 'undefined') {
        subtype = '';
    }
    var url = this.getLinkUri(type, subtype) + "/" + encodeURIComponent(which);
    $.ajax({url: url, type: "delete", dataType: "json", success: this.getLinkCallback(type, subtype)});
};