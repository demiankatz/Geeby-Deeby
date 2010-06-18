<div id="editForm">
  <input type="hidden" id="Note_ID" value="{$note.Note_ID|escape}" />
  <table class="edit">
    <tr>
      <th>Note:</th>
      <td><input type="text" id="Note" value="{$note.Note|escape}" /></td>
    </tr>
  </table>
  <button id="save_note" onclick="saveNote();">Save</button>
  <div id="save_note_status"></div>
</div>
