var FullTextSourceEditor = function() {
    this.type = "Full Text Source";
    this.saveFields = {
        'fulltextsource': { 'id': '#Full_Text_Source_Name', emptyError: 'Full text source cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(FullTextSourceEditor);
var FullTextSource = new FullTextSourceEditor();