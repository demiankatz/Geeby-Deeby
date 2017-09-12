var SeriesEditor = function() {
    this.type = "Series";
    this.saveFields = {
        'name': { 'id': '#Series_Name', emptyError: 'Series name cannot be blank.' },
        'desc': { 'id': '#Series_Description' },
        'lang': { 'id': '#Language_ID' }
    };
    this.attributeSelector = '.series-attribute';
    this.links = {
        'AltTitle': {
            'saveFields': {
                'title': { 'id': '#Alt_Title', 'emptyError': 'Title must not be blank.' },
                'note_id': { 'id': '#Alt_Title_Note', 'nonNumericDefault': '' }
            }
        },
        'Item': {
            'uriField': { 'id': '#item_name', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid item' }
        },
        'Material': {
            'uriField': { 'id': '#Series_Material_Type_ID' }
        },
        'Translation': {
            'subtypeSelector': { 'id': '#trans_type' },
            'uriField': { 'id': '#trans_name', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid series.' }
        }
    };
};
BaseEditor.prototype.registerSubclass(SeriesEditor);

/**
 * Save selected categories:
 */
SeriesEditor.prototype.saveCategories = function()
{
    // Create an array of all checked categories:
    var values = [];
    $('.Category_ID').each(function(intIndex) {
        if ($(this).is(':checked')) {
            values[values.length] = $(this).val();
        }
    });

    // Hide save button and display status message to avoid duplicate submission:
    $('#save_categories').hide();
    $('#save_categories_status').html('Saving...');

    // Use AJAX to save the values:
    var url = this.getLinkUri('Categories');
    $.post(url, {"categories[]": values}, function(data) {
        // If save failed, display error message.
        if (!data.success) {
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_categories').show();
        $('#save_categories_status').html('');
    }, 'json');
};

var Series = new SeriesEditor();

/**
 * Override the standard Item redraw list function:
 */
if (typeof Item === "object") {
    Item.redrawList = function() {
        Series.redrawLinks('Item');
    }
}

// Load data and setup autocomplete.
$(document).ready(function() {
    if (typeof registerAutocomplete === 'function') {
        registerAutocomplete('#Publisher_ID', 'Publisher');
        registerAutocomplete('#trans_name', 'Series');
        registerAutocomplete('#item_name', 'Item');
        // .Note_ID autocomplete is already registered by edit_items.js
    }
});
