/* Note -- this is the script for editing a single person; edit_people.js is for the
 *         people list page.
 */

/* Save the person inside the provided form element:
 */
function savePerson()
{
    // Obtain values from form:
    var personID = $('#Person_ID').val();
    var first = $('#First_Name').val();
    var middle = $('#Middle_Name').val();
    var last = $('#Last_Name').val();
    var bio = $('#Biography').val();
    
    // Validate form:
    if (last.length == 0) {
        alert('Last name cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_person').hide();
    $('#save_person_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = 'ajax.php?module=people&method=save';
    $.post(url, {id: personID, first: first, middle: middle, last: last, bio: bio}, function(data) {
        // If save was successful...
        if (!data.success) {
            // Save failed -- display error message.
            alert('Error: ' + data.msg);
        }
        // Restore save button:
        $('#save_person').show();
        $('#save_person_status').html('');
    }, 'json');
}

/* Redraw the pseudonym list:
 */
function redrawPseudonyms()
{
    var personID = $('#Person_ID').val();
    var url = 'ajax.php?module=people&method=getPseudonymList&id=' + 
        encodeURIComponent(personID);
    $('#pseudonym_list').load(url);
}

/* Redraw the real names list:
 */
function redrawRealNames()
{
    var personID = $('#Person_ID').val();
    var url = 'ajax.php?module=people&method=getRealNameList&id=' + 
        encodeURIComponent(personID);
    $('#realname_list').load(url);
}

/* Save a relationship to the current person:
 */
function saveRelationship()
{
    var personID = $('#Person_ID').val();
    var relationship = $('#pseudo_type').val();
    var relatedID = parseInt($('#pseudo_name').val());
    
    // Validate user selection:
    if (isNaN(relatedID)) {
        alert("Please choose a valid person.");
        return;
    }
    
    // Save and update based on selected relationship:
    var url = 'ajax.php?module=people&method=addPseudonym';
    switch(relationship) {
    case 'pseudonym':
        $.post(url, {real_id: personID, pseudo_id: relatedID}, function(data) {
            // If save was successful...
            if (data.success) {
                // Update the person list.
                redrawPseudonyms();
            } else {
                // Save failed -- display error message:
                alert('Error: ' + data.msg);
            }
        }, 'json');
        break;
    case 'realname':
        $.post(url, {pseudo_id: personID, real_id: relatedID}, function(data) {
            // If save was successful...
            if (data.success) {
                // Update the person list.
                redrawRealNames();
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

/* Delete a real name:
 */
function deleteRealName(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var personID = $('#Person_ID').val();
    var url = 'ajax.php?module=people&method=deletePseudonym';
    $.post(url, {pseudo_id: personID, real_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the person list.
            redrawRealNames();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Delete a pseudonym:
 */
function deletePseudonym(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    var personID = $('#Person_ID').val();
    var url = 'ajax.php?module=people&method=deletePseudonym';
    $.post(url, {real_id: personID, pseudo_id: relatedID}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the person list.
            redrawPseudonyms();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

// Activate autocomplete when DOM is ready:
$(document).ready(function(){
    var options = {
        url: basePath + "/Suggest/Person",
        highlight: false
    };
    $('#pseudo_name').autocomplete(options);
});
