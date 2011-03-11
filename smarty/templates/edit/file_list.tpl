{foreach from=$files item=current}
  <a href="?page=edit_file&id={$current.File_ID|escape:"url"}">{$current.File_Name|escape}</a><br />
{/foreach}
