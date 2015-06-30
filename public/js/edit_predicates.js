// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a predicate:
 */
function editPredicate(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/Predicate/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add Predicate" : ("Edit Predicate " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the predicates on the screen:
 */
function redrawPredicates()
{
    var url = basePath + '/edit/PredicateList';
    $('#predicate_list').load(url);
}

/* Save the predicate inside the provided form element:
 */
function savePredicate()
{
    // Obtain values from form:
    var predicateID = $('#Predicate_ID').val();
    var predicate = $('#Predicate').val();
    var abbrev = $('#Predicate_Abbrev').val();
    
    // Validate form:
    if (predicate.length == 0) {
        alert('Predicate cannot be blank.');
        return;
    }
    if (abbrev.length == 0) {
        alert('Abbreviation cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_predicate').hide();
    $('#save_predicate_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/Predicate/' + encodeURIComponent(predicateID);
    $.post(url, {predicate: predicate, abbrev: abbrev}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the predicate list.
            redrawPredicates();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_predicate').show();
            $('#save_predicate_status').html('');
        }
    }, 'json');
}
