/**
 * Register an autocomplete on elements matching the selector to retrieve the
 * specified type of objects.
 */
function registerAutocomplete(selector, type) {
  var ac = new Autocomplete({ limit: 10000 });
  document.querySelectorAll(selector).forEach(function bindAC(input) {
    ac(input, function achandler(query, callback) {
      $.ajax({
        url: basePath + "/Suggest/" + type + "?q=" + query,
        success: function(data) {
          callback(data.split('\n').slice(0, -1));
        }
      });
    });
  });
}
