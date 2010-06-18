<div id="editForm">
  <input type="hidden" id="Category_ID" value="{$category.Category_ID|escape}" />
  <table class="edit">
    <tr>
      <th>Category:</th>
      <td><input type="text" id="Category" value="{$category.Category|escape}" /></td>
    </tr>
    <tr>
      <th>Description:</th>
      <td><textarea id="Description">{$category.Description|escape}</textarea></td>
    </tr>
  </table>
  <button id="save_category" onclick="saveCategory();">Save</button>
  <div id="save_category_status"></div>
</div>
