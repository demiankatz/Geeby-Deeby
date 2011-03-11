<h1>Edit File</h1>
<div class="edit_container">
  {include file="file_edit.tpl"}
</div>
<hr />
<b>Related Items:</b>
<br />
<input id="file_item_id" class="Item_ID" />
<button onclick="linkItem();">Link</button>
<div id="file_item_list">
{include file="file_item_list.tpl"}
</div>
<hr />
<b>Related Series:</b>
<br />
<input id="file_series_id" class="Series_ID" />
<button onclick="linkSeries();">Link</button>
<div id="file_series_list">
{include file="file_series_list.tpl"}
</div>
<hr />
<b>Related People:</b>
<br />
<input id="file_person_id" class="Person_ID" />
<button onclick="linkPerson();">Link</button>
<div id="file_person_list">
{include file="file_person_list.tpl"}
</div>
