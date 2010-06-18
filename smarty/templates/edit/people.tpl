<h1>Edit People</h1>
<table>
  <tr>
    <td>
      <p><b>People:</b></p>
      <button id="add_person" type="button" onclick="editPerson(false);">Add Person</button>
      <br /><br />
      <div id="people_list">{include file="people_list.tpl"}</div>
    </td>
    <td>
      <p><b>Roles:</b></p>
      <button id="add_role" type="button" onclick="editRole(false);">Add Role</button>
      <br /><br />
      <div id="role_list">{include file="role_list.tpl"}</div>
    </td>
  </tr>
</table>