{foreach from=$translatedFrom item=current}
  <table class="list_item">
    <tr>
      <td>
        <a href="?page=edit_series&id={$current.Series_ID|escape:"url"}#translations-tab">
          {$current.Series_Name|escape}
        </a>
      </td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="deleteTranslatedFrom({$current.Series_ID|escape}); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No relevant series.
{/foreach}
