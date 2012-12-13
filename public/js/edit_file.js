/* Note -- this is the script for editing a single file; edit_files.js is for the
 *         file list page.
 */

/* Save the file inside the provided form element:
 */
function saveFile()
{
    // Obtain values from form:
    var fileID = $('#File_ID').val();
    var fileName = $('#File_Name').val();
    var filePath = $('#File_Path').val();
    var desc = $('#Description').val();
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
    var params = {id: fileID, file_name: fileName, path: filePath, desc: desc, 
        type_id: typeID};
    $.post('ajax.php?module=file&method=save', params, function(data) {
        // If save was successful...
        if (!data.success) {
            // Save failed -- display error message.
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_file').show();
        $('#save_file_status').html('');
    }, 'json');
}

/* Redraw the item list:
 */
function redrawItems()
{
    var fileID = $('#File_ID').val();
    var url = 'ajax.php?module=file&method=getItemList&id=' + 
        encodeURIComponent(fileID);
    $('#file_item_list').load(url);
}

/* Redraw the series list:
 */
function redrawSeries()
{
    var fileID = $('#File_ID').val();
    var url = 'ajax.php?module=file&method=getSeriesList&id=' + 
        encodeURIComponent(fileID);
    $('#file_series_list').load(url);
}

/* Redraw the person list:
 */
function redrawPeople()
{
    var fileID = $('#File_ID').val();
    var url = 'ajax.php?module=file&method=getPersonList&id=' + 
        encodeURIComponent(fileID);
    $('#file_person_list').load(url);
}

/* Save a relationship to the current item:
 */
function linkItem()
{
    var fileID = $('#File_ID').val();
    var relatedID = parseInt($('#file_item_id').val());
    
    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid item.");
        return;
    }
    
    // Save and update:
    var url = 'ajax.php?module=file&method=linkItem';
    $.post(url, {file_id: fileID, item_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the person list.
            redrawItems();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Delete an item:
 */
function unlinkItem(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var fileID = $('#File_ID').val();
    var url = 'ajax.php?module=file&method=unlinkItem';
    $.post(url, {file_id: fileID, item_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the item list.
            redrawItems();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Save a relationship to the current series:
 */
function linkSeries()
{
    var fileID = $('#File_ID').val();
    var relatedID = parseInt($('#file_series_id').val());
    
    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid series.");
        return;
    }
    
    // Save and update:
    var url = 'ajax.php?module=file&method=linkSeries';
    $.post(url, {file_id: fileID, series_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the series list.
            redrawSeries();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Delete a series:
 */
function unlinkSeries(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var fileID = $('#File_ID').val();
    var url = 'ajax.php?module=file&method=unlinkSeries';
    $.post(url, {file_id: fileID, series_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the series list.
            redrawSeries();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Save a relationship to the current person:
 */
function linkPerson()
{
    var fileID = $('#File_ID').val();
    var relatedID = parseInt($('#file_person_id').val());
    
    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid person.");
        return;
    }
    
    // Save and update:
    var url = 'ajax.php?module=file&method=linkPerson';
    $.post(url, {file_id: fileID, person_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the person list.
            redrawPeople();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Delete a person:
 */
function unlinkPerson(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var fileID = $('#File_ID').val();
    var url = 'ajax.php?module=file&method=unlinkPerson';
    $.post(url, {file_id: fileID, person_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the person list.
            redrawPeople();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

// Activate autocomplete when DOM is ready:
$(document).ready(function(){
    // Turn on autocomplete
    var options = {
        url: basePath + "/Suggest/Item",
        highlight: false
    };
    $('.Item_ID').autocomplete(options);
    options = {
        url: basePath + "/Suggest/Person",
        highlight: false
    };
    $('.Person_ID').autocomplete(options);
    options = {
        url: basePath + "/Suggest/Series",
        highlight: false
    };
    $('.Series_ID').autocomplete(options);
});
