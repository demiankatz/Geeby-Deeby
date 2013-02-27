/* Note -- this is the script for editing the people list; edit_person.js is for the
 *         single person edit page.
 */

// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a person:
 */
function editPerson(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/Person/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add Person" : ("Edit Person " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the people on the screen:
 */
function redrawPeople()
{
    var url = basePath + '/edit/PersonList';
    $('#people_list').load(url);
}

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
    var url = basePath + '/edit/Person/' + encodeURIComponent(personID);
    $.post(url, {id: personID, first: first, middle: middle, last: last, bio: bio}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the person list.
            redrawPeople();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_person').show();
            $('#save_person_status').html('');
        }
    }, 'json');
}

/* Pop up a dialog to edit a role:
 */
function editRole(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/PersonRole/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add Role" : ("Edit Role " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the roles on the screen:
 */
function redrawRoles()
{
    var url = basePath + '/edit/PersonRoleList';
    $('#role_list').load(url);
}

/* Save the role inside the provided form element:
 */
function saveRole()
{
    // Obtain values from form:
    var roleID = $('#Role_ID').val();
    var role = $('#Role_Name').val();
    
    // Validate form:
    if (role.length == 0) {
        alert('Role cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_role').hide();
    $('#save_role_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/PersonRole/' + encodeURIComponent(roleID);
    $.post(url, {id: roleID, role: role}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the role list.
            redrawRoles();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_role').show();
            $('#save_role_status').html('');
        }
    }, 'json');
}

/* Pop up a dialog to edit an authority:
 */
function editAuthority(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/PersonAuthority/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add Authority" : ("Edit Authority " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the authorities on the screen:
 */
function redrawAuthorities()
{
    var url = basePath + '/edit/PersonAuthorityList';
    $('#authority_list').load(url);
}

/* Save the authority inside the provided form element:
 */
function saveAuthority()
{
    // Obtain values from form:
    var authorityID = $('#Authority_ID').val();
    var authority = $('#Authority_Name').val();
    
    // Validate form:
    if (authority.length == 0) {
        alert('Authority name cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_authority').hide();
    $('#save_authority_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/PersonAuthority/' + encodeURIComponent(authorityID);
    $.post(url, {id: authorityID, authority: authority}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the authority list.
            redrawAuthorities();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_authority').show();
            $('#save_authority_status').html('');
        }
    }, 'json');
}
