// Global reference to current open edit box.
var editBox = false;

/* Modify a publisher attached to the series:
 */
function modifyPublisher(rowID)
{
    // Open the edit dialog box:
    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Publisher/' + encodeURIComponent(rowID);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: "Modify Publisher",
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#modifyPublisherForm').remove(); }
    });
}

/* Save the modified publisher information:
 */
function saveModifiedPublisher()
{
    // Obtain values from form:
    var seriesID = $('#Series_ID').val();
    var rowID = $('#Series_Publisher_ID').val();
    var addressID = $('#Address_ID').val();
    var imprintID = $('#Imprint_ID').val();

    // Hide save button and display status message to avoid duplicate submission:
    $('#save_modified_publisher').hide();
    $('#save_modified_publisher_status').html('Saving...');

    // Use AJAX to save the values:
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Publisher/' + encodeURIComponent(rowID);
    var details = {
        address: addressID, imprint: imprintID
    };
    $.post(url, details, function(data) {
        // If save failed, display error message.
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            Series.redrawLinks('Publisher');
        } else {
            alert('Error: ' + data.msg);

            // Restore save button:
            $('#save_modified_publisher').show();
            $('#save_modified_publisher_status').html('');
        }
    }, 'json');
}
