<div id="editForm">
  <input type="hidden" id="Role_ID" value="{$role.Role_ID|escape}" />
  <table class="edit">
    <tr>
      <th>Role:</th>
      <td><input type="text" id="Role_Name" value="{$role.Role_Name|escape}" /></td>
    </tr>
  </table>
  <button id="save_role" onclick="saveRole();">Save</button>
  <div id="save_role_status"></div>
</div>
