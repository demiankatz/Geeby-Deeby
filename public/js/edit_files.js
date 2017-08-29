var FileEditor = function() {
    this.type = "File";
    this.saveFields = {
        'file_name': { 'id': '#File_Name', emptyError: 'File name cannot be blank.' },
        'path': { 'id': '#File_Path', emptyError: 'File path cannot be blank.' },
        'desc': { 'id': '#Description' },
        'type_id': { 'id': '#File_Type_ID' }
    };
    this.links = {
        'Item': {
            'uriField': { 'id': '#file_item_id', 'nonNumericDefault': '', 'emptyError': 'Please choose a valid item.' }
        },
        'Person': {
            'uriField': { 'id': '#file_person_id', 'nonNumericDefault': '', 'emptyError': 'Please choose a valid person.' }
        },
        'Series': {
            'uriField': { 'id': '#file_series_id', 'nonNumericDefault': '', 'emptyError': 'Please choose a valid series.' }
        }
    };
};
BaseEditor.prototype.registerSubclass(FileEditor);
var File = new FileEditor();

var FileTypeEditor = function() {
    this.type = "File Type";
    this.saveFields = {
        'fileType': { 'id': '#File_Type', emptyError: 'File type cannot be blank.' }
    };
}
BaseEditor.prototype.registerSubclass(FileTypeEditor);
var FileType = new FileTypeEditor();

// Load data and setup autocomplete.
$(document).ready(function() {
  registerAutocomplete('.Item_ID', 'Item');
  registerAutocomplete('.Person_ID', 'Person');
  registerAutocomplete('.Series_ID', 'Series');
});
