<div id="editForm">
  <input type="hidden" id="Link_ID" value="{$link.Link_ID|escape}" />
  <table class="edit">
    <tr>
      <th>Link Name:</th>
      <td><input type="text" id="Link_Name" value="{$link.Link_Name|escape}" /></td>
    </tr>
    <tr>
      <th>URL:</th>
      <td><input type="text" id="URL" value="{$link.URL|escape}" /></td>
    </tr>
    <tr>
      <th>Link Type:</th>
      <td>{include file="link_type_select.tpl" selected=$link.Link_Type_ID}</td>
    </tr>
    <tr>
      <th>Date Checked<br/>(YYYY-MM-DD):</th>
      <td><input type="text" id="Date_Checked" value="{$link.Date_Checked|escape}" /></td>
    </tr>
    <tr>
      <th>Description:</th>
      <td><textarea id="Description">{$link.Description|escape}</textarea></td>
    </tr>
  </table>
  <button id="save_link" onclick="saveLink();">Save</button>
  <div id="save_link_status"></div>
</div>
