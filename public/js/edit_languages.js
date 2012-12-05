// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a language:
 */
function editLanguage(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/Language/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add Language" : ("Edit Language " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the languages on the screen:
 */
function redrawLanguages()
{
    var url = basePath + '/edit/LanguageList';
    $('#language_list').load(url);
}

/* Save the language inside the provided form element:
 */
function saveLanguage()
{
    // Obtain values from form:
    var languageID = $('#Language_ID').val();
    var language = $('#Language_Name').val();
    
    // Validate form:
    if (language.length == 0) {
        alert('Language cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_language').hide();
    $('#save_language_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/Language/' + encodeURIComponent(languageID);
    $.post(url, {language: language}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the language list.
            redrawLanguages();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_language').show();
            $('#save_language_status').html('');
        }
    }, 'json');
}
