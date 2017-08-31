/* Redraw the list of attached items:
 */
function redrawAttachmentList()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Attachment';
    $('#item_list').load(url);
}

/* Attach one item to another:
 */
function addAttachedItem()
{
    var itemID = $('#Item_ID').val();
    var noteID = parseInt($('#Attachment_Note').val());
    var attachID = parseInt($('#attachment_name').val());

    // Validate user selection:
    if (isNaN(attachID)) {
        alert("Please choose a valid item.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Attachment/' + encodeURIComponent(attachID);
    $.post(url, {note_id: noteID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#attachment_name').val('');
            $('#Attachment_Note').val('');

            // Update the list.
            redrawAttachmentList();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove an attached item:
 */
function removeAttachment(attachID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Attachment/' + encodeURIComponent(attachID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawAttachmentList();
        } else {
            // Remove failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Change the position of an item within the series:
 */
function changeAttachmentOrder(attachID)
{
    var itemID = $('#Item_ID').val();
    var pos = parseInt($('#attachment' + attachID).val(), 10);

    // Validate user selection:
    if (isNaN(itemID)) {
        alert("Please choose a valid item.");
        return;
    } else if (isNaN(pos)) {
        alert("Please enter a valid number.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AttachmentOrder';
    var details = {
        attach_id: attachID,
        pos: pos
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawAttachmentList();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Redraw the list of editions:
 */
function redrawEditions()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Editions';
    $('#edition_list').load(url);
}

/* Copy an edition
 */
function copyEdition()
{
    var radio = $('.selectedEdition:checked').val();
    if (!radio) {
        alert("Please select an edition.");
        return;
    }
    // Save and update based on selected relationship:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(radio) + '/Copy';
    $.post(url, {}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawEditions();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

// Load data and setup autocomplete.
$(document).ready(function() {
  registerAutocomplete('.Item_ID', 'Item');
  registerAutocomplete('.Note_ID', 'Note');
  registerAutocomplete('.Person_ID', 'Person');
  registerAutocomplete('.Series_ID', 'Series');
  registerAutocomplete('.Tag_ID', 'Tag');
});
