<div id="editForm">
  <input type="hidden" id="Language_ID" value="{$language.Language_ID|escape}" />
  <table class="edit">
    <tr>
      <th>Language:</th>
      <td><input type="text" id="Language_Name" value="{$language.Language_Name|escape}" /></td>
    </tr>
  </table>
  <button id="save_language" onclick="saveLanguage();">Save</button>
  <div id="save_language_status"></div>
</div>
