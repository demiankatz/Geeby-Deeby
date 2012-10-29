// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a category:
 */
function editCategory(id)
{
    // Open the edit dialog box:
    var url = 'ajax.php?module=category&method=edit&id=' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === false ? "Add Category" : ("Edit Category " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the categories on the screen:
 */
function redrawCategories()
{
    var url = 'ajax.php?module=category&method=getList';
    $('#category_list').load(url);
}

/* Save the category inside the provided form element:
 */
function saveCategory()
{
    // Obtain values from form:
    var categoryID = $('#Category_ID').val();
    var catName = $('#Category').val();
    var desc = $('#Description').val();
    
    // Validate form:
    if (catName.length == 0) {
        alert('Category name cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_category').hide();
    $('#save_category_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = 'ajax.php?module=category&method=save';
    $.post(url, {id: categoryID, name: catName, desc: desc}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the category list.
            redrawCategories();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_category').show();
            $('#save_category_status').html('');
        }
    }, 'json');
}
