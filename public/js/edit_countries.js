var CountryEditor = function() {
    this.type = "Country";
    this.saveFields = {
        'country': { 'id': '#Country_Name', emptyError: 'Country cannot be blank.' }
    };
    this.links = {
        'URI': {
            'uriField': { 'id': '#uri', 'emptyError': 'Please specify a valid URL.' },
            'saveFields': {
                'predicate_id': { 'id': '#Predicate_ID' }
            }
        }
    };
};
BaseEditor.prototype.registerSubclass(CountryEditor);
var Country = new CountryEditor();

var CityEditor = function() {
    this.type = "City";
    this.saveFields = {
        'city': { 'id': '#City_Name', emptyError: 'City cannot be blank.' }
    };
    this.links = {
        'URI': {
            'uriField': { 'id': '#uri', 'emptyError': 'Please specify a valid URL.' },
            'saveFields': {
                'predicate_id': { 'id': '#Predicate_ID' }
            }
        }
    };
};
BaseEditor.prototype.registerSubclass(CityEditor);
var City = new CityEditor();
