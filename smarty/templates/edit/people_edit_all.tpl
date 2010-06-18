<h1>Edit Person</h1>
<div class="edit_container">
  {include file="people_edit.tpl"}
</div>
<hr />
<div>
<select id="pseudo_type">
  <option value='realname'>Pseudonym For</option>
  <option value='pseudonym'>Pseudonym:</option>
</select>
<input id="pseudo_name" />
<button onclick="saveRelationship();">Add Relationship</button>
</div>
<table>
  <tr>
    <td>
      <b>Pseudonym For:</b>
      <div id="realname_list">
        {include file="people_realnames.tpl"}
      </div>
    </td>
    <td>
      <b>Pseudonym(s):</b>
      <div id="pseudonym_list">
        {include file="people_pseudonyms.tpl"}
      </div>
    </td>
  </tr>
</table>
