{foreach from=$items item=current}
  <a href="?page=edit_item&id={$current.Item_ID|escape:"url"}">{$current.Item_Name|escape}</a><br />
{/foreach}
