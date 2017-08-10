var NoteEditor = function() {
    this.type = "Note";
    this.saveFields = {
        'note': { 'id': '#Note', emptyError: 'Note cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(NoteEditor);
var Note = new NoteEditor();
