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
var Item = new ItemEditor();
