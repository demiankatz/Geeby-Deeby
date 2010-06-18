{if empty($item_list)}
  No items.
{else}
  <table class="list_item">
    <tr><th>Order</th><th colspan="2">Title / Material Type</th></tr>
    {foreach from=$item_list item=current}
      <tr>
        <td>
          <input class="number" type="text" value="{$current.Position|escape}" id="order{$current.Item_ID}" />
          <button onclick="changeSeriesOrder({$current.Item_ID|escape});">Set</button>
        </td>
        <td>
          <a class="ui-icon ui-icon-trash" href="#" onclick="removeFromSeries({$current.Item_ID|escape}); return false;">
          </a>
        </td>
        <td>
          <a href="?page=edit_item&id={$current.Item_ID|escape:"url"}">
            {$current.Item_Name|escape}
          </a>
          ({$current.Material_Type_Name|escape})
        </td>
      </tr>
    {/foreach}
  </table>
{/if}