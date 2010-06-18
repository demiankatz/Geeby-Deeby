{foreach from=$item_platforms item=current}
  <table class="list_item">
    <tr>
      <td>
        {$current.Platform|escape}
      </td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="deletePlatform({$current.Platform_ID|escape}); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No platforms set.
{/foreach}
