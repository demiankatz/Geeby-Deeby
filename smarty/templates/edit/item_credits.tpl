{if empty($credits)}
  No credits.
{else}
  <table class="list_item">
    <tr><th>Order</th><th colspan="2">Credit</th></tr>
    {foreach from=$credits item=current}
      <tr>
        <td>
          <input class="number" type="text" value="{$current.Position|escape}" id="credit_order{$current.Person_ID|escape}_{$current.Role_ID|escape}" />
          <button onclick="changeCreditOrder({$current.Person_ID|escape}, {$current.Role_ID|escape});">Set</button>
        </td>
        <td>
          <a class="ui-icon ui-icon-trash" href="#" onclick="removeCredit({$current.Person_ID|escape}, {$current.Role_ID|escape}); return false;">
          </a>
        </td>
        <td>
          {$current.Role_Name|escape}:
          <a href="?page=edit_person&id={$current.Person_ID|escape:"url"}">
            {$current.First_Name|escape} {$current.Middle_Name|escape} {$current.Last_Name|escape}
          </a>
          {if $current.Note}
            ({$current.Note|escape})
          {/if}
        </td>
      </tr>
    {/foreach}
  </table>
{/if}