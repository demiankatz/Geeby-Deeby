<h1>Edit Link</h1>
<div class="edit_container">
  {include file="link_edit.tpl"}
</div>
<hr />
<b>Related Items:</b>
<br />
<input id="link_item_id" class="Item_ID" />
<button onclick="linkItem();">Link</button>
<div id="link_item_list">
{include file="link_item_list.tpl"}
</div>
<hr />
<b>Related Series:</b>
<br />
<input id="link_series_id" class="Series_ID" />
<button onclick="linkSeries();">Link</button>
<div id="link_series_list">
{include file="link_series_list.tpl"}
</div>
<hr />
<b>Related People:</b>
<br />
<input id="link_person_id" class="Person_ID" />
<button onclick="linkPerson();">Link</button>
<div id="link_person_list">
{include file="link_person_list.tpl"}
</div>
