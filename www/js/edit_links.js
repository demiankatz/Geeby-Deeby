// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a person:
 */
function editLink(id)
{
    // Open the edit dialog box:
    var url = 'ajax.php?module=link&method=edit&id=' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === false ? "Add Link" : ("Edit Link " + id)),
        modal: true,
        autoOpen: true,
        width: 600,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the links on the screen:
 */
function redrawLinks()
{
    var url = 'ajax.php?module=link&method=getList';
    $('#link_list').load(url);
}

/* Save the link inside the provided form element:
 */
function saveLink()
{
    // Obtain values from form:
    var linkID = $('#Link_ID').val();
    var linkName = $('#Link_Name').val();
    var url = $('#URL').val();
    var desc = $('#Description').val();
    var dateChecked = $('#Date_Checked').val();
    var typeID = $('#Link_Type_ID').val();
    
    // Validate form:
    if (linkName.length == 0) {
        alert('Link name cannot be blank.');
        return;
    }
    if (url.length == 0) {
        alert('URL cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_link').hide();
    $('#save_link_status').html('Saving...');
    
    // Use AJAX to save the values:
    var params = {id: linkID, link_name: linkName, url: url, desc: desc, 
        date_checked: dateChecked, type_id: typeID};
    $.post('ajax.php?module=link&method=save', params, function(data) {
        // If save was successful...
        if (data.success) {
             // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the person list.
            redrawLinks();
       } else {
            // Save failed -- display error message.
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_link').show();
        $('#save_link_status').html('');
    }, 'json');
}

/* Pop up a dialog to edit a link type:
 */
function editLinkType(id)
{
    // Open the edit dialog box:
    var url = 'ajax.php?module=linktype&method=edit&id=' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === false ? "Add Link Type" : ("Edit Link Type " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the link types on the screen:
 */
function redrawLinkTypes()
{
    var url = 'ajax.php?module=linktype&method=getList';
    $('#link_type_list').load(url);
}

/* Save the link type inside the provided form element:
 */
function saveLinkType()
{
    // Obtain values from form:
    var linkTypeID = $('#Link_Type_ID').val();
    var linkType = $('#Link_Type').val();
    
    // Validate form:
    if (linkType.length == 0) {
        alert('Link type cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_link_type').hide();
    $('#save_link_type_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = 'ajax.php?module=linktype&method=save';
    $.post(url, {id: linkTypeID, linkType: linkType}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the role list.
            redrawLinkTypes();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_link_type').show();
            $('#save_link_type_status').html('');
        }
    }, 'json');
}
