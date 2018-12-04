/* Save the edition inside the provided form element:
 */
function saveEdition()
{
    // Obtain values from form:
    var editionID = $('#Edition_ID').val();
    var editionName = $('#Edition_Name').val();
    var desc = $('#Edition_Description').val();
    var pos = $('#Position').val();
    var itemID = $('#Item_ID').val();
    var seriesID = $('#Series_ID').val();
    var len = $('#Edition_Length').val();
    var endings = $('#Edition_Endings').val();

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
        desc: desc,
        item_id: itemID,
        series_id: seriesID,
        position: pos,
        len: len,
        endings: endings
    };
    $.post(url, details, function(data) {
        // If save failed, display error message.
        if (!data.success) {
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_edition').show();
        $('#save_edition_status').html('');
        // Redraw alt titles:
        redrawItemAltTitles();
        redrawSeriesAltTitles();
        redrawSeriesPublishers();
        redrawNextAndPrev();
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

/* Redraw credit list:
 */
function redrawCredits()
{
    var editionID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/Credits';
    $('#credits').load(url);
}

/* Add a credit:
 */
function saveCredit()
{
    // Extract the basic values:
    var editionID = $('#Edition_ID').val();
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
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/AddCredit';
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

/* Redraw the item alternate title list:
 */
function redrawItemAltTitles()
{
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/ItemAltTitles';
    $('#item-alt-title-select-container').load(url);
    $('#Preferred_Item_Title_Text').val('');
}

/* Clear the preferred item alternate title:
 */
function deleteItemAltTitle()
{
    // Extract the basic values:
    var editionID = $('#Edition_ID').val();

    // Save the credit:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/ClearPreferredItemTitle';
    $.post(url, {}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawItemAltTitles();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Set the preferred item alternate title:
 */
function saveItemAltTitle()
{
    // Extract the basic values:
    var editionID = $('#Edition_ID').val();
    var titleID = $('#Preferred_Item_Title_ID').val();
    var titleText = false;
    if (titleID == 'NEW') {
        titleText = $('#Preferred_Item_Title_Text').val();
        if (titleText.length < 1) {
            alert('Title cannot be blank.');
            return;
        }
    } else {
        titleID = parseInt(titleID);
        if (isNaN(titleID) || titleID < 1) {
            alert('Invalid title selection.');
            return;
        }
    }

    // Save the title:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/SetPreferredItemTitle';
    var params = {};
    if (titleText) {
        params.title_text = titleText;
    } else {
        params.title_id = titleID;
    }
    $.post(url, params, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawItemAltTitles();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Redraw the next and previous links:
 */
function redrawNextAndPrev()
{
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/NextAndPrev';
    $('#nextAndPrev').load(url);
}

/* Redraw the series alternate title list:
 */
function redrawSeriesAltTitles()
{
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/SeriesAltTitles';
    $('#series-alt-title-select-container').load(url);
    $('#Preferred_Series_Title_Text').val('');
}

/* Redraw the series publisher list:
 */
function redrawSeriesPublishers()
{
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/SeriesPublishers';
    $('#series-publisher-select-container').load(url);
}

/* Clear the preferred series alternate title:
 */
function deleteSeriesAltTitle()
{
    // Extract the basic values:
    var editionID = $('#Edition_ID').val();

    // Save the credit:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/ClearPreferredSeriesTitle';
    $.post(url, {}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawSeriesAltTitles();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Set the preferred series alternate title:
 */
function saveSeriesAltTitle()
{
    // Extract the basic values:
    var editionID = $('#Edition_ID').val();
    var titleID = $('#Preferred_Series_Title_ID').val();
    var titleText = false;
    if (titleID == 'NEW') {
        titleText = $('#Preferred_Series_Title_Text').val();
        if (titleText.length < 1) {
            alert('Title cannot be blank.');
            return;
        }
    } else {
        titleID = parseInt(titleID);
        if (isNaN(titleID) || titleID < 1) {
            alert('Invalid title selection.');
            return;
        }
    }

    // Save the title:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/SetPreferredSeriesTitle';
    var params = {};
    if (titleText) {
        params.title_text = titleText;
    } else {
        params.title_id = titleID;
    }
    $.post(url, params, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawSeriesAltTitles();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Set the preferred series publisher:
 */
function saveSeriesPublisher()
{
    // Extract the basic values:
    var editionID = $('#Edition_ID').val();
    var pubID = $('#Series_Publisher_ID').val();

    // Save the publisher:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/SetPreferredPublisher';
    var params = {pub_id: pubID};
    $.post(url, params, function(data) {
        // If save was successful...
        if (!data.success) {
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

    var editionID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/DeleteCredit';
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
    var editionID = $('#Edition_ID').val();
    var pos = parseInt($('#credit_order' + person + '_' + role).val());
    if (isNaN(pos)) {
        pos = 0;
    }

    // Renumber the credit:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/CreditOrder';
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

/* Save a full text URL:
 */
function saveFullText()
{
    // Extract the basic values:
    var editionID = $('#Edition_ID').val();
    var source_id = $('#Full_Text_Source_ID').val();
    var fulltext_url = $('#Full_Text_URL').val();
    if (fulltext_url.length < 1) {
        alert('URL cannot be blank.');
        return;
    }

    // Save the title:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/FullText';
    var params = {source_id: source_id, url: fulltext_url};
    $.post(url, params, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawFullText();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove a release date:
 */
function deleteFullText(id)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/FullText/' + encodeURIComponent(id);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If delete was successful...
        if (data.success) {
            // Update the list.
            redrawFullText();
        } else {
            // Delete failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Redraw credit list:
 */
function redrawFullText()
{
    var editionID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/FullTextList';
    $('#fulltext_list').load(url);
}

/* Redraw the ISBN list:
 */
function redrawISBNs()
{
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/ISBN';
    $('#item_isbns').load(url);
}

/* Save the current ISBN:
 */
function addISBN()
{
    var edID = $('#Edition_ID').val();
    var noteID = parseInt($('#isbn_note').val());

    // Validate user selection:
    if (isNaN(noteID)) {
        noteID = '';
    }

    // Save and update:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/ISBN/NEW';
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

/* Remove an ISBN from the edition:
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
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/ISBN/' + encodeURIComponent(rowID);
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
function redrawOCLCNumbers()
{
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/OCLCNumber';
    $('#item_oclc_numbers').load(url);
}

/* Save the current product code:
 */
function addOCLCNumber()
{
    var edID = $('#Edition_ID').val();
    var noteID = parseInt($('#oclc_number_note').val());

    // Validate user selection:
    if (isNaN(noteID)) {
        noteID = '';
    }

    // Save and update:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/OCLCNumber/NEW';
    var details = {
        note_id: noteID,
        oclc_number: $('#oclc_number').val()
    };
    $.post(url, details, function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#oclc_number').val('');
            $('#oclc_number_note').val('');

            // Update the list.
            redrawOCLCNumbers();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Remove a code from the edition:
 */
function deleteOCLCNumber(rowID)
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
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/OCLCNumber/' + encodeURIComponent(rowID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawOCLCNumbers();
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
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/ProductCode';
    $('#item_codes').load(url);
}

/* Save the current product code:
 */
function addProductCode()
{
    var edID = $('#Edition_ID').val();
    var noteID = parseInt($('#product_code_note').val());

    // Validate user selection:
    if (isNaN(noteID)) {
        noteID = '';
    }

    // Save and update:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/ProductCode/NEW';
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

/* Remove a code from the edition:
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
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/ProductCode/' + encodeURIComponent(rowID);
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

/* Redraw image list:
 */
function redrawImages()
{
    var editionID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/Image';
    $('#image_list').load(url);
}

/* Add an image:
 */
function saveImage()
{
    // Extract the basic values:
    var editionID = $('#Edition_ID').val();
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
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/Image/NEW';
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

    var editionID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/Image/' + encodeURIComponent(image_id);
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
    var editionID = $('#Edition_ID').val();
    var pos = parseInt($('#image_order' + image_id).val());
    if (isNaN(pos)) {
        pos = 0;
    }

    // Renumber the image:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(editionID) + '/ImageOrder/' + encodeURIComponent(image_id);
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