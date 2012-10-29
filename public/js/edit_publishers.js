// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a publisher:
 */
function editPublisher(id)
{
    // Open the edit dialog box:
    var url = 'ajax.php?module=publisher&method=edit&id=' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === false ? "Add Publisher" : ("Edit Publisher " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the publishers on the screen:
 */
function redrawPublishers()
{
    var url = 'ajax.php?module=publisher&method=getList';
    $('#publisher_list').load(url);
}

/* Save the publisher inside the provided form element:
 */
function savePublisher()
{
    // Obtain values from form:
    var publisherID = $('#Publisher_ID').val();
    var publisher = $('#Publisher_Name').val();
    
    // Validate form:
    if (publisher.length == 0) {
        alert('Publisher cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_publisher').hide();
    $('#save_publisher_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = 'ajax.php?module=publisher&method=save';
    $.post(url, {id: publisherID, publisher: publisher}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the publisher list.
            redrawPublishers();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_publisher').show();
            $('#save_publisher_status').html('');
        }
    }, 'json');
}
