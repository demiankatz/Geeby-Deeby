var TagEditor = function() {
    this.type = "Tag";
    this.attributeSelector = '.tag-attribute';
    this.saveFields = {
        'tag': { 'id': '#Tag', emptyError: 'Subject/tag name cannot be blank.' },
        'type_id': { 'id': '#Tag_Type_ID' }
    };
    this.links = {
        'Relationship': {
            'subtypeSelector': { 'id': '#relationship_type' },
            'targetSelector': '#relationship_list',
            'uriField': { 'id': '#target_tag', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid tag.' }
        },
        'URI': {
            'uriField': { 'id': '#uri', 'emptyError': 'Please specify a valid URL.' },
            'saveFields': {
                'predicate_id': { 'id': '#Predicate_ID' }
            }
        }
    };
};
BaseEditor.prototype.registerSubclass(TagEditor);
var Tag = new TagEditor();

var TagTypeEditor = function() {
    this.type = "Tag Type";
    this.saveFields = {
        'tagType': { 'id': '#Tag_Type', emptyError: 'Subject/tag type cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(TagTypeEditor);
var TagType = new TagTypeEditor();

$(document).ready(function() {
    if (typeof registerAutocomplete === 'function') {
        registerAutocomplete('#target_tag', 'Tag');
    }
});