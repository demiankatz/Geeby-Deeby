<div id="editForm">
  <input type="hidden" id="Country_ID" value="{$country.Country_ID|escape}" />
  <table class="edit">
    <tr>
      <th>Country:</th>
      <td><input type="text" id="Country_Name" value="{$country.Country_Name|escape}" /></td>
    </tr>
  </table>
  <button id="save_country" onclick="saveCountry();">Save</button>
  <div id="save_country_status"></div>
</div>
