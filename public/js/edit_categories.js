var CategoryEditor = function() {
    this.type = "Category";
    this.saveFields = {
        'name': { 'id': '#Category', emptyError: 'Category name cannot be blank.' },
        'desc': { 'id': '#Description' }
    };
};
BaseEditor.prototype.registerSubclass(CategoryEditor);
var Category = new CategoryEditor();
