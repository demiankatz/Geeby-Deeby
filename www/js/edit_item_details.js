/* Save the item inside the provided form element:
 */
function saveItem()
{
    // Obtain values from form:
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
        len: len,
        endings: endings,
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
    var url = 'ajax.php?module=item&method=getAltTitles&id=' + 
        encodeURIComponent(itemID);
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
    var url = 'ajax.php?module=item&method=addAltTitle';
    var details = {
        item_id: itemID, 
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
    var url = 'ajax.php?module=item&method=deleteAltTitle';
    $.post(url, {item_id: itemID, row_id: rowID}, function(data) {
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

/* Redraw the ISBN list:
 */
function redrawISBNs()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getISBNs&id=' + 
        encodeURIComponent(itemID);
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
    var url = 'ajax.php?module=item&method=addISBN';
    var details = {
        item_id: itemID, 
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
    var url = 'ajax.php?module=item&method=deleteISBN';
    $.post(url, {item_id: itemID, row_id: rowID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawISBNs();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Redraw the code list:
 */
function redrawProductCodes()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getProductCodes&id=' + 
        encodeURIComponent(itemID);
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
    var url = 'ajax.php?module=item&method=addProductCode';
    var details = {
        item_id: itemID, 
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
    var url = 'ajax.php?module=item&method=deleteProductCode';
    $.post(url, {item_id: itemID, row_id: rowID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawProductCodes();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Redraw the platform list:
 */
function redrawPlatforms()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getPlatforms&id=' + 
        encodeURIComponent(itemID);
    $('#platform_list').load(url);
}

/* Save the current platform:
 */
function addPlatform()
{
    var itemID = $('#Item_ID').val();
    var platID = parseInt($('#Platform_ID').val());
    
    // Save and update:
    var url = 'ajax.php?module=item&method=addPlatform';
    var details = {
        item_id: itemID,
        platform_id: platID
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawPlatforms();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
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
    var url = 'ajax.php?module=item&method=deletePlatform';
    $.post(url, {item_id: itemID, platform_id: platID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawPlatforms();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Redraw the adaptation list:
 */
function redrawAdaptations()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getAdaptations&id=' + 
        encodeURIComponent(itemID);
    $('#adapt_into').load(url);
}

/* Redraw the adapted from list:
 */
function redrawAdaptedFrom()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getAdaptedFrom&id=' + 
        encodeURIComponent(itemID);
    $('#adapt_from').load(url);
}

/* Redraw the item bibliography:
 */
function redrawItemBib()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getItemReferences&id=' + 
        encodeURIComponent(itemID);
    $('#item_bib').load(url);
}

/* Redraw the translation list:
 */
function redrawTranslations()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getTranslations&id=' + 
        encodeURIComponent(itemID);
    $('#trans_into').load(url);
}

/* Redraw the translated from list:
 */
function redrawTranslatedFrom()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getTranslatedFrom&id=' + 
        encodeURIComponent(itemID);
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
    var url = 'ajax.php?module=item&method=addAdaptation';
    switch(relationship) {
    case 'from':
        $.post(url, {adapt_id: itemID, source_id: relatedID}, function(data) {
            // If save was successful...
            if (data.success) {
                // Update the list.
                redrawAdaptedFrom();
            } else {
                // Save failed -- display error message:
                alert('Error: ' + data.msg);
            }
        }, 'json');
        break;
    case 'into':
        $.post(url, {source_id: itemID, adapt_id: relatedID}, function(data) {
            // If save was successful...
            if (data.success) {
                // Update the list.
                redrawAdaptations();
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

/* Delete an adaptation:
 */
function deleteAdaptation(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=deleteAdaptation';
    $.post(url, {source_id: itemID, adapt_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawAdaptations();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Delete an adaptation:
 */
function deleteAdaptedFrom(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=deleteAdaptation';
    $.post(url, {adapt_id: itemID, source_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawAdaptedFrom();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
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
    var url = 'ajax.php?module=item&method=addTranslation';
    switch(relationship) {
    case 'from':
        $.post(url, {trans_id: itemID, source_id: relatedID}, function(data) {
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
        $.post(url, {source_id: itemID, trans_id: relatedID}, function(data) {
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
    
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=deleteTranslation';
    $.post(url, {source_id: itemID, trans_id: relatedID}, function(data) {
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
    var url = 'ajax.php?module=item&method=addItemReference';
    $.post(url, {item_id: itemID, bib_item_id: relatedID}, function(data) {
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
    }, 'json');
}

/* Delete an item reference:
 */
function deleteItemReference(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=deleteItemReference';
    $.post(url, {item_id: itemID, bib_item_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawItemBib();
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
    
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=deleteTranslation';
    $.post(url, {trans_id: itemID, source_id: relatedID}, function(data) {
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

/* Redraw date list:
 */
function redrawReleaseDates()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getDates&id=' + 
        encodeURIComponent(itemID);
    $('#date_list').load(url);
}

/* Add a release date:
 */
function saveReleaseDate()
{
    // Extract the basic values:
    var itemID = $('#Item_ID').val();
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
    var url = 'ajax.php?module=item&method=addDate';
    var params =
        {item_id: itemID, year: year, month: month, day: day, note_id: noteID};
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
    
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=deleteDate';
    var params = {item_id: itemID, year: year, month: month, day: day};
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

/* Redraw description list:
 */
function redrawDescriptions()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getDescriptions&id=' + 
        encodeURIComponent(itemID);
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
    var url = 'ajax.php?module=item&method=addDescription';
    $.post(url, {item_id: itemID, source: descType, desc: desc}, function(data) {
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
    var url = 'ajax.php?module=item&method=deleteDescription';
    $.post(url, {item_id: itemID, source: descType}, function(data) {
        // If delete was successful...
        if (data.success) {
            // Update the list.
            redrawDescriptions();
        } else {
            // Delete failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Redraw credit list:
 */
function redrawCredits()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getCredits&id=' + 
        encodeURIComponent(itemID);
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
    var url = 'ajax.php?module=item&method=addCredit';
    var params =
        {item_id: itemID, person_id: person, role_id: role, note_id: noteID, pos: pos};
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
    var url = 'ajax.php?module=item&method=deleteCredit';
    var params = {item_id: itemID, person_id: person, role_id: role};
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
    var url = 'ajax.php?module=item&method=renumberCredit';
    var params =
        {item_id: itemID, person_id: person, role_id: role, pos: pos};
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
    var url = 'ajax.php?module=item&method=getImages&id=' + 
        encodeURIComponent(itemID);
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
    var url = 'ajax.php?module=item&method=addImage';
    var params =
        {item_id: itemID, image: image, thumb: thumb, note_id: noteID, pos: pos};
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
    var url = 'ajax.php?module=item&method=deleteImage';
    var params = {item_id: itemID, image_id: image_id};
    $.post(url, params, function(data) {
        // If delete was successful...
        if (data.success) {
            // Update the list.
            redrawImages();
        } else {
            // Delete failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
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
    var url = 'ajax.php?module=item&method=renumberImage';
    var params =
        {item_id: itemID, image_id: image_id, pos: pos};
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

/* Redraw the list of attached items:
 */
function redrawAttachmentList()
{
    var itemID = $('#Item_ID').val();
    var url = 'ajax.php?module=item&method=getAttachments&id=' + 
        encodeURIComponent(itemID);
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
    var url = 'ajax.php?module=item&method=addAttachment';
    var details = {
        attach_id: attachID, 
        item_id: itemID
    };
    $.post(url, details, function(data) {
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
    var url = 'ajax.php?module=item&method=deleteAttachment';
    $.post(url, {attach_id: attachID, item_id: itemID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawAttachmentList();
        } else {
            // Remove failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
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
    var url = 'ajax.php?module=item&method=renumberAttachment';
    var details = {
        attach_id: attachID, 
        item_id: itemID,
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

// Activate page controls on domready:
$(document).ready(function(){
    // Turn on tabs
    $("#tabs").tabs();
    $("#tabs").tabs('paging', {cycle: true});
    
    // Turn on autocomplete
    var options = {
        url: "ajax.php", 
        extraParams: {module: "item", method: "suggest" }, 
        highlight: false
    };
    $('.Item_ID').autocomplete(options);
    options = {
        url: "ajax.php", 
        extraParams: {module: "note", method: "suggest" }, 
        highlight: false
    };
    $('.Note_ID').autocomplete(options);
    options = {
        url: "ajax.php", 
        extraParams: {module: "people", method: "suggest" }, 
        highlight: false
    };
    $('.Person_ID').autocomplete(options);
});
