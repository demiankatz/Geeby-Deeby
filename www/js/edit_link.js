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
    var params = {id: linkID, link_name: linkName, url: url, desc: desc, 
        date_checked: dateChecked, type_id: typeID};
    $.post('ajax.php?module=link&method=save', params, function(data) {
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
    var url = 'ajax.php?module=link&method=getItemList&id=' + 
        encodeURIComponent(linkID);
    $('#link_item_list').load(url);
}

/* Redraw the series list:
 */
function redrawSeries()
{
    var linkID = $('#Link_ID').val();
    var url = 'ajax.php?module=link&method=getSeriesList&id=' + 
        encodeURIComponent(linkID);
    $('#link_series_list').load(url);
}

/* Redraw the person list:
 */
function redrawPeople()
{
    var linkID = $('#Link_ID').val();
    var url = 'ajax.php?module=link&method=getPersonList&id=' + 
        encodeURIComponent(linkID);
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
    var url = 'ajax.php?module=link&method=linkItem';
    $.post(url, {link_id: linkID, item_id: relatedID}, function(data) {
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
    
    var linkID = $('#Link_ID').val();
    var url = 'ajax.php?module=link&method=unlinkItem';
    $.post(url, {link_id: linkID, item_id: relatedID}, function(data) {
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
    var linkID = $('#Link_ID').val();
    var relatedID = parseInt($('#link_series_id').val());
    
    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid series.");
        return;
    }
    
    // Save and update:
    var url = 'ajax.php?module=link&method=linkSeries';
    $.post(url, {link_id: linkID, series_id: relatedID}, function(data) {
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
    
    var linkID = $('#Link_ID').val();
    var url = 'ajax.php?module=link&method=unlinkSeries';
    $.post(url, {link_id: linkID, series_id: relatedID}, function(data) {
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
    var linkID = $('#Link_ID').val();
    var relatedID = parseInt($('#link_person_id').val());
    
    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid person.");
        return;
    }
    
    // Save and update:
    var url = 'ajax.php?module=link&method=linkPerson';
    $.post(url, {link_id: linkID, person_id: relatedID}, function(data) {
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
    
    var linkID = $('#Link_ID').val();
    var url = 'ajax.php?module=link&method=unlinkPerson';
    $.post(url, {link_id: linkID, person_id: relatedID}, function(data) {
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
        url: "ajax.php", 
        extraParams: {module: "item", method: "suggest" }, 
        highlight: false
    };
    $('.Item_ID').autocomplete(options);
    options = {
        url: "ajax.php", 
        extraParams: {module: "people", method: "suggest" }, 
        highlight: false
    };
    $('.Person_ID').autocomplete(options);
    options = {
        url: "ajax.php", 
        extraParams: {module: "series", method: "suggest" }, 
        highlight: false
    };
    $('.Series_ID').autocomplete(options);
});
