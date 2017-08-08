/**
 * Constructor
 */
var BaseEditor = function() {
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
BaseEditor.prototype.getSaveData = function() {
    var values = {};
    for (var key in this.saveFields) {
        var rules = this.saveFields[key];
        var format = typeof rules.format === 'undefined' ? 'text' : rules.format;
        var current;
        if (format === 'checkbox') {
            current = $(rules.id).is(':checked') ? 1 : 0;
        } else {
            current = $(rules.id).val();
        }
        if (typeof rules.emptyError !== 'undefined' && rules.emptyError && current.length == 0) {
            alert(rules.emptyError);
            return false;
        }
        values[key] = current;
    }
    if (typeof this.attributeSelector !== 'undefined' && this.attributeSelector) {
        var attribElements = $(this.attributeSelector);
        for (var i = 0; i < attribElements.length; i++) {
            var obj = $(attribElements[i]);
            var attrId = obj.attr('id').replace(this.getAttributeIdPrefix(), '');
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
    }
};

/**
 * Save an active instance of the type.
 */
BaseEditor.prototype.save = function() {
    var values = this.getSaveData();
    if (!values) {
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $(this.getSaveButton()).hide();
    $(this.getSaveStatusTarget()).html('Saving...');
    
    // Use AJAX to save the values:
    var url = this.getBaseUri() + '/' + encodeURIComponent($(this.getIdSelector()).val());
    $.post(url, values, this.getSaveCallback(), 'json');
};
