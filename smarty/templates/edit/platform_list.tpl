{foreach from=$platforms item=current}
  <a href="#" onclick="editPlatform({$current.Platform_ID}); return false;">{$current.Platform|escape}</a><br />
{/foreach}
