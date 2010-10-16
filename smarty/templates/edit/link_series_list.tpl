{foreach from=$seriesLinks item=current}
  <table class="list_item">
    <tr>
      <td>
        <a href="?page=edit_series&id={$current.Series_ID|escape:"url"}">{$current.Series_Name|escape}</a><br />
      </td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="unlinkSeries({$current.Series_ID|escape}); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No relevant series.
{/foreach}
