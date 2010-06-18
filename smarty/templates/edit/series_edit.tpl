<div id="editForm">
  <input type="hidden" id="Series_ID" value="{$series.Series_ID|escape}" />
  <table class="edit">
    <tr>
      <th>Series Name:</th>
      <td><input type="text" id="Series_Name" value="{$series.Series_Name|escape}" /></td>
    </tr>
    <tr>
      <th>Description:</th>
      <td><textarea id="Series_Description">{$series.Series_Description|escape}</textarea></td>
    </tr>
    <tr>
      <th>Language:</th>
      <td>{include file="language_select.tpl" selected=$series.Language_ID}</td>
    </tr>
  </table>
  <button id="save_series" onclick="saveSeries();">Save</button>
  <div id="save_series_status"></div>
</div>
