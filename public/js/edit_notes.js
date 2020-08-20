var NoteEditor = function() {
    this.type = "Note";
    this.saveFields = {
        'note': { 'id': '#Note_Text', emptyError: 'Note cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(NoteEditor);
var Note = new NoteEditor();
