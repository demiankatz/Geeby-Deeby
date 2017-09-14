var UserEditor = function() {
    this.type = "User";
    this.saveFields = {
        'username': { 'id': '#Username', emptyError: 'Username cannot be blank.' },
        'name': { 'id': '#Name', emptyError: 'Name cannot be blank.' },
        'address': { 'id': '#Address' },
        'person_id': { 'id': '#Person_ID' },
        'group_id': { 'id': '#User_Group_ID' },
        'password': { 'id': '#Password' },
        'password_confirm': { 'id': '#Password_Confirm' }
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

// Set up autocomplete.
$(document).ready(function() {
    if (typeof registerAutocomplete === 'function') {
        registerAutocomplete('#Person_ID', 'Person');
    }
});
