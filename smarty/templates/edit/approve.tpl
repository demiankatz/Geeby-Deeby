<h1>Approve New Users / Content</h1>
<h2>New Users</h2>
<table>
  <tr>
    <th>Username</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Person Record</th>
    <th>Options</th>
  </tr>
  {foreach from=$newUsers item=current}
    <tr id="NewUser_{$current.User_ID|escape}">
      <td><input type="text" id="Username_{$current.User_ID|escape}" value="{$current.Username|escape}"/></td>
      <td><input type="text" id="Name_{$current.User_ID|escape}" value="{$current.Name|escape}"/></td>
      <td><input type="text" id="Address_{$current.User_ID|escape}" value="{$current.Address|escape}"/></td>
      <td><input class="Person_ID" id="Person_ID_{$current.User_ID|escape}" value="-1 (normal user)"/></td>
      <td id="UserButtons_{$current.User_ID|escape}">
        <button onclick="approveUser({$current.User_ID|escape});">Approve</button>
        <button onclick="rejectUser({$current.User_ID|escape});">Reject<button>
      </td>
    </tr>
  {/foreach}
</table>
