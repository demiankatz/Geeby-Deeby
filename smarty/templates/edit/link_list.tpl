{foreach from=$links item=current}
  <a href="?page=edit_link&id={$current.Link_ID|escape:"url"}">{$current.Link_Name|escape}</a><br />
{/foreach}
