{foreach from=$publishers item=current}
  <a href="#" onclick="editPublisher({$current.Publisher_ID}); return false;">{$current.Publisher_Name|escape}</a><br />
{/foreach}
