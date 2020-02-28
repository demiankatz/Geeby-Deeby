var CitationEditor = function() {
    this.type = "Citation";
    this.saveFields = {
        'citation': { 'id': '#Citation_Text', emptyError: 'Citation cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(CitationEditor);
var Citation = new CitationEditor();
