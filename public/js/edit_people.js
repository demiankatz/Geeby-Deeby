/**
 * Note -- this is the script for editing the people list; edit_person.js is for the
 *         single person edit page.
 */
var PersonEditor = function() {
    this.type = "Person";
    this.saveFields = {
        'last': { 'id': '#Last_Name', emptyError: 'Last name cannot be blank.' },
        'first': { 'id': '#First_Name' },
        'middle': { 'id': '#Middle_Name' },
        'extra': { 'id': '#Extra_Details' },
        'bio': { 'id': '#Biography' },
        'authority': { 'id': '#Authority_ID' }
    };
    this.links = {
        'Alias': {
            'subtypeSelector': { 'id': '#pseudo_type' },
            'uriField': { 'id': '#pseudo_name', 'nonNumericDefault': '', 'emptyError': 'Please specify a valid person.' }
        },
        'URI': {
            'uriField': { 'id': '#uri', 'emptyError': 'Please specify a valid URL.' },
            'saveFields': {
                'predicate_id': { 'id': '#Predicate_ID' }
            }
        }
    };
};
BaseEditor.prototype.registerSubclass(PersonEditor);
var Person = new PersonEditor();

var PersonAuthorityEditor = function() {
    this.type = "Person Authority";
    this.saveFields = {
        'authority': { 'id': '#Authority_Name', emptyError: 'Authority name cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(PersonAuthorityEditor);
var PersonAuthority = new PersonAuthorityEditor();

var PersonRoleEditor = function() {
    this.type = "Person Role";
    this.saveFields = {
        'role': { 'id': '#Role_Name', emptyError: 'Role cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(PersonRoleEditor);
var PersonRole = new PersonRoleEditor();

$(document).ready(function() {
    if (typeof registerAutocomplete === 'function') {
        registerAutocomplete('#pseudo_name', 'Person');
    }
});