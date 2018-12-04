/* Save the edition inside the provided form element:
 */
function saveEdition()
{
    // Obtain values from form:
    var editionID = $('#Edition_ID').val();
    var editionName = $('#Edition_Name').val();
    var pos = $('#Position').val();
    var itemID = $('#Item_ID').val();
    var seriesID = $('#Series_ID').val();

    // Validate form:
    if (editionName.length == 0) {
        alert('Edition name cannot be blank.');
        return;
    }

    // Hide save button and display status message to avoid duplicate submission:
    $('#save_edition').hide();
    $('#save_edition_status').html('Saving...');

    // Use AJAX to save the values:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID);
    var details = {
        name: editionName,
        item_id: itemID,
        series_id: seriesID,
        position: pos
    };
    $.post(url, details, function(data) {
        // If save failed, display error message.
        if (!data.success) {
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_edition').show();
        $('#save_edition_status').html('');
    }, 'json');
}

/* Redraw date list:
 */
function redrawReleaseDates()
{
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/Dates';
    $('#date_list').load(url);
}

/* Add a release date:
 */
function saveReleaseDate()
{
    // Extract the basic values:
    var edID = $('#Edition_ID').val();
    var noteID = parseInt($('#releaseNote').val());
    if (isNaN(noteID)) {
        noteID = '';
    }
    var year = parseInt($('#releaseYear').val());
    if (isNaN(year)) {
        year = 0;
    }
    var month = parseInt($('#releaseMonth').val());
    if (isNaN(month)) {
        month = 0;
    }
    var day = parseInt($('#releaseDay').val());
    if (isNaN(day)) {
        day = 0;
    }

    // Validate month and day:
    if (month > 12) {
        alert('Please enter a valid month.');
        return;
    }
    if (day > 31) {
        alert('Please enter a valid day.');
        return;
    }

    // Save the date:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/AddDate';
    var params =
        {year: year, month: month, day: day, note_id: noteID};
    $.post(url, params, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawReleaseDates();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove a release date:
 */
function deleteReleaseDate(year, month, day)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/DeleteDate';
    var params = {year: year, month: month, day: day};
    $.post(url, params, function(data) {
        // If delete was successful...
        if (data.success) {
            // Update the list.
            redrawReleaseDates();
        } else {
            // Delete failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

// Activate page controls on domready:
$(document).ready(function(){
    // Turn on tabs
    $("#tabs").tabs();
    $("#tabs").tabs('paging', {cycle: true});

    // Turn on autocomplete
    var options = {
        url: basePath + "/Suggest/Item",
        highlight: false
    };
    $('.Item_ID').autocomplete(options);
    options = {
        url: basePath + "/Suggest/Note",
        highlight: false
    };
    $('.Note_ID').autocomplete(options);
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
