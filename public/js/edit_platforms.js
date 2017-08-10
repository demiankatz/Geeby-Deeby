var PlatformEditor = function() {
    this.type = "Platform";
    this.saveFields = {
        'platform': { 'id': '#Platform', emptyError: 'Platform cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(PlatformEditor);
var Platform = new PlatformEditor();
