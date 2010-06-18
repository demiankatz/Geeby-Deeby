{foreach from=$materials item=current}
  <a href="#" onclick="editMaterial({$current.Material_Type_ID}); return false;">{$current.Material_Type_Name|escape}</a><br />
{/foreach}
