<div id="editForm">
  <input type="hidden" id="Publisher_ID" value="{$publisher.Publisher_ID|escape}" />
  <table class="edit">
    <tr>
      <th>Publisher:</th>
      <td><input type="text" id="Publisher_Name" value="{$publisher.Publisher_Name|escape}" /></td>
    </tr>
  </table>
  <button id="save_publisher" onclick="savePublisher();">Save</button>
  <div id="save_publisher_status"></div>
</div>
