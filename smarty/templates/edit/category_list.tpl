{foreach from=$categories item=current}
  <a href="#" onclick="editCategory({$current.Category_ID}); return false;">{$current.Category|escape}</a><br />
{/foreach}
