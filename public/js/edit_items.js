var ItemEditor = function() {
    this.type = "Item";
    this.saveFields = {
        'series_id': { 'id': '#Series_ID' },
        'name': { 'id': '#Item_Name', emptyError: 'Item name cannot be blank.' },
        'errata': { 'id': '#Item_Errata' },
        'thanks': { 'id': '#Item_Thanks' },
        'material': { 'id': '#Material_Type_ID' },
        // Length and endings are actually Edition fields, but we load the data here
        // for convenience when creating items in some contexts.
        'len': { 'id': '#Item_Length' },
        'endings': { 'id': '#Item_Endings' },
        // Edition_ID is only set when we are creating a child edition....
        'edition_id': { 'id': '#Edition_ID' }
    };
    this.links = {
        'AboutItem': {
            'uriField': { 'id': '#item_bib_id', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid item.' }
        },
        'AboutPerson': {
            'uriField': { 'id': '#person_bib_id', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid person.' }
        },
        'AboutSeries': {
            'uriField': { 'id': '#series_bib_id', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid series.' }
        },
        'Adaptation': {
            'subtypeSelector': { 'id': '#adapt_type' },
            'uriField': { 'id': '#adapt_name', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid item.' }
        },
        'AltTitle': {
            'saveFields': {
                'title': { 'id': '#Alt_Title', 'emptyError': 'Title must not be blank.' },
                'note_id': { 'id': '#Alt_Title_Note', 'nonNumericDefault': '' }
            }
        },
        'Attachment': {
            'uriField': { 'id': '#attachment_name', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid item.' },
            'saveFields': {
                'note_id': { 'id': '#Attachment_Note', 'nonNumericDefault': '' },
            }
        },
        'Creator': {
            'saveFields': {
                'person_id': { 'id': '#creator_person', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid person' },
                'role_id': { 'id': '#Creator_Role_ID' }
            }
        },
        'Credit': {
            'saveFields': {
                'note_id': { 'id': '#credit_note', 'nonNumericDefault': '' },
                'person_id': { 'id': '#credit_person', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid person' },
                'pos': { 'id': '#credit_position', 'nonNumericDefault': 0 },
                'role_id': { 'id': '#Role_ID' }
            }
        },
        'Description': {
            'saveFields': {
                'type': { 'id': '#DescriptionType' },
                'desc': { 'id': '#Description', 'emptyError': 'Description must not be blank.' }
            }
        },
        'Editions': { /* dummy placeholder to make copyEdition function work correctly */ },
        'Relationship': {
            'subtypeSelector': { 'id': '#relationship_type' },
            'targetSelector': '#relationship_list',
            'uriField': { 'id': '#target_item', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid item.' }
        },
        'Tag': {
            'uriField': { 'id': '#Tag_ID', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid tag.' }
        },
        'Translation': {
            'subtypeSelector': { 'id': '#trans_type' },
            'uriField': { 'id': '#trans_name', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid item.' }
        }
    };
};
BaseEditor.prototype.registerSubclass(ItemEditor);

/**
 * Copy an edition (special Item-specific action)
 */
ItemEditor.prototype.copyEdition = function() {
    var edition = $('.selectedEdition:checked').val();
    if (!edition) {
        alert("Please select an edition.");
        return;
    }
    // Save and update based on selected relationship:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edition) + '/Copy';
    $.post(url, {}, this.getLinkCallback('Editions'), 'json');
}

var Item = new ItemEditor();

var ItemCreatorEditor = function() {
    this.type = "Item_Creator";
    this.saveFields = [];
    this.links = {
        'Citation': {
            'uriField': { 'id' : '#Citation_ID', 'nonNumericDefault': '', 'emptyError': 'Please select a citation.' }
        }
    };
};
BaseEditor.prototype.registerSubclass(ItemCreatorEditor);

var ItemCreator = new ItemCreatorEditor();

// Load data and setup autocomplete.
$(document).ready(function() {
    if (typeof registerAutocomplete === 'function') {
        registerAutocomplete('.Item_ID,#target_item', 'Item');
        registerAutocomplete('.Note_ID', 'Note');
        registerAutocomplete('.Person_ID', 'Person');
        registerAutocomplete('.Series_ID', 'Series');
        registerAutocomplete('.Tag_ID', 'Tag');
    }
});
