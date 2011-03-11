<h1>Edit Files</h1>
<table>
  <tr>
    <td>
      <p><b>Files:</b></p>
      <button id="add_file" type="button" onclick="editFile(false);">Add File</button>
      <br /><br />
      <div id="file_list">{include file="file_list.tpl"}</div>
    </td>
    <td>
      <p><b>File Types:</b></p>
      <button id="add_file_type" type="button" onclick="editFileType(false);">Add File Type</button>
      <br /><br />
      <div id="file_type_list">{include file="file_type_list.tpl"}</div>
    </td>
  </tr>
</table>