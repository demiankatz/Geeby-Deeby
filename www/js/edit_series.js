// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a series:
 */
function editSeries(id)
{
    // Open the edit dialog box:
    var url = 'ajax.php?module=series&method=edit&id=' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === false ? "Add Series" : ("Edit Series " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the series on the screen:
 */
function redrawSeries()
{
    var url = 'ajax.php?module=series&method=getList';
    $('#series_list').load(url);
}

/* Save the series inside the provided form element:
 */
function saveSeries()
{
    // Obtain values from form:
    var seriesID = $('#Series_ID').val();
    var seriesName = $('#Series_Name').val();
    var desc = $('#Series_Description').val();
    var lang = $('#Language_ID').val();
    
    // Validate form:
    if (seriesName.length == 0) {
        alert('Series name cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_series').hide();
    $('#save_series_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = 'ajax.php?module=series&method=save';
    $.post(url, {id: seriesID, name: seriesName, desc: desc, lang: lang}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the series list.
            redrawSeries();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_series').show();
            $('#save_series_status').html('');
        }
    }, 'json');
}
