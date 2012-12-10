// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a person:
 */
function editFile(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/File/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add File" : ("Edit File " + id)),
        modal: true,
        autoOpen: true,
        width: 600,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the files on the screen:
 */
function redrawFiles()
{
    var url = basePath + '/edit/FileList';
    $('#file_list').load(url);
}

/* Save the file inside the provided form element:
 */
function saveFile()
{
    // Obtain values from form:
    var fileID = $('#File_ID').val();
    var fileName = $('#File_Name').val();
    var filePath = $('#File_Path').val();
    var desc = $('#Description').val();
    var dateChecked = $('#Date_Checked').val();
    var typeID = $('#File_Type_ID').val();
    
    // Validate form:
    if (fileName.length == 0) {
        alert('File name cannot be blank.');
        return;
    }
    if (filePath.length == 0) {
        alert('File path cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_file').hide();
    $('#save_file_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/File/' + encodeURIComponent(fileID);
    var params = {file_name: fileName, path: filePath, desc: desc, 
        date_checked: dateChecked, type_id: typeID};
    $.post(url, params, function(data) {
        // If save was successful...
        if (data.success) {
             // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the person list.
            redrawFiles();
       } else {
            // Save failed -- display error message.
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_file').show();
        $('#save_file_status').html('');
    }, 'json');
}

/* Pop up a dialog to edit a file type:
 */
function editFileType(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/FileType/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add File Type" : ("Edit File Type " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the file types on the screen:
 */
function redrawFileTypes()
{
    var url = basePath + '/edit/FileTypeList';
    $('#file_type_list').load(url);
}

/* Save the file type inside the provided form element:
 */
function saveFileType()
{
    // Obtain values from form:
    var fileTypeID = $('#File_Type_ID').val();
    var fileType = $('#File_Type').val();
    
    // Validate form:
    if (fileType.length == 0) {
        alert('File type cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_file_type').hide();
    $('#save_file_type_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/FileType/' + encodeURIComponent(fileTypeID);
    $.post(url, {fileType: fileType}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the role list.
            redrawFileTypes();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_file_type').show();
            $('#save_file_type_status').html('');
        }
    }, 'json');
}
