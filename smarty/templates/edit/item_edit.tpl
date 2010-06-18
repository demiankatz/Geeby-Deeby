<div id="editItemForm">
  <input type="hidden" id="Item_ID" value="{$item.Item_ID|escape}" />
  <table class="edit">
    <tr>
      <th>Material Type:</th>
      <td>{include file="material_select.tpl" selected=$item.Material_Type_ID}</td>
    </tr>
    <tr>
      <th>Item Name:</th>
      <td><input type="text" id="Item_Name" value="{$item.Item_Name|escape}" /></td>
    </tr>
    <tr>
      <th>Length:</th>
      <td><input type="text" id="Item_Length" value="{$item.Item_Length|escape}" /></td>
    </tr>
    <tr>
      <th>Endings:</th>
      <td><input type="text" id="Item_Endings" value="{$item.Item_Endings|escape}" /></td>
    </tr>
    <tr>
      <th>Errata:</th>
      <td><textarea id="Item_Errata">{$item.Item_Errata|escape}</textarea></td>
    </tr>
    <tr>
      <th>Thanks:</th>
      <td><input type="text" id="Item_Thanks" value="{$item.Item_Thanks|escape}" /></td>
    </tr>
  </table>
  <button id="save_item" onclick="saveItem();">Save</button>
  <div id="save_item_status"></div>
</div>
