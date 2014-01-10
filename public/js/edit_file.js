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
    var params = {file_name: fileName, path: filePath, desc: desc, 
        type_id: typeID};
    var url = basePath + '/edit/File/' + encodeURIComponent(fileID);
    $.post(url, params, function(data) {
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
    var url = basePath + '/edit/File/' + encodeURIComponent(fileID) + '/Item';
    $('#file_item_list').load(url);
}

/* Redraw the series list:
 */
function redrawSeries()
{
    var fileID = $('#File_ID').val();
    var url = basePath + '/edit/File/' + encodeURIComponent(fileID) + '/Series';
    $('#file_series_list').load(url);
}

/* Redraw the person list:
 */
function redrawPeople()
{
    var fileID = $('#File_ID').val();
    var url = basePath + '/edit/File/' + encodeURIComponent(fileID) + '/Person';
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
    var url = basePath + '/edit/File/' + encodeURIComponent(fileID) + '/Item/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the person list.
            redrawItems();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Delete an item:
 */
function unlinkItem(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var fileID = $('#File_ID').val();
    var url = basePath + '/edit/File/' + encodeURIComponent(fileID) + '/Item/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the item list.
            redrawItems();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
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
    var url = basePath + '/edit/File/' + encodeURIComponent(fileID) + '/Series/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the series list.
            redrawSeries();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Delete a series:
 */
function unlinkSeries(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var fileID = $('#File_ID').val();
    var url = basePath + '/edit/File/' + encodeURIComponent(fileID) + '/Series/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the series list.
            redrawSeries();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
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
    var url = basePath + '/edit/File/' + encodeURIComponent(fileID) + '/Person/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the person list.
            redrawPeople();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Delete a person:
 */
function unlinkPerson(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var fileID = $('#File_ID').val();
    var url = basePath + '/edit/File/' + encodeURIComponent(fileID) + '/Person/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the person list.
            redrawPeople();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

// Load data and setup autocomplete.
$.ajax({
  url: basePath + "/Suggest/Item", 
  success: function(data) {
    $('.Item_ID').autocomplete({
      source: data.split('\n'),
      highlight: false
    });
  }
});
$.ajax({
  url: basePath + "/Suggest/Person", 
  success: function(data) {
    $('.Person_ID').autocomplete({
      source: data.split('\n'),
      highlight: false
    });
  }
});
$.ajax({
  url: basePath + "/Suggest/Series", 
  success: function(data) {
    $('.Series_ID').autocomplete({
      source: data.split('\n'),
      highlight: false
    });
  }
});
