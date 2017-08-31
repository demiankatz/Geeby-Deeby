/* Redraw the list of editions:
 */
function redrawEditions()
{
    var itemID = $('#Item_ID').val();
    var url = basePath + '/edit/Item/' + encodeURIComponent(itemID) + '/Editions';
    $('#edition_list').load(url);
}

/* Copy an edition
 */
function copyEdition()
{
    var radio = $('.selectedEdition:checked').val();
    if (!radio) {
        alert("Please select an edition.");
        return;
    }
    // Save and update based on selected relationship:
    var url = basePath + '/edit/Edition/' + encodeURIComponent(radio) + '/Copy';
    $.post(url, {}, function(data) {
        // If save was successful...
        if (data.success) {
            // Update the list.
            redrawEditions();
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
  registerAutocomplete('.Tag_ID', 'Tag');
});
