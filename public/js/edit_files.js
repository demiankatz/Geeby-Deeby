var FileEditor = function() {
    this.type = "File";
    this.saveFields = {
        'file_name': { 'id': '#File_Name', emptyError: 'File name cannot be blank.' },
        'path': { 'id': '#File_Path', emptyError: 'File path cannot be blank.' },
        'desc': { 'id': '#Description' },
        'type_id': { 'id': '#File_Type_ID' }
    };
    this.idSelector = '#File_ID';
};
BaseEditor.prototype.registerSubclass(FileEditor);
var File = new FileEditor();

var FileTypeEditor = function() {
    this.type = "File Type";
    this.saveFields = {
        'fileType': { 'id': '#File_Type', emptyError: 'File type cannot be blank.' }
    };
    this.idSelector = "#File_Type_ID";
}
BaseEditor.prototype.registerSubclass(FileTypeEditor);
var FileType = new FileTypeEditor();