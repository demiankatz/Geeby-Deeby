<div id="editForm">
  <input type="hidden" id="Material_Type_ID" value="{$material.Material_Type_ID|escape}" />
  <table class="edit">
    <tr>
      <th>Material Types:</th>
      <td><input type="text" id="Material_Type_Name" value="{$material.Material_Type_Name|escape}" /></td>
    </tr>
  </table>
  <button id="save_material" onclick="saveMaterial();">Save</button>
  <div id="save_material_status"></div>
</div>
