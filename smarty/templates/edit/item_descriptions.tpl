{foreach from=$descriptions item=current}
  <table class="list_item">
    <tr>
      <td>
        {* Note: Description is not escaped since it may contain HTML! *}
        {$current.Description} (Source: {$current.Source_Description|escape})
      </td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="deleteDescription(&quot;{$current.Source|escape}&quot;); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No descriptions set.
{/foreach}
