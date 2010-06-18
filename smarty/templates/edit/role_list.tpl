{foreach from=$roles item=current}
  <a href="#" onclick="editRole({$current.Role_ID}); return false;">{$current.Role_Name|escape}</a><br />
{/foreach}
