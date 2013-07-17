// Global reference to current open edit box.
var editBox = false;

/* Pop up a dialog to edit a country:
 */
function editCountry(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/Country/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add Country" : ("Edit Country " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the countries on the screen:
 */
function redrawCountries()
{
    var url = basePath + '/edit/CountryList';
    $('#country_list').load(url);
}

/* Save the country inside the provided form element:
 */
function saveCountry()
{
    // Obtain values from form:
    var countryID = $('#Country_ID').val();
    var country = $('#Country_Name').val();
    
    // Validate form:
    if (country.length == 0) {
        alert('Country cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_country').hide();
    $('#save_country_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/Country/' + encodeURIComponent(countryID);
    $.post(url, {country: country}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the country list.
            redrawCountries();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_country').show();
            $('#save_country_status').html('');
        }
    }, 'json');
}

/* Pop up a dialog to edit a country:
 */
function editCity(id)
{
    // Open the edit dialog box:
    var url = basePath + '/edit/City/' + encodeURIComponent(id);
    editBox = $('<div>Loading...</div>').load(url).dialog({
        title: (id === 'NEW' ? "Add City" : ("Edit City " + id)),
        modal: true,
        autoOpen: true,
        width: 500,
        height: 400,
        // Remove dialog box contents from the DOM to prevent duplicate identifier problems.
        close: function() { $('#editForm').remove(); }
    });
}

/* Redraw the cities on the screen:
 */
function redrawCities()
{
    var url = basePath + '/edit/CityList';
    $('#city_list').load(url);
}

/* Save the city inside the provided form element:
 */
function saveCity()
{
    // Obtain values from form:
    var cityID = $('#City_ID').val();
    var city = $('#City_Name').val();
    
    // Validate form:
    if (city.length == 0) {
        alert('City cannot be blank.');
        return;
    }
    
    // Hide save button and display status message to avoid duplicate submission:
    $('#save_city').hide();
    $('#save_city_status').html('Saving...');
    
    // Use AJAX to save the values:
    var url = basePath + '/edit/City/' + encodeURIComponent(cityID);
    $.post(url, {city: city}, function(data) {
        // If save was successful...
        if (data.success) {
            // Close the dialog box.
            if (editBox) {
                editBox.dialog('close');
                editBox.dialog('destroy');
                editBox = false;
            }
            
            // Update the city list.
            redrawCities();
        } else {
            // Save failed -- display error message and restore save button:
            alert('Error: ' + data.msg);
            $('#save_city').show();
            $('#save_city_status').html('');
        }
    }, 'json');
}
