var PredicateEditor = function() {
    this.type = "Predicate";
    this.saveFields = {
        'predicate': { 'id': '#Predicate_URI', emptyError: 'Predicate cannot be blank.' },
        'abbrev': { 'id': '#Predicate_Abbrev', emptyError: 'Abbreviation cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(PredicateEditor);
var Predicate = new PredicateEditor();
