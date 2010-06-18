{foreach from=$series_alt_titles item=current}
  <table class="list_item">
    <tr>
      <td>
        {$current.Series_AltName|escape}{if strlen($current.Note) > 0} ({$current.Note|escape}){/if}
      </td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="deleteAltTitle({$current.Sequence_ID|escape}); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No alternate titles set.
{/foreach}
