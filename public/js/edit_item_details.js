/* Redraw the adaptation list:
 */
function redrawAdaptations()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Adaptation';
    $('#adapt_into').load(url);
}

/* Redraw the adapted from list:
 */
function redrawAdaptedFrom()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AdaptedFrom';
    $('#adapt_from').load(url);
}

/* Redraw the translation list:
 */
function redrawTranslations()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Translation';
    $('#trans_into').load(url);
}

/* Redraw the translated from list:
 */
function redrawTranslatedFrom()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/TranslatedFrom';
    $('#trans_from').load(url);
}

/* Save an adaptation:
 */
function saveAdaptation()
{
    var itemID = $('#Item_ID').val();
    var relationship = $('#adapt_type').val();
    var relatedID = parseInt($('#adapt_name').val());

    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid item.");
        return;
    }

    // Save and update based on selected relationship:
    switch(relationship) {
    case 'from':
        var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AdaptedFrom/' + encodeURIComponent(relatedID);
        $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
            // If save was successful...
            if (data.success) {
                // Update the list.
                redrawAdaptedFrom();
            } else {
                // Save failed -- display error message:
                alert('Error: ' + data.msg);
            }
        }});
        break;
    case 'into':
        var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Adaptation/' + encodeURIComponent(relatedID);
        $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
            // If save was successful...
            if (data.success) {
                // Update the list.
                redrawAdaptations();
            } else {
                // Save failed -- display error message:
                alert('Error: ' + data.msg);
            }
        }});
        break;
    default:
        alert('Unknown relationship.');
        return;
        break;
    }
}

/* Delete an adaptation:
 */
function deleteAdaptation(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Adaptation/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawAdaptations();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Delete an adaptation:
 */
function deleteAdaptedFrom(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AdaptedFrom/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawAdaptedFrom();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Save a translation:
 */
function saveTranslation()
{
    var itemID = $('#Item_ID').val();
    var relationship = $('#trans_type').val();
    var relatedID = parseInt($('#trans_name').val());

    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid item.");
        return;
    }

    // Save and update based on selected relationship:
    switch(relationship) {
    case 'from':
        var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/TranslatedFrom/' + encodeURIComponent(relatedID);
        $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
            // If save was successful...
            if (data.success) {
                // Update the list.
                redrawTranslatedFrom();
            } else {
                // Save failed -- display error message:
                alert('Error: ' + data.msg);
            }
        }});
        break;
    case 'into':
        var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Translation/' + encodeURIComponent(relatedID);
        $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
            // If save was successful...
            if (data.success) {
                // Update the list.
                redrawTranslations();
            } else {
                // Save failed -- display error message:
                alert('Error: ' + data.msg);
            }
        }});
        break;
    default:
        alert('Unknown relationship.');
        return;
        break;
    }
}

/* Delete a translation:
 */
function deleteTranslation(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Translation/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawTranslations();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Delete a translation:
 */
function deleteTranslatedFrom(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/TranslatedFrom/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawTranslatedFrom();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Redraw description list:
 */
function redrawDescriptions()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Description';
    $('#desc_list').load(url);
}

/* Add a description:
 */
function saveDescription()
{
    // Extract the basic values:
    var itemID = $('#Item_ID').val();
    var descType = $('#DescriptionType').val();
    var desc = $('#Description').val();

    // Save the date:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Description/' + encodeURIComponent(descType);
    $.post(url, {desc: desc}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawDescriptions();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove a description:
 */
function deleteDescription(descType)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Description/' + encodeURIComponent(descType);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If delete was successful...
        if (data.success) {
            // Update the list.
            redrawDescriptions();
        } else {
            // Delete failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Redraw credit list:
 */
function redrawCredits()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Credits';
    $('#credits').load(url);
}

/* Add a credit:
 */
function saveCredit()
{
    // Extract the basic values:
    var itemID = $('#Item_ID').val();
    var noteID = parseInt($('#credit_note').val());
    if (isNaN(noteID)) {
        noteID = '';
    }
    var pos = parseInt($('#credit_position').val());
    if (isNaN(pos)) {
        pos = 0;
    }
    var person = parseInt($('#credit_person').val());
    if (isNaN(person)) {
        alert('Please select a valid person.');
        return;
    }
    var role = parseInt($('#Role_ID').val());
    if (isNaN(role)) {
        alert('Please select a valid role.');
        return;
    }

    // Save the credit:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AddCredit';
    var params =
        {person_id: person, role_id: role, note_id: noteID, pos: pos};
    $.post(url, params, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawCredits();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove a credit:
 */
function removeCredit(person, role)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/DeleteCredit';
    var params = {person_id: person, role_id: role};
    $.post(url, params, function(data) {
        // If delete was successful...
        if (data.success) {
            // Update the list.
            redrawCredits();
        } else {
            // Delete failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Renumber a credit:
 */
function changeCreditOrder(person, role)
{
    // Validate parameters:
    if (isNaN(person)) {
        alert('Please select a valid person.');
        return;
    }
    if (isNaN(role)) {
        alert('Please select a valid role.');
        return;
    }

    // Extract the basic values:
    var itemID = $('#Item_ID').val();
    var pos = parseInt($('#credit_order' + person + '_' + role).val());
    if (isNaN(pos)) {
        pos = 0;
    }

    // Renumber the credit:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/CreditOrder';
    var params =
        {person_id: person, role_id: role, pos: pos};
    $.post(url, params, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawCredits();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

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
