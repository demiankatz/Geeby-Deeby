var TagEditor = function() {
    this.type = "Tag";
    this.saveFields = {
        'tag': { 'id': '#Tag', emptyError: 'Subject/tag name cannot be blank.' },
        'type_id': { 'id': '#Tag_Type_ID' }
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
