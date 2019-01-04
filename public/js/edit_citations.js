var CitationEditor = function() {
    this.type = "Citation";
    this.saveFields = {
        'citation': { 'id': '#Citation', emptyError: 'Citation cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(CitationEditor);
var Citation = new CitationEditor();
