{foreach from=$releaseDates item=current}
  <table class="list_item">
    <tr>
      <td>
        {$current|@formatReleaseDate}
      </td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="deleteReleaseDate({$current.Year|escape}, {$current.Month|escape}, {$current.Day|escape}); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No release dates set.
{/foreach}
