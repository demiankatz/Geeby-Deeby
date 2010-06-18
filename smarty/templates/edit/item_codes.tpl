{foreach from=$productCodes item=current}
  <table class="list_item">
    <tr>
      <td>
        {$current.Product_Code|escape}{if strlen($current.Note) > 0} ({$current.Note|escape}){/if}
      </td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="deleteProductCode({$current.Sequence_ID|escape}); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No product codes set.
{/foreach}
