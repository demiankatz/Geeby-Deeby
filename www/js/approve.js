/* Hide the row of the specified user after processing is complete:
 */
function hideUserRow(userID)
{
    var o = document.getElementById('NewUser_' + userID);
    if (o) {
        o.style.display = 'none';
    }
}

/* Hide the controls associated with a particular user:
 */
function hideUserButtons(userID)
{
    var o = document.getElementById('UserButtons_' + userID);
    if (o) {
        o.innerHTML = 'Working...';
    }
}

/* Approve the specified user:
 */
function approveUser(userID)
{
    // Hide buttons to prevent double-click:
    hideUserButtons(userID);
    
    // Use AJAX to save the values:
    var url = 'ajax.php?module=approve&method=approveUser';
    var details = {
        id: userID,
        person_id: parseInt($('#Person_ID_' + userID).val()),
        username: $('#Username_' + userID).val(),
        fullname: $('#Name_' + userID).val(),
        address: $('#Address_' + userID).val()
    };
    $.post(url, details, function(data) {
        // If save failed, display error message.
        if (!data.success) {
            alert('Error: ' + data.msg);
        }
        
        // Hide row now that processing is complete.
        hideUserRow(userID);
    }, 'json');
}

/* Reject the specified user:
 */
function rejectUser(userID)
{
    if (!confirm("Are you sure?")) {
        return;
    }
    
    // Hide buttons to prevent double-click:
    hideUserButtons(userID);
    
    // Use AJAX to save the values:
    var url = 'ajax.php?module=approve&method=rejectUser';
    var details = {
        id: userID
    };
    $.post(url, details, function(data) {
        // If save failed, display error message.
        if (!data.success) {
            alert('Error: ' + data.msg);
        }
        
        // Hide row now that processing is complete.
        hideUserRow(userID);
    }, 'json');
}

// Activate page controls on domready:
$(document).ready(function(){
    options = {
        url: "ajax.php", 
        extraParams: {module: "people", method: "suggest" }, 
        highlight: false
    };
    $('.Person_ID').autocomplete(options);
});
