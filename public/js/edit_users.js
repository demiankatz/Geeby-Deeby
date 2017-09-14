var UserEditor = function() {
    this.type = "User";
    this.saveFields = {
        'username': { 'id': '#Username', emptyError: 'Username cannot be blank.' },
    };
};
BaseEditor.prototype.registerSubclass(UserEditor);
var User = new UserEditor();
