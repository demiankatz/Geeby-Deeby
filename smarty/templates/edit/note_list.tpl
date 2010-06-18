{foreach from=$notes item=current}
  <a href="#" onclick="editNote({$current.Note_ID}); return false;">{$current.Note|escape}</a><br />
{/foreach}
