var LanguageEditor = function() {
    this.type = "Language";
    this.saveFields = {
        'language': { 'id': '#Language_Name', emptyError: 'Language cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(LanguageEditor);
var Language = new LanguageEditor();
