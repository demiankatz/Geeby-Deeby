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

// Activate page controls on domready:
$(document).ready(function(){
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
