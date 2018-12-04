// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a source:
 */
function editFullTextSource(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/FullTextSource/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add Full Text Source" : ("Edit Full Text Source " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the sources on the screen:
 */
function redrawFullTextSources()
{
    var url = basePath + '/edit/FullTextSourceList';
    $('#source_list').load(url);
}

/* Save the source inside the provided form element:
 */
function saveFullTextSource()
{
    // Obtain values from form:
    var sourceID = $('#Full_Text_Source_ID').val();
    var fulltextsource = $('#Full_Text_Source_Name').val();

    // Validate form:
    if (fulltextsource.length == 0) {
        alert('Full text source cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_source').hide();
    $('#save_source_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/FullTextSource/' + encodeURIComponent(sourceID);
    $.post(url, {fulltextsource: fulltextsource}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the source list.
            redrawFullTextSources();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_source').show();
            $('#save_source_status').html('');
        }
    }, 'json');
}
