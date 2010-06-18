{foreach from=$countries item=current}
  <a href="#" onclick="editCountry({$current.Country_ID}); return false;">{$current.Country_Name|escape}</a><br />
{/foreach}
