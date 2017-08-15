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
    var listTarget = $(this.getListTarget());
    if (listTarget) {
        var url = this.getBaseUri() + 'List';
        listTarget.load(url);
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
 * Retrieve and validate data values to save.
 */
BaseEditor.prototype.getSaveData = function(saveFields, attributeSelector, attributeIdPrefix) {
    var values = {};
    for (var key in saveFields) {
        var rules = saveFields[key];
        var format = typeof rules.format === 'undefined' ? 'text' : rules.format;
        var current;
        if (format === 'checkbox') {
            current = $(rules.id).is(':checked') ? 1 : 0;
        } else {
            current = $(rules.id).val();
        }
        if (typeof rules.nonNumericDefault !== 'undefined') {
            current = parseInt(current);
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
 * Get the URI to interact with a particular type of link.
 */
BaseEditor.prototype.getLinkUri = function(type) {
    return this.getSaveUri() + "/" + type;
}

/**
 * Redraw a list of linked information.
 */
BaseEditor.prototype.redrawLinks = function(type) {
    var target = '#' + type.toLowerCase() + "_list";
    $(target).load(this.getLinkUri(type));
};

/**
 * Get the callback function for the AJAX link action.
 */
BaseEditor.prototype.getLinkCallback = function(type) {
    var editor = this;
    return function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            editor.redrawLinks(type);
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
    var values = this.getSaveData(this.links[type].saveFields, null, null);
    if (!values) {
        return;
    }
    var uri = this.getLinkUri(type);
    // Check if we need to add an extra value to the URI:
    if (typeof this.links[type].uriField !== 'undefined') {
        var extra = this.getSaveData({ 'extra': this.links[type].uriField }, null, null);
        uri += "/" + extra['extra'];
    }
    $.post(uri, values, this.getLinkCallback(type), 'json');
};

/**
 * Remove a piece of linked information.
 */
BaseEditor.prototype.unlink = function(type, which) {
    if (!confirm("Are you sure?")) {
        return;
    }
    var url = this.getLinkUri(type) + "/" + encodeURIComponent(which);
    $.ajax({url: url, type: "delete", dataType: "json", success: this.getLinkCallback(type)});
};