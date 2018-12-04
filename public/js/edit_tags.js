// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a person:
 */
function editTag(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/Tag/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add Subject/Tag" : ("Edit Subject/Tag " + id)),
        modal: true,
        autoOpen: true,
        width: 600,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the tags on the screen:
 */
function redrawTags()
{
    var url = basePath + '/edit/TagList';
    $('#tag_list').load(url);
}

/* Save the tag inside the provided form element:
 */
function saveTag()
{
    // Obtain values from form:
    var tagID = $('#Tag_ID').val();
    var tag = $('#Tag').val();
    var typeID = $('#Tag_Type_ID').val();
    
    // Validate form:
    if (tag.length == 0) {
        alert('Subject/tag name cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_tag').hide();
    $('#save_tag_status').html('Saving...');
    
    // Use AJAX to save the values:
    var targetUrl = basePath + '/edit/Tag/' + encodeURIComponent(tagID);
    var params = {tag: tag, type_id: typeID};
    $.post(targetUrl, params, function(data) {
        // If save was successful...
        if (data.success) {
             // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the person list.
            redrawTags();
       } else {
            // Save failed -- display error message.
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_tag').show();
        $('#save_tag_status').html('');
    }, 'json');
}

/* Pop up a dialog to edit a tag type:
 */
function editTagType(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/TagType/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === false ? "Add Subject/Tag Type" : ("Edit Subject/Tag Type " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the tag types on the screen:
 */
function redrawTagTypes()
{
    var url = basePath + '/edit/TagTypeList';
    $('#tag_type_list').load(url);
}

/* Save the tag type inside the provided form element:
 */
function saveTagType()
{
    // Obtain values from form:
    var tagTypeID = $('#Tag_Type_ID').val();
    var tagType = $('#Tag_Type').val();
    
    // Validate form:
    if (tagType.length == 0) {
        alert('Subject/tag type cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_tag_type').hide();
    $('#save_tag_type_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/TagType/' + encodeURIComponent(tagTypeID);
    $.post(url, {tagType: tagType}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the role list.
            redrawTagTypes();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_tag_type').show();
            $('#save_tag_type_status').html('');
        }
    }, 'json');
}
