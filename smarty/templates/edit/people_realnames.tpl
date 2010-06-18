{foreach from=$realnames item=current}
  <table class="list_item">
    <tr>
      <td>
        <a href="?page=edit_person&id={$current.Person_ID|escape:"url"}">
          {$current.Last_Name|escape}, {$current.First_Name|escape} {$current.Middle_Name|escape}
        </a>
      </td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="deleteRealName({$current.Person_ID|escape}); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No relevant names.
{/foreach}
