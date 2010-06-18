// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a material type:
 */
function editMaterial(id)
{
    // Open the edit dialog box:
    var url = 'ajax.php?module=materialtype&method=edit&id=' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === false ? "Add Material Type" : ("Edit Material Type " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the material types on the screen:
 */
function redrawMaterials()
{
    var url = 'ajax.php?module=materialtype&method=getList';
    $('#material_list').load(url);
}

/* Save the material type inside the provided form element:
 */
function saveMaterial()
{
    // Obtain values from form:
    var materialID = $('#Material_Type_ID').val();
    var material = $('#Material_Type_Name').val();
    
    // Validate form:
    if (material.length == 0) {
        alert('Material type cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_material').hide();
    $('#save_material_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = 'ajax.php?module=materialtype&method=save';
    $.post(url, {id: materialID, material: material}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the material type list.
            redrawMaterials();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_material').show();
            $('#save_material_status').html('');
        }
    }, 'json');
}
