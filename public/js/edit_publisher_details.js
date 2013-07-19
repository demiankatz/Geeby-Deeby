/* Save the publisher inside the provided form element:
 */
function savePublisher()
{
    // Obtain values from form:
    var publisherID = $('#Publisher_ID').val();
    var publisher = $('#Publisher_Name').val();
    
    // Validate form:
    if (publisher.length == 0) {
        alert('Publisher cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_publisher').hide();
    $('#save_publisher_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/Publisher/' + encodeURIComponent(publisherID);
    $.post(url, {publisher: publisher}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the publisher list.
            redrawPublishers();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_publisher').show();
            $('#save_publisher_status').html('');
        }
    }, 'json');
}

/* Redraw the imprint list:
 */
function redrawImprints()
{
    var pubID = $('#Publisher_ID').val();
    var url = basePath + '/edit/Publisher/' + encodeURIComponent(pubID) + '/Imprint';
    $('#imprint_list').load(url);
}

/* Save the current imprint:
 */
function addImprint()
{
    var pubID = $('#Publisher_ID').val();

    // Save and update:
    var url = basePath + '/edit/Publisher/' + encodeURIComponent(pubID) + '/Imprint/NEW';
    var details = {
        imprint: $('#Imprint').val()
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#Imprint').val('');

            // Update the list.
            redrawImprints();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove an imprint from the publisher:
 */
function deleteImprint(rowID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    // Validate user selection:
    if (isNaN(rowID)) {
        alert("Please choose a valid imprint.");
        return;
    }

    // Save and update:
    var pubID = $('#Publisher_ID').val();
    var url = basePath + '/edit/Publisher/' + encodeURIComponent(pubID) + '/Imprint/' + encodeURIComponent(rowID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawImprints();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Redraw the address list:
 */
function redrawAddresses()
{
    var pubID = $('#Publisher_ID').val();
    var url = basePath + '/edit/Publisher/' + encodeURIComponent(pubID) + '/Address';
    $('#address_list').load(url);
}

/* Save the current address:
 */
function addAddress()
{
    var pubID = $('#Publisher_ID').val();

    // Save and update:
    var url = basePath + '/edit/Publisher/' + encodeURIComponent(pubID) + '/Address/NEW';
    var details = {
        country: $('#Country_ID').val(),
        city: $('#City_ID').val(),
        street: $('#Street').val()
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#Street').val('');

            // Update the list.
            redrawAddresses();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove an address from the publisher:
 */
function deleteAddress(rowID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    // Validate user selection:
    if (isNaN(rowID)) {
        alert("Please choose a valid address.");
        return;
    }

    // Save and update:
    var pubID = $('#Publisher_ID').val();
    var url = basePath + '/edit/Publisher/' + encodeURIComponent(pubID) + '/Address/' + encodeURIComponent(rowID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawAddresses();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

// Activate page controls on domready:
$(document).ready(function(){
    // Turn on tabs
    $("#tabs").tabs();
    $("#tabs").tabs('paging', {cycle: true});
});
