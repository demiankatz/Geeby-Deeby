var SeriesEditor = function() {
    this.type = "Series";
    this.saveFields = {
        'name': { 'id': '#Series_Name', emptyError: 'Series name cannot be blank.' },
        'desc': { 'id': '#Series_Description' },
        'lang': { 'id': '#Language_ID' }
    };
    this.attributeSelector = '.series-attribute';
};
BaseEditor.prototype.registerSubclass(SeriesEditor);
var Series = new SeriesEditor();
