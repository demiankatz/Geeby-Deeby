{foreach from=$series item=current}
  <a href="?page=edit_series&id={$current.Series_ID|escape:"url"}">{$current.Series_Name|escape}</a><br />
{/foreach}
