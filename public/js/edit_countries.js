var CountryEditor = function() {
    this.type = "Country";
    this.saveFields = {
        'country': { 'id': '#Country_Name', emptyError: 'Country cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(CountryEditor);
var Country = new CountryEditor();

var CityEditor = function() {
    this.type = "City";
    this.saveFields = {
        'city': { 'id': '#City_Name', emptyError: 'City cannot be blank.' }
    };
};
BaseEditor.prototype.registerSubclass(CityEditor);
var City = new CityEditor();
