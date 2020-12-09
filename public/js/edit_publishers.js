var PublisherEditor = function() {
    this.type = "Publisher";
    this.saveFields = {
        'publisher': { 'id': '#Publisher_Name', emptyError: 'Publisher cannot be blank.' }
    };
    this.links = {
        'Address': {
            'saveFields': {
                'country': { 'id': '#Country_ID' },
                'city': { 'id': '#City_ID' },
                'street': { 'id': '#Street' }
            }
        },
        'Imprint': {
            'saveFields': {
                'imprint': { 'id': '#Imprint', 'emptyError': 'Imprint must not be blank.' }
            }
        },
        'URI': {
            'uriField': { 'id': '#uri', 'emptyError': 'Please specify a valid URL.' },
            'saveFields': {
                'predicate_id': { 'id': '#Predicate_ID' }
            }
        }
    };
};
BaseEditor.prototype.registerSubclass(PublisherEditor);
var Publisher = new PublisherEditor();
