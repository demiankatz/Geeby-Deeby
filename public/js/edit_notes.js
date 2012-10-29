// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a note:
 */
function editNote(id)
{
    // Open the edit dialog box:
    var url = 'ajax.php?module=note&method=edit&id=' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === false ? "Add Note" : ("Edit Note " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the notes on the screen:
 */
function redrawNotes()
{
    var url = 'ajax.php?module=note&method=getList';
    $('#note_list').load(url);
}

/* Save the note inside the provided form element:
 */
function saveNote()
{
    // Obtain values from form:
    var noteID = $('#Note_ID').val();
    var note = $('#Note').val();
    
    // Validate form:
    if (note.length == 0) {
        alert('Note cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_note').hide();
    $('#save_note_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = 'ajax.php?module=note&method=save';
    $.post(url, {id: noteID, note: note}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the note list.
            redrawNotes();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_note').show();
            $('#save_note_status').html('');
        }
    }, 'json');
}
