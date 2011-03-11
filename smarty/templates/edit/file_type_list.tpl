{foreach from=$fileTypes item=current}
  <a href="#" onclick="editFileType({$current.File_Type_ID}); return false;">{$current.File_Type|escape}</a><br />
{/foreach}
