// Global reference to current open edit box.
var editBox = false;

/* Save the series inside the provided form element:
 */
function saveSeries()
{
    // Obtain values from form:
    var seriesID = $('#Series_ID').val();
    var seriesName = $('#Series_Name').val();
    var desc = $('#Series_Description').val();
    var lang = $('#Language_ID').val();

    // Validate form:
    if (seriesName.length == 0) {
        alert('Series name cannot be blank.');
        return;
    }

    // Hide save button and display status message to avoid duplicate submission:
    $('#save_series').hide();
    $('#save_series_status').html('Saving...');

    // Use AJAX to save the values:
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID);
    $.post(url, {name: seriesName, desc: desc, lang: lang}, function(data) {
        // If save failed, display error message.
        if (!data.success) {
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_series').show();
        $('#save_series_status').html('');
    }, 'json');
}

/* Add a material type to the series:
 */
function addMaterial()
{
    var seriesID = $('#Series_ID').val();
    var materialID = parseInt($('#Series_Material_Type_ID').val());

    // Validate user selection:
    if (isNaN(materialID)) {
        alert("Please choose a valid material type.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Material/' + encodeURIComponent(materialID);
    $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the material list.
            redrawMaterials();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Remove a material type from the series:
 */
function deleteMaterial(materialID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    // Validate user selection:
    if (isNaN(materialID)) {
        alert("Please choose a valid material type.");
        return;
    }

    // Save and update based on selected relationship:
    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Material/' + encodeURIComponent(materialID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the material list.
            redrawMaterials();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Redraw the material type list:
 */
function redrawMaterials()
{
    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Material';
    $('#material_list').load(url);
}

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

/* Redraw the title list:
 */
function redrawAltTitles()
{
    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/AltTitle';
    $('#alt_title_list').load(url);
}

/* Save the current alternate title:
 */
function addAltTitle()
{
    var seriesID = $('#Series_ID').val();
    var noteID = parseInt($('#Alt_Title_Note').val());

    // Validate user selection:
    if (isNaN(noteID)) {
        noteID = '';
    }

    // Save and update:
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/AltTitle/NEW';
    var details = {
        note_id: noteID,
        title: $('#Alt_Title').val()
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#Alt_Title').val('');
            $('#Alt_Title_Note').val('');

            // Update the list.
            redrawAltTitles();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove a title from the series:
 */
function deleteAltTitle(rowID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    // Validate user selection:
    if (isNaN(rowID)) {
        alert("Please choose a valid title.");
        return;
    }

    // Save and update:
    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/AltTitle/' + encodeURIComponent(rowID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawAltTitles();
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

/* Save selected categories:
 */
function saveCategories()
{
    // Create an array of all checked categories:
    var values = [];
    $('.Category_ID').each(function(intIndex) {
        if ($(this).is(':checked')) {
            values[values.length] = $(this).val();
        }
    });

    // Hide save button and display status message to avoid duplicate submission:
    $('#save_categories').hide();
    $('#save_categories_status').html('Saving...');

    // Use AJAX to save the values:
    var url = basePath + '/edit/Series/' + encodeURIComponent($('#Series_ID').val()) + '/Categories';
    $.post(url, {"categories[]": values}, function(data) {
        // If save failed, display error message.
        if (!data.success) {
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_categories').show();
        $('#save_categories_status').html('');
    }, 'json');
}

/* Redraw the translation list:
 */
function redrawTranslations()
{
    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Translation';
    $('#trans_into').load(url);
}

/* Redraw the translated from list:
 */
function redrawTranslatedFrom()
{
    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/TranslatedFrom';
    $('#trans_from').load(url);
}

/* Save a translation:
 */
function saveTranslation()
{
    var seriesID = $('#Series_ID').val();
    var relationship = $('#trans_type').val();
    var relatedID = parseInt($('#trans_name').val());

    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid series.");
        return;
    }

    // Save and update based on selected relationship:
    switch(relationship) {
    case 'from':
        var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/TranslatedFrom/' + encodeURIComponent(relatedID);
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
        var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Translation/' + encodeURIComponent(relatedID);
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

    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Translation/' + encodeURIComponent(relatedID);
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

    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/TranslatedFrom/' + encodeURIComponent(relatedID);
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

/* Redraw the list of items linked to the series:
 */
function redrawItemList()
{
    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Item';
    $('#item_list').load(url);
}

/* Add a new item to the series:
 */
function addNewItem()
{
    // Open the edit dialog box:
    var url = basePath + '/edit/Item/NEW';
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: "Add Item",
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editItemForm').remove(); }
    });
}

/* Add an existing item to the series:
 */
function addExistingItem()
{
    var seriesID = $('#Series_ID').val();
    var itemID = parseInt($('#item_name').val());

    // Validate user selection:
    if (isNaN(itemID)) {
        alert("Please choose a valid item.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Item/' + encodeURIComponent(itemID);
    $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#item_name').val('');

            // Update the list.
            redrawItemList();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Remove an item from the series:
 */
function removeFromSeries(itemID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var seriesID = $('#Series_ID').val();
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/Item/' + encodeURIComponent(itemID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawItemList();
        } else {
            // Remove failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Change the position of an item within the series:
 */
function changeSeriesOrder(editionID)
{
    var seriesID = $('#Series_ID').val();
    var pos = parseInt($('#order' + editionID).val(), 10);

    // Validate user selection:
    if (isNaN(editionID)) {
        alert("Please choose a valid item.");
        return;
    } else if (isNaN(pos)) {
        alert("Please enter a valid number.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Series/' + encodeURIComponent(seriesID) + '/ItemOrder';
    var details = {
        edition_id: editionID,
        pos: pos
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawItemList();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Save the item inside the provided form element:
 */
function saveItem()
{
    // Obtain values from form:
    var seriesID = $('#Series_ID').val();
    var itemID = $('#Item_ID').val();
    var itemName = $('#Item_Name').val();
    var errata = $('#Item_Errata').val();
    var thanks = $('#Item_Thanks').val();
    var material = $('#Material_Type_ID').val();

    // Length and endings are actually Edition fields, but we load the data here
    // for convenience when creating items.
    var len = $('#Item_Length').val();
    var endings = $('#Item_Endings').val();

    // Validate form:
    if (itemName.length == 0) {
        alert('Item name cannot be blank.');
        return;
    }

    // Hide save button and display status message to avoid duplicate submission:
    $('#save_item').hide();
    $('#save_item_status').html('Saving...');

    // Use AJAX to save the values:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID);
    var details = {
        name: itemName,
        len: len,
        endings: endings,
        errata: errata,
        thanks: thanks,
        material: material,
        series_id: seriesID
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

            // Redraw the item list:
            redrawItemList();
        } else {
            alert('Error: ' + data.msg);

            // Restore save button:
            $('#save_item').show();
            $('#save_item_status').html('');
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

// Load data and setup autocomplete.
$.ajax({
  url: basePath + "/Suggest/Publisher", 
  success: function(data) {
    $('#Publisher_ID').autocomplete({
      source: data.split('\n'),
      highlight: false
    });
  }
});
$.ajax({
  url: basePath + "/Suggest/Series", 
  success: function(data) {
    $('#trans_name').autocomplete({
      source: data.split('\n'),
      highlight: false
    });
  }
});
$.ajax({
  url: basePath + "/Suggest/Item", 
  success: function(data) {
    $('#item_name').autocomplete({
      source: data.split('\n'),
      highlight: false
    });
  }
});
$.ajax({
  url: basePath + "/Suggest/Note", 
  success: function(data) {
    $('.Note_ID').autocomplete({
      source: data.split('\n'),
      highlight: false
    });
  }
});