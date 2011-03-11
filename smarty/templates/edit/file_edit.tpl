<div id="editForm">
  <input type="hidden" id="File_ID" value="{$file.File_ID|escape}" />
  <table class="edit">
    <tr>
      <th>File Name:</th>
      <td><input type="text" id="File_Name" value="{$file.File_Name|escape}" /></td>
    </tr>
    <tr>
      <th>File Path:</th>
      <td><input type="text" id="File_Path" value="{$file.File_Path|escape}" /></td>
    </tr>
    <tr>
      <th>File Type:</th>
      <td>{include file="file_type_select.tpl" selected=$file.File_Type_ID}</td>
    </tr>
    <tr>
      <th>Description:</th>
      <td><textarea id="Description">{$file.Description|escape}</textarea></td>
    </tr>
  </table>
  <button id="save_file" onclick="saveFile();">Save</button>
  <div id="save_file_status"></div>
</div>
