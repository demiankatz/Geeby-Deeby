// Global reference to current open edit box.
var editBox = false;

/* Save the current publisher:
 */
function addPublisher()
{
    var seriesID = $('#Series_ID').val();
    var publisherID = parseInt($('#Publisher_ID').val());
    var noteID = parseInt($('#Publisher_Note_ID').val());

    // Validate user selection:
    if (isNaN(publisherID)) {
        alert("Please choose a valid publisher.");
        return;
    }
    if (isNaN(noteID)) {
        noteID = '';
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Publisher/NEW';
    var details = {
        publisher_id: publisherID,
        note_id: noteID,
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#Publisher_ID').val('');
            $('#Publisher_Note_ID').val('');

            // Update the publisher list.
            redrawPublishers();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Modify a publisher attached to the series:
 */
function modifyPublisher(rowID)
{
    // Open the edit dialog box:
    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Publisher/' + encodeURIComponent(rowID);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: "Modify Publisher",
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#modifyPublisherForm').remove(); }
    });
}

/* Remove a publisher from the series:
 */
function deletePublisher(rowID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    // Validate user selection:
    if (isNaN(rowID)) {
        alert("Please choose a valid publisher.");
        return;
    }

    // Save and update based on selected relationship:
    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Publisher/' + encodeURIComponent(rowID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the publisher list.
            redrawPublishers();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Redraw the publisher list:
 */
function redrawPublishers()
{
    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Publisher';
    $('#publisher_list').load(url);
}

/* Change the position of an item within the series:
 */
function changeSeriesOrder(editionID)
{
    var seriesID = $('#Series_ID').val();
    var raw = $('#order' + editionID).val().split(',');
    var pos;
    var vol = 0;
    if (raw.length < 2) {
        pos = parseInt(raw[0], 10);
    } else {
        vol = parseInt(raw[0], 10);
        pos = parseInt(raw[1], 10);
    }

    // Validate user selection:
    if (isNaN(editionID)) {
        alert("Please choose a valid item.");
        return;
    } else if (isNaN(pos) || isNaN(vol)) {
        alert("Please enter a valid number.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/ItemOrder';
    var details = {
        edition_id: editionID,
        pos: vol + "," + pos
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            Item.redrawList();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Save the modified publisher information:
 */
function saveModifiedPublisher()
{
    // Obtain values from form:
    var seriesID = $('#Series_ID').val();
    var rowID = $('#Series_Publisher_ID').val();
    var addressID = $('#Address_ID').val();
    var imprintID = $('#Imprint_ID').val();

    // Hide save button and display status message to avoid duplicate submission:
    $('#save_modified_publisher').hide();
    $('#save_modified_publisher_status').html('Saving...');

    // Use AJAX to save the values:
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Publisher/' + encodeURIComponent(rowID);
    var details = {
        address: addressID, imprint: imprintID
    };
    $.post(url, details, function(data) {
        // If save failed, display error message.
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            redrawPublishers();
        } else {
            alert('Error: ' + data.msg);

            // Restore save button:
            $('#save_modified_publisher').show();
            $('#save_modified_publisher_status').html('');
        }
    }, 'json');
}
