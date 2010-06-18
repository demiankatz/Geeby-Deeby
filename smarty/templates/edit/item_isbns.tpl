{foreach from=$ISBNs item=current}
  <table class="list_item">
    <tr>
      <td>
        {if !empty($current.ISBN)}{$current.ISBN|escape} / {/if}{$current.ISBN13|escape}{if strlen($current.Note) > 0} ({$current.Note|escape}){/if}
      </td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="deleteISBN({$current.Sequence_ID|escape}); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No ISBNs set.
{/foreach}
