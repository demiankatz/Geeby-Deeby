var PublisherEditor = function() {
    this.type = "Publisher";
    this.saveFields = {
        'publisher': { 'id': '#Publisher_Name', emptyError: 'Publisher cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(PublisherEditor);
var Publisher = new PublisherEditor();
