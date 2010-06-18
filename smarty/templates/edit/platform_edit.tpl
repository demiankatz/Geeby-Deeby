<div id="editForm">
  <input type="hidden" id="Platform_ID" value="{$platform.Platform_ID|escape}" />
  <table class="edit">
    <tr>
      <th>Platform:</th>
      <td><input type="text" id="Platform" value="{$platform.Platform|escape}" /></td>
    </tr>
  </table>
  <button id="save_platform" onclick="savePlatform();">Save</button>
  <div id="save_platform_status"></div>
</div>
