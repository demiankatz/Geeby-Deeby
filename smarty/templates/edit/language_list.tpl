{foreach from=$languages item=current}
  <a href="#" onclick="editLanguage({$current.Language_ID}); return false;">{$current.Language_Name|escape}</a><br />
{/foreach}
