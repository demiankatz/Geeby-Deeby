var SeriesEditor = function() {
    this.type = "Series";
    this.saveFields = {
        'name': { 'id': '#Series_Name', emptyError: 'Series name cannot be blank.' },
        'desc': { 'id': '#Series_Description' },
        'lang': { 'id': '#Language_ID' }
    };
    this.attributeSelector = '.series-attribute';
    this.links = {
        'Material': {
            'uriField': { 'id': '#Series_Material_Type_ID' }
        },
    };
};
BaseEditor.prototype.registerSubclass(SeriesEditor);
var Series = new SeriesEditor();

/**
 * Override the standard Item redraw list function:
 */
if (typeof Item === "object") {
    Item.redrawList = function() {
        var seriesID = $('#Series_ID').val();
        var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Item';
        $('#item_list').load(url);
    }
}