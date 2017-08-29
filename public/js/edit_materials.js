var MaterialTypeEditor = function() {
    this.type = "Material Type";
    this.saveFields = {
        'material': { 'id': '#Material_Type_Name', emptyError: 'Material type cannot be blank.' },
        'material_plural': { 'id': '#Material_Type_Plural_Name' },
        'material_rdf': { 'id': '#Material_Type_RDF_Class' },
        'default': { 'id': '#Default' }
    };
};
BaseEditor.prototype.registerSubclass(MaterialTypeEditor);
var MaterialType = new MaterialTypeEditor();
