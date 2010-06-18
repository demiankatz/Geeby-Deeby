{foreach from=$series_publishers item=current}
  <table class="list_item">
    <tr>
      <td>
        {$current.Publisher_Name|escape}
        ({if strlen($current.Imprint) > 0}{$current.Imprint}: {/if}{$current.Country_Name|escape}{if strlen($current.Note) > 0} - {$current.Note|escape}{/if})
      </td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="deletePublisher({$current.Series_Publisher_ID|escape}); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No publishers set.
{/foreach}
