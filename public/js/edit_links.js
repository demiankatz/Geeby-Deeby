var LinkEditor = function() {
    this.type = "Link";
    this.saveFields = {
        'link_name': { 'id': '#Link_Name', emptyError: 'Link name cannot be blank.' },
        'url': { 'id': '#URL', emptyError: 'URL cannot be blank.' },
        'desc': { 'id': '#Description' },
        'date_checked': { 'id': '#Date_Checked' },
        'type_id': { 'id': '#Link_Type_ID' }
    };
    this.links = {
        'Item': {
            'uriField': { 'id': '#link_item_id', 'nonNumericDefault': '', 'emptyError': 'Please choose a valid item.' }
        },
        'Person': {
            'uriField': { 'id': '#link_person_id', 'nonNumericDefault': '', 'emptyError': 'Please choose a valid person.' }
        },
        'Series': {
            'uriField': { 'id': '#link_series_id', 'nonNumericDefault': '', 'emptyError': 'Please choose a valid series.' }
        }
    };
};
BaseEditor.prototype.registerSubclass(LinkEditor);
var Link = new LinkEditor();

var LinkTypeEditor = function() {
    this.type = "Link Type";
    this.saveFields = {
        'linkType': { 'id': '#Link_Type', emptyError: 'Link type cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(LinkTypeEditor);
var LinkType = new LinkTypeEditor();

// Load data and setup autocomplete.
$(document).ready(function() {
    if (typeof registerAutocomplete === 'function') {
        registerAutocomplete('.Item_ID', 'Item');
        registerAutocomplete('.Person_ID', 'Person');
        registerAutocomplete('.Series_ID', 'Series');
    }
});
