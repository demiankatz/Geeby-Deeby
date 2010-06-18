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
    var url = 'ajax.php?module=series&method=save';
    $.post(url, {id: seriesID, name: seriesName, desc: desc, lang: lang}, function(data) {
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
    var url = 'ajax.php?module=series&method=addMaterial';
    $.post(url, {series_id: seriesID, material_id: materialID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the material list.
            redrawMaterials();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
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
    var url = 'ajax.php?module=series&method=deleteMaterial';
    $.post(url, {series_id: seriesID, material_id: materialID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the material list.
            redrawMaterials();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Redraw the material type list:
 */
function redrawMaterials()
{
    var seriesID = $('#Series_ID').val();
    var url = 'ajax.php?module=series&method=getMaterials&id=' + 
        encodeURIComponent(seriesID);
    $('#material_list').load(url);
}

/* Save the current publisher:
 */
function addPublisher()
{
    var seriesID = $('#Series_ID').val();
    var publisherID = parseInt($('#Publisher_ID').val());
    var countryID = parseInt($('#Country_ID').val());
    var noteID = parseInt($('#Publisher_Note_ID').val());
    
    // Validate user selection:
    if (isNaN(publisherID)) {
        alert("Please choose a valid publisher.");
        return;
    }
    if (isNaN(countryID)) {
        alert("Please choose a valid country.");
        return;
    }
    if (isNaN(noteID)) {
        noteID = '';
    }
    
    // Save and update based on selected relationship:
    var url = 'ajax.php?module=series&method=addPublisher';
    var details = {
        series_id: seriesID, 
        publisher_id: publisherID,
        country_id: countryID,
        note_id: noteID,
        imprint: $('#Imprint').val()
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#Publisher_ID').val('');
            $('#Country_ID').val('');
            $('#Publisher_Note_ID').val('');
            $('#Imprint').val('');
            
            // Update the publisher list.
            redrawPublishers();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
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
    var url = 'ajax.php?module=series&method=deletePublisher';
    $.post(url, {series_id: seriesID, row_id: rowID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the publisher list.
            redrawPublishers();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Redraw the title list:
 */
function redrawAltTitles()
{
    var seriesID = $('#Series_ID').val();
    var url = 'ajax.php?module=series&method=getAltTitles&id=' + 
        encodeURIComponent(seriesID);
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
    var url = 'ajax.php?module=series&method=addAltTitle';
    var details = {
        series_id: seriesID, 
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
    var url = 'ajax.php?module=series&method=deleteAltTitle';
    $.post(url, {series_id: seriesID, row_id: rowID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawAltTitles();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Redraw the publisher list:
 */
function redrawPublishers()
{
    var seriesID = $('#Series_ID').val();
    var url = 'ajax.php?module=series&method=getPublishers&id=' + 
        encodeURIComponent(seriesID);
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
    var url = 'ajax.php?module=series&method=saveCategories';
    $.post(url, {series_id: $('#Series_ID').val(), "categories[]": values}, function(data) {
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
    var url = 'ajax.php?module=series&method=getTranslations&id=' + 
        encodeURIComponent(seriesID);
    $('#trans_into').load(url);
}

/* Redraw the translated from list:
 */
function redrawTranslatedFrom()
{
    var seriesID = $('#Series_ID').val();
    var url = 'ajax.php?module=series&method=getTranslatedFrom&id=' + 
        encodeURIComponent(seriesID);
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
    var url = 'ajax.php?module=series&method=addTranslation';
    switch(relationship) {
    case 'from':
        $.post(url, {trans_id: seriesID, source_id: relatedID}, function(data) {
            // If save was successful...
            if (data.success) {
                // Update the list.
                redrawTranslatedFrom();
            } else {
                // Save failed -- display error message:
                alert('Error: ' + data.msg);
            }
        }, 'json');
        break;
    case 'into':
        $.post(url, {source_id: seriesID, trans_id: relatedID}, function(data) {
            // If save was successful...
            if (data.success) {
                // Update the list.
                redrawTranslations();
            } else {
                // Save failed -- display error message:
                alert('Error: ' + data.msg);
            }
        }, 'json');
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
    var url = 'ajax.php?module=series&method=deleteTranslation';
    $.post(url, {source_id: seriesID, trans_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawTranslations();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Delete a translation:
 */
function deleteTranslatedFrom(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var seriesID = $('#Series_ID').val();
    var url = 'ajax.php?module=series&method=deleteTranslation';
    $.post(url, {trans_id: seriesID, source_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawTranslatedFrom();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Redraw the list of items linked to the series:
 */
function redrawItemList()
{
    var seriesID = $('#Series_ID').val();
    var url = 'ajax.php?module=series&method=getItems&id=' + 
        encodeURIComponent(seriesID);
    $('#item_list').load(url);
}

/* Add a new item to the series:
 */
function addNewItem()
{
    // Open the edit dialog box:
    var url = 'ajax.php?module=item&method=edit&id=';
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
    var url = 'ajax.php?module=series&method=addItem';
    var details = {
        series_id: seriesID, 
        item_id: itemID
    };
    $.post(url, details, function(data) {
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
    }, 'json');
}

/* Remove an item from the series:
 */
function removeFromSeries(itemID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var seriesID = $('#Series_ID').val();
    var url = 'ajax.php?module=series&method=deleteItem';
    $.post(url, {series_id: seriesID, item_id: itemID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawItemList();
        } else {
            // Remove failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Change the position of an item within the series:
 */
function changeSeriesOrder(itemID)
{
    var seriesID = $('#Series_ID').val();
    var pos = parseInt($('#order' + itemID).val(), 10);
    
    // Validate user selection:
    if (isNaN(itemID)) {
        alert("Please choose a valid item.");
        return;
    } else if (isNaN(pos)) {
        alert("Please enter a valid number.");
        return;
    }
    
    // Save and update based on selected relationship:
    var url = 'ajax.php?module=series&method=renumberItem';
    var details = {
        series_id: seriesID, 
        item_id: itemID,
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
    var len = $('#Item_Length').val();
    var endings = $('#Item_Endings').val();
    var errata = $('#Item_Errata').val();
    var thanks = $('#Item_Thanks').val();
    var material = $('#Material_Type_ID').val();

    // Validate form:
    if (itemName.length == 0) {
        alert('Item name cannot be blank.');
        return;
    }

    // Hide save button and display status message to avoid duplicate submission:
    $('#save_item').hide();
    $('#save_item_status').html('Saving...');

    // Use AJAX to save the values:
    var url = 'ajax.php?module=item&method=save';
    var details = {
        id: itemID, 
        name: itemName, 
        length: len,
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

// Activate page controls on domready:
$(document).ready(function(){
    // Turn on tabs
    $("#tabs").tabs();
    
    // Turn on autocomplete
    var options = {
        url: "ajax.php", 
        extraParams: {module: "publisher", method: "suggest" }, 
        highlight: false
    };
    $('#Publisher_ID').autocomplete(options);
    var options = {
        url: "ajax.php", 
        extraParams: {module: "series", method: "suggest" }, 
        highlight: false
    };
    $('#trans_name').autocomplete(options);
    var options = {
        url: "ajax.php", 
        extraParams: {module: "item", method: "suggest" }, 
        highlight: false
    };
    $('#item_name').autocomplete(options);
    var options = {
        url: "ajax.php", 
        extraParams: {module: "note", method: "suggest" }, 
        highlight: false
    };
    $('.Note_ID').autocomplete(options);
});
