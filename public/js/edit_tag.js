/* Redraw the URI list:
 */
function redrawURIs()
{
    var tagID = $('#Tag_ID').val();
    var url = basePath + '/edit/Tag/' + encodeURIComponent(tagID) + "/URI";
    $('#uri_list').load(url);
}

/* Save a URI to the current tag:
 */
function addURI()
{
    var tagID = $('#Tag_ID').val();
    var predicateID = $('#Predicate_ID').val();
    var uri = $('#uri').val();

    // Validate user selection:
    if (uri.length < 7) {
        alert("Please choose a valid URI.");
        return;
    }

    // Save and update based on selected relationship:
    var url = basePath + '/edit/Tag/' + encodeURIComponent(tagID) + "/URI/" + encodeURIComponent(uri);
    $.post(url, { predicate_id: predicateID }, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the tag list.
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

    var tagID = $('#Tag_ID').val();
    var url = basePath + '/edit/Tag/' + encodeURIComponent(tagID) + "/URI/" + encodeURIComponent(uri);
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
