/* Save the item inside the provided form element:
 */
function saveItem()
{
    // Obtain values from form:
    var itemID = $('#Item_ID').val();
    var itemName = $('#Item_Name').val();
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
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID);
    var details = {
        name: itemName,
        errata: errata,
        thanks: thanks,
        material: material
    };
    $.post(url, details, function(data) {
        // If save failed, display error message.
        if (!data.success) {
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_item').show();
        $('#save_item_status').html('');
    }, 'json');
}

/* Redraw the title list:
 */
function redrawAltTitles()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AltTitle';
    $('#alt_title_list').load(url);
}

/* Save the current alternate title:
 */
function addAltTitle()
{
    var itemID = $('#Item_ID').val();
    var noteID = parseInt($('#Alt_Title_Note').val());

    // Validate user selection:
    if (isNaN(noteID)) {
        noteID = '';
    }

    // Save and update:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AltTitle/NEW';
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

/* Remove a title from the item:
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
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AltTitle/' + encodeURIComponent(rowID);
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

/* Redraw the ISBN list:
 */
function redrawISBNs()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/ISBN';
    $('#item_isbns').load(url);
}

/* Save the current ISBN:
 */
function addISBN()
{
    var itemID = $('#Item_ID').val();
    var noteID = parseInt($('#isbn_note').val());

    // Validate user selection:
    if (isNaN(noteID)) {
        noteID = '';
    }

    // Save and update:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/ISBN/NEW';
    var details = {
        note_id: noteID,
        isbn: $('#isbn').val()
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#isbn').val('');
            $('#isbn_note').val('');

            // Update the list.
            redrawISBNs();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove an ISBN from the item:
 */
function deleteISBN(rowID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    // Validate user selection:
    if (isNaN(rowID)) {
        alert("Please choose a valid ISBN.");
        return;
    }

    // Save and update:
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/ISBN/' + encodeURIComponent(rowID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawISBNs();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Redraw the code list:
 */
function redrawProductCodes()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/ProductCode';
    $('#item_codes').load(url);
}

/* Save the current product code:
 */
function addProductCode()
{
    var itemID = $('#Item_ID').val();
    var noteID = parseInt($('#product_code_note').val());

    // Validate user selection:
    if (isNaN(noteID)) {
        noteID = '';
    }

    // Save and update:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/ProductCode/NEW';
    var details = {
        note_id: noteID,
        code: $('#product_code').val()
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#product_code').val('');
            $('#product_code_note').val('');

            // Update the list.
            redrawProductCodes();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove a code from the item:
 */
function deleteProductCode(rowID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    // Validate user selection:
    if (isNaN(rowID)) {
        alert("Please choose a valid code.");
        return;
    }

    // Save and update:
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/ProductCode/' + encodeURIComponent(rowID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawProductCodes();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Redraw the platform list:
 */
function redrawPlatforms()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Platform';
    $('#platform_list').load(url);
}

/* Save the current platform:
 */
function addPlatform()
{
    var itemID = $('#Item_ID').val();
    var platID = parseInt($('#Platform_ID').val());

    // Save and update:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Platform/' + encodeURIComponent(platID);
    $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawPlatforms();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Remove a platform from the item:
 */
function deletePlatform(platID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    // Validate user selection:
    if (isNaN(platID)) {
        alert("Please choose a valid platform.");
        return;
    }

    // Save and update:
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Platform/' + encodeURIComponent(platID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawPlatforms();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

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

/* Redraw the item bibliography:
 */
function redrawItemBib()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AboutItem';
    $('#item_bib').load(url);
}

/* Redraw the series bibliography:
 */
function redrawSeriesBib()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AboutSeries';
    $('#series_bib').load(url);
}

/* Redraw the person bibliography:
 */
function redrawPersonBib()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AboutPerson';
    $('#people_bib').load(url);
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

/* Save an item reference:
 */
function addItemReference()
{
    var itemID = $('#Item_ID').val();
    var relatedID = parseInt($('#item_bib_id').val());

    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid item.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AboutItem/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form
            $('#item_bib_id').val('');

            // Update the list.
            redrawItemBib();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Delete an item reference:
 */
function deleteItemReference(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AboutItem/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawItemBib();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Save a series reference:
 */
function addSeriesReference()
{
    var itemID = $('#Item_ID').val();
    var relatedID = parseInt($('#series_bib_id').val());

    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid series.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AboutSeries/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form
            $('#series_bib_id').val('');

            // Update the list.
            redrawSeriesBib();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Delete a series reference:
 */
function deleteSeriesReference(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AboutSeries/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawSeriesBib();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Save a person reference:
 */
function addPersonReference()
{
    var itemID = $('#Item_ID').val();
    var relatedID = parseInt($('#person_bib_id').val());

    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid person.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AboutPerson/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form
            $('#person_bib_id').val('');

            // Update the list.
            redrawPersonBib();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Delete a person reference:
 */
function deletePersonReference(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/AboutPerson/' + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawPersonBib();
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

/* Redraw image list:
 */
function redrawImages()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Image';
    $('#image_list').load(url);
}

/* Add an image:
 */
function saveImage()
{
    // Extract the basic values:
    var itemID = $('#Item_ID').val();
    var noteID = parseInt($('#image_note').val());
    if (isNaN(noteID)) {
        noteID = '';
    }
    var pos = parseInt($('#image_position').val());
    if (isNaN(pos)) {
        pos = 0;
    }
    var image = $('#image_path').val();
    var thumb = $('#thumb_path').val();

    // Save the image:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Image/NEW';
    var params =
        {image: image, thumb: thumb, note_id: noteID, pos: pos};
    $.post(url, params, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawImages();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove an image:
 */
function removeImage(image_id, role)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Image/' + encodeURIComponent(image_id);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If delete was successful...
        if (data.success) {
            // Update the list.
            redrawImages();
        } else {
            // Delete failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Renumber an image:
 */
function changeImageOrder(image_id)
{
    // Validate parameters:
    if (isNaN(image_id)) {
        alert('Please select a valid image.');
        return;
    }

    // Extract the basic values:
    var itemID = $('#Item_ID').val();
    var pos = parseInt($('#image_order' + image_id).val());
    if (isNaN(pos)) {
        pos = 0;
    }

    // Renumber the image:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/ImageOrder/' + encodeURIComponent(image_id);
    $.post(url, {pos: pos}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawImages();
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
    var attachID = parseInt($('#attachment_name').val());

    // Validate user selection:
    if (isNaN(attachID)) {
        alert("Please choose a valid item.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Attachment/' + encodeURIComponent(attachID);
    $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#attachment_name').val('');

            // Update the list.
            redrawAttachmentList();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
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
