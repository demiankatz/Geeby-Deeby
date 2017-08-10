/* Note -- this is the script for editing a single person; edit_people.js is for the
 *         people list page.
 */

/* Redraw the pseudonym list:
 */
function redrawPseudonyms()
{
    var personID = $('#Person_ID').val();
    var url = basePath + '/edit/Person/' + encodeURIComponent(personID) + "/Pseudonym";
    $('#pseudonym_list').load(url);
}

/* Redraw the real names list:
 */
function redrawRealNames()
{
    var personID = $('#Person_ID').val();
    var url = basePath + '/edit/Person/' + encodeURIComponent(personID) + "/RealName";
    $('#realname_list').load(url);
}

/* Redraw the URI list:
 */
function redrawURIs()
{
    var personID = $('#Person_ID').val();
    var url = basePath + '/edit/Person/' + encodeURIComponent(personID) + "/URI";
    $('#uri_list').load(url);
}

/* Save a URI to the current person:
 */
function addURI()
{
    var personID = $('#Person_ID').val();
    var predicateID = $('#Predicate_ID').val();
    var uri = $('#uri').val();

    // Validate user selection:
    if (uri.length < 7) {
        alert("Please choose a valid URI.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Person/' + encodeURIComponent(personID) + "/URI/" + encodeURIComponent(uri);
    $.post(url, { predicate_id: predicateID }, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the person list.
            redrawURIs();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }, 'json');
}

/* Delete a URI:
 */
function deleteURI(uri)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var personID = $('#Person_ID').val();
    var url = basePath + '/edit/Person/' + encodeURIComponent(personID) + "/URI/" + encodeURIComponent(uri);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawURIs();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
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
    switch(relationship) {
    case 'pseudonym':
        var url = basePath + '/edit/Person/' + encodeURIComponent(personID) + "/Pseudonym/" + encodeURIComponent(relatedID);
        $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
            // If save was successful...
            if (data.success) {
                // Update the person list.
                redrawPseudonyms();
            } else {
                // Save failed -- display error message:
                alert('Error: ' + data.msg);
            }
        }});
        break;
    case 'realname':
        var url = basePath + '/edit/Person/' + encodeURIComponent(personID) + "/RealName/" + encodeURIComponent(relatedID);
        $.ajax({url: url, type: "put", dataType: "json", success: function(data) {
            // If save was successful...
            if (data.success) {
                // Update the person list.
                redrawRealNames();
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

/* Delete a real name:
 */
function deleteRealName(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var personID = $('#Person_ID').val();
    var url = basePath + '/edit/Person/' + encodeURIComponent(personID) + "/RealName/" + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the person list.
            redrawRealNames();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

/* Delete a pseudonym:
 */
function deletePseudonym(relatedID)
{
    if (!confirm("Are you sure?")) {
        return;
    }

    var personID = $('#Person_ID').val();
    var url = basePath + '/edit/Person/' + encodeURIComponent(personID) + "/Pseudonym/" + encodeURIComponent(relatedID);
    $.ajax({url: url, type: "delete", dataType: "json", success: function(data) {
        // If save was successful...
        if (data.success) {
            // Update the person list.
            redrawPseudonyms();
        } else {
            // Save failed -- display error message:
            alert('Error: ' + data.msg);
        }
    }});
}

// Load data and setup autocomplete.
$(document).ready(function() {
  $('#pseudo_name').autocomplete({
    source: function(request, response) {
      $.ajax({
        url: basePath + "/Suggest/Person?q=" + request.term, 
        success: function(data) {
          response(data.split('\n').slice(0, -1));
        }
      });
    }
  })
});