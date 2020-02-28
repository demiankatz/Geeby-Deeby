var PlatformEditor = function() {
    this.type = "Platform";
    this.saveFields = {
        'platform': { 'id': '#Platform_Name', emptyError: 'Platform cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(PlatformEditor);
var Platform = new PlatformEditor();
