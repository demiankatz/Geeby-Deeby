{if empty($images)}
  No images.
{else}
  <table class="list_item">
    <tr><th>Order</th><th colspan="2">Image</th><th>Details</th></tr>
    {foreach from=$images item=current}
      <tr>
        <td>
          <input class="number" type="text" value="{$current.Position|escape}" id="image_order{$current.Sequence_ID|escape}" />
          <button onclick="changeImageOrder({$current.Sequence_ID|escape});">Set</button>
        </td>
        <td>
          <a class="ui-icon ui-icon-trash" href="#" onclick="removeImage({$current.Sequence_ID|escape}); return false;">
          </a>
        </td>
        <td>
          <a target="_imagePopup" href="{$current.Image_Path|escape}">
            <img src="{$current.Thumb_Path|escape}" />
          </a>
        </td>
        <td>
          <b>Image Path:</b> {$current.Image_Path|escape}<br />
          <b>Thumb Path:</b> {$current.Thumb_Path|escape}<br />
          {if $current.Note}
            <b>Note:</b> {$current.Note|escape}
          {/if}
        </td>
      </tr>
    {/foreach}
  </table>
{/if}