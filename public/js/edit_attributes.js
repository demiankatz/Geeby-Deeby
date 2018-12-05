// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a series attribute:
 */
function editSeriesAttribute(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/SeriesAttribute/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add Series Attribute" : ("Edit Series Attribute " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the series attributes on the screen:
 */
function redrawSeriesAttributes()
{
    var url = basePath + '/edit/SeriesAttributeList';
    $('#series_attribute_list').load(url);
}

/* Save the series attribute inside the provided form element:
 */
function saveSeriesAttribute()
{
    // Obtain values from form:
    var attributeID = $('#Series_Attribute_ID').val();
    var attribute_name = $('#Series_Attribute_Name').val();
    var rdf_property = $('#Series_Attribute_RDF_Property').val();
    var priority = $('#Display_Priority').val();
    var allow_html = $('#Allow_HTML').is(':checked');

    // Validate form:
    if (attribute_name.length == 0) {
        alert('Name cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_series_attribute').hide();
    $('#save_series_attribute_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/SeriesAttribute/' + encodeURIComponent(attributeID);
    $.post(url, {attribute_name: attribute_name, rdf_property: rdf_property, priority: priority, allow_html: allow_html ? 1 : 0}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the on-screen list.
            redrawSeriesAttributes();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_series_attribute').show();
            $('#save_series_attribute_status').html('');
        }
    }, 'json');
}
