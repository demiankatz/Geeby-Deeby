{foreach from=$series_materials item=current}
  <table class="list_item">
    <tr>
      <td>{$current.Material_Type_Name|escape}</td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="deleteMaterial({$current.Material_Type_ID|escape}); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No material types set.
{/foreach}
