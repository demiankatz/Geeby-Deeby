// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a material type:
 */
function editMaterial(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/MaterialType/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add Material Type" : ("Edit Material Type " + id)),
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
    var url = basePath + '/edit/MaterialTypeList';
    $('#material_list').load(url);
}

/* Save the material type inside the provided form element:
 */
function saveMaterial()
{
    // Obtain values from form:
    var materialID = $('#Material_Type_ID').val();
    var material = $('#Material_Type_Name').val();
    var material_plural = $('#Material_Type_Plural_Name').val();
    var material_default = $('#Default').is(':checked');

    // Validate form:
    if (material.length == 0) {
        alert('Material type cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_material').hide();
    $('#save_material_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/MaterialType/' + encodeURIComponent(materialID);
    $.post(url, {material: material, material_plural: material_plural, default: material_default ? 1 : 0}, function(data) {
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
