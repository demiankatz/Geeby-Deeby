<div id="editForm">
  <input type="hidden" id="Person_ID" value="{$person.Person_ID|escape}" />
  <table class="edit">
    <tr>
      <th>First Name:</th>
      <td><input type="text" id="First_Name" value="{$person.First_Name|escape}" /></td>
    </tr>
    <tr>
      <th>Middle Name:</th>
      <td><input type="text" id="Middle_Name" value="{$person.Middle_Name|escape}" /></td>
    </tr>
    <tr>
      <th>Last Name:</th>
      <td><input type="text" id="Last_Name" value="{$person.Last_Name|escape}" /></td>
    </tr>
    <tr>
      <th>Biography:</th>
      <td><textarea id="Biography">{$person.Biography|escape}</textarea></td>
    </tr>
  </table>
  <button id="save_person" onclick="savePerson();">Save</button>
  <div id="save_person_status"></div>
</div>
