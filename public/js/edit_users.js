var UserEditor = function() {
    this.type = "User";
    this.saveFields = {
        'username': { 'id': '#Username', emptyError: 'Username cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(UserEditor);
var User = new UserEditor();

var UserGroupEditor = function() {
    this.type = "User Group";
    this.saveFields = {
        'name': { 'id': '#Group_Name', emptyError: 'Group name cannot be blank.' },
        'content_editor': { 'id': '#Content_Editor' },
        'user_editor': { 'id': '#User_Editor' },
        'approver': { 'id': '#Approver' },
        'data_manager': { 'id': '#Data_Manager' }
    };
};
BaseEditor.prototype.registerSubclass(UserGroupEditor);
var UserGroup = new UserGroupEditor();
