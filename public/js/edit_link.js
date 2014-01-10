/* Note -- this is the script for editing a single link; edit_links.js is for the
 *         link list page.
 */

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
    var url = basePath + '/edit/Link/' + encodeURIComponent(linkID);
    var params = {link_name: linkName, url: url, desc: desc, 
        date_checked: dateChecked, type_id: typeID};
    $.post(url, params, function(data) {
        // If save was successful...
        if (!data.success) {
            // Save failed -- display error message.
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_link').show();
        $('#save_link_status').html('');
    }, 'json');
}

/* Redraw the item list:
 */
function redrawItems()
{
    var linkID = $('#Link_ID').val();
    var url = basePath + '/edit/Link/' + encodeURIComponent(linkID) + '/Item';
    $('#link_item_list').load(url);
}

/* Redraw the series list:
 */
function redrawSeries()
{
    var linkID = $('#Link_ID').val();
    var url = basePath + '/edit/Link/' + encodeURIComponent(linkID) + '/Series';
    $('#link_series_list').load(url);
}

/* Redraw the person list:
 */
function redrawPeople()
{
    var linkID = $('#Link_ID').val();
    var url = basePath + '/edit/Link/' + encodeURIComponent(linkID) + '/Person';
    $('#link_person_list').load(url);
}

/* Save a relationship to the current item:
 */
function linkItem()
{
    var linkID = $('#Link_ID').val();
    var relatedID = parseInt($('#link_item_id').val());
    
    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid item.");
        return;
    }
    
    // Save and update:
    var url = basePath + '/edit/Link/' + encodeURIComponent(linkID) + '/Item/' + encodeURIComponent(relatedID);
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
    
    var linkID = $('#Link_ID').val();
    var url = basePath + '/edit/Link/' + encodeURIComponent(linkID) + '/Item/' + encodeURIComponent(relatedID);
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
    var linkID = $('#Link_ID').val();
    var relatedID = parseInt($('#link_series_id').val());
    
    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid series.");
        return;
    }
    
    // Save and update:
    var url = basePath + '/edit/Link/' + encodeURIComponent(linkID) + '/Series/' + encodeURIComponent(relatedID);
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
    
    var linkID = $('#Link_ID').val();
    var url = basePath + '/edit/Link/' + encodeURIComponent(linkID) + '/Series/' + encodeURIComponent(relatedID);
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
    var linkID = $('#Link_ID').val();
    var relatedID = parseInt($('#link_person_id').val());
    
    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid person.");
        return;
    }
    
    // Save and update:
    var url = basePath + '/edit/Link/' + encodeURIComponent(linkID) + '/Person/' + encodeURIComponent(relatedID);
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
    
    var linkID = $('#Link_ID').val();
    var url = basePath + '/edit/Link/' + encodeURIComponent(linkID) + '/Person/' + encodeURIComponent(relatedID);
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
$(document).ready(function() {
  $('.Item_ID').autocomplete({
    source: function(request, response) {
      $.ajax({
        url: basePath + "/Suggest/Item?q=" + request.term, 
        success: function(data) {
          response(data.split('\n').slice(0, -1));
        }
      });
    }
  });
  $('.Person_ID').autocomplete({
    source: function(request, response) {
      $.ajax({
        url: basePath + "/Suggest/Person?q=" + request.term, 
        success: function(data) {
          response(data.split('\n').slice(0, -1));
        }
      });
    }
  });
  $('.Series_ID').autocomplete({
    source: function(request, response) {
      $.ajax({
        url: basePath + "/Suggest/Series?q=" + request.term, 
        success: function(data) {
          response(data.split('\n').slice(0, -1));
        }
      });
    }
  });
});
