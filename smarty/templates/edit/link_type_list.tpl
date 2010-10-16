{foreach from=$linkTypes item=current}
  <a href="#" onclick="editLinkType({$current.Link_Type_ID}); return false;">{$current.Link_Type|escape}</a><br />
{/foreach}
