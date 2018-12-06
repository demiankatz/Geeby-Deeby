/**
 * Register an autocomplete on elements matching the selector to retrieve the
 * specified type of objects.
 */
function registerAutocomplete(selector, type) {
  $(selector).autocomplete({
    source: function(request, response) {
      $.ajax({
        url: basePath + "/Suggest/" + type + "?q=" + request.term, 
        success: function(data) {
          response(data.split('\n').slice(0, -1));
        }
      });
    }
  });
}