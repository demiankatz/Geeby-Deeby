// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a platform:
 */
function editPlatform(id)
{
    // Open the edit dialog box:
    var url = 'ajax.php?module=platform&method=edit&id=' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === false ? "Add Platform" : ("Edit Platform " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the platforms on the screen:
 */
function redrawPlatforms()
{
    var url = 'ajax.php?module=platform&method=getList';
    $('#platform_list').load(url);
}

/* Save the platform inside the provided form element:
 */
function savePlatform()
{
    // Obtain values from form:
    var platformID = $('#Platform_ID').val();
    var platform = $('#Platform').val();
    
    // Validate form:
    if (platform.length == 0) {
        alert('Platform cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_platform').hide();
    $('#save_platform_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = 'ajax.php?module=platform&method=save';
    $.post(url, {id: platformID, platform: platform}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the platform list.
            redrawPlatforms();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_platform').show();
            $('#save_platform_status').html('');
        }
    }, 'json');
}
