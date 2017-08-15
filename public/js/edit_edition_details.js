var EditionEditor = function() {
    this.type = "Edition";
    this.saveFields = {
        'name': { 'id': '#Edition_Name', emptyError: 'Edition name cannot be blank.' },
        'desc': { 'id': '#Edition_Description' },
        'volume': { 'id': '#Volume' },
        'position': { 'id': '#Position' },
        'replacement_number': { 'id': '#Replacement_Number' },
        'item_id': { 'id': '#Edition_Item_ID' },
        'series_id': { 'id': '#Series_ID' },
        'len': { 'id': '#Edition_Length' },
        'endings': { 'id': '#Edition_Endings' },
        'parent_edition_id': { 'id': '#Parent_Edition_ID' },
        'position_in_parent': { 'id': '#Position_In_Parent' },
        'extent_in_parent': { 'id': '#Extent_In_Parent' }
    };
    this.attributeSelector = '.edition-attribute';
    this.links = {
        'Date': {
            'saveFields': {
                'year': { 'id': '#releaseYear', 'nonNumericDefault': 0 },
                'month': {
                    'id': '#releaseMonth', 'nonNumericDefault': 0, 'customValidator': function (month) {
                        if (month < 0 || month > 12) {
                            alert('Please enter a valid month.');
                            return false;
                        }
                        return true;
                    }
                },
                'day': {
                    'id': '#releaseDay', 'nonNumericDefault': 0, 'customValidator': function (day) {
                        if (day < 0 || day > 31) {
                            alert('Please enter a valid day.');
                            return false;
                        }
                        return true;
                    }
                },
                'note_id': { 'id': '#releaseNote', 'nonNumericDefault': '' }
            }
        },
        'FullText': {
            'saveFields': {
                'source_id': { 'id': '#Full_Text_Source_ID' },
                'url': { 'id': '#Full_Text_URL', 'emptyError': 'URL cannot be blank.' }
            }
        }
    }
};
BaseEditor.prototype.registerSubclass(EditionEditor);

/**
 * Override the standard "redraw after save" behavior.
 */
EditionEditor.prototype.redrawAfterSave = function() {
    redrawItemAltTitles();
    redrawSeriesAltTitles();
    redrawSeriesPublishers();
    redrawNextAndPrev();
}

var Edition = new EditionEditor();

/**
 * Override the standard Item redraw list function:
 */
Item.redrawList = function() {
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/Item';
    $('#item_list').load(url);
};    

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

/* Redraw the platform list:
 */
function redrawPlatforms()
{
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/Platform';
    $('#platform_list').load(url);
}

/* Save the current platform:
 */
function addPlatform()
{
    var edID = $('#Edition_ID').val();
    var platID = parseInt($('#Platform_ID').val());

    // Save and update:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/Platform/' + encodeURIComponent(platID);
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

/* Remove a platform from the edition:
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
    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/Platform/' + encodeURIComponent(platID);
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

/* Add an existing item to the edition:
 */
function addExistingItem()
{
    var edID = $('#Edition_ID').val();
    var itemID = parseInt($('#item_name').val());

    // Validate user selection:
    if (isNaN(itemID)) {
        alert("Please choose a valid item.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/Item/' + encodeURIComponent(itemID);
    $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Clear the form:
            $('#item_name').val('');

            // Update the list.
            Item.redrawList();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Remove an item from the edition:
 */
function removeFromContents(itemID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var edID = $('#Edition_ID').val();
    var url = basePath + '/edit/Edition/' + encodeURIComponent(edID) + '/Item/' + encodeURIComponent(itemID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            Item.redrawList();
        } else {
            // Remove failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Change the position of an item within the contents of the edition:
 */
function changeContentsOrder(editionID)
{
    var mainEditionID = $('#Edition_ID').val();
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
    var url = basePath + '/edit/Edition/' + encodeURIComponent(mainEditionID) + '/ItemOrder';
    var details = {
        edition_id: editionID,
        pos: pos
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

// Load data and setup autocomplete.
$(document).ready(function() {
  registerAutocomplete('.Item_ID', 'Item');
  registerAutocomplete('.Note_ID', 'Note');
  registerAutocomplete('.Person_ID', 'Person');
  registerAutocomplete('.Series_ID', 'Series');
});
