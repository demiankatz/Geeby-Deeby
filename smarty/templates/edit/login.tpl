<h1>Please log in.</h1>
{if $msg}
  <div class="error">{$msg|escape}</div>
{/if}
<form method="post">
  <table>
    <tr>
      <th>Username:</th>
      <td><input type="text" name="user"/></td>
    </tr>
    <tr>
      <th>Password:</th>
      <td><input type="password" name="pass"/></td>
    </tr>
    <tr>
      <th>&nbsp;</th>
      <td><input type="submit" value="Log In"/></td>
    </tr>
  </table>
</form>