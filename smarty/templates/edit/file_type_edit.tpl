<div id="editForm">
  <input type="hidden" id="File_Type_ID" value="{$fileType.File_Type_ID|escape}" />
  <table class="edit">
    <tr>
      <th>File Type:</th>
      <td><input type="text" id="File_Type" value="{$fileType.File_Type|escape}" /></td>
    </tr>
  </table>
  <button id="save_file_type" onclick="saveFileType();">Save</button>
  <div id="save_file_type_status"></div>
</div>
