{foreach from=$translatedFrom item=current}
  <table class="list_item">
    <tr>
      <td>
        <a href="?page=edit_item&id={$current.Item_ID|escape:"url"}#translations-tab">
          {$current.Item_Name|escape}
        </a>
      </td>
      <td>
        <a class="ui-icon ui-icon-trash" href="#" onclick="deleteTranslatedFrom({$current.Item_ID|escape}); return false;">
        </a>
      </td>
    </tr>
  </table>
{foreachelse}
  No relevant items.
{/foreach}
