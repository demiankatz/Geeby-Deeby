var SeriesAttributeEditor = function() {
    this.type = "Series Attribute";
    this.saveFields = {
        'attribute_name': { 'id': '#Series_Attribute_Name', emptyError: 'Name cannot be blank.' },
        'rdf_property': { 'id': '#Series_Attribute_RDF_Property' },
        'priority': { 'id': '#Display_Priority' },
        'allow_html': { 'id': '#Allow_HTML' }
    };
};
BaseEditor.prototype.registerSubclass(SeriesAttributeEditor);
var SeriesAttribute = new SeriesAttributeEditor();

var EditionsAttributeEditor = function() {
    this.type = "Editions Attribute";
    this.saveFields = {
        'attribute_name': { 'id': '#Editions_Attribute_Name', emptyError: 'Name cannot be blank.' },
        'rdf_property': { 'id': '#Editions_Attribute_RDF_Property' },
        'priority': { 'id': '#Display_Priority' },
        'copy_to_clone': { 'id': '#Copy_To_Clone' },
        'allow_html': { 'id': '#Allow_HTML' }
    };
};
BaseEditor.prototype.registerSubclass(EditionsAttributeEditor);
var EditionsAttribute = new EditionsAttributeEditor();

var TagsAttributeEditor = function() {
    this.type = "Tags Attribute";
    this.saveFields = {
        'attribute_name': { 'id': '#Tags_Attribute_Name', emptyError: 'Name cannot be blank.' },
        'rdf_property': { 'id': '#Tags_Attribute_RDF_Property' },
        'priority': { 'id': '#Display_Priority' },
        'allow_html': { 'id': '#Allow_HTML' }
    };
};
BaseEditor.prototype.registerSubclass(TagsAttributeEditor);
var TagsAttribute = new TagsAttributeEditor();
