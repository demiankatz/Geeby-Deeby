<h1>Edit Links</h1>
<table>
  <tr>
    <td>
      <p><b>Links:</b></p>
      <button id="add_link" type="button" onclick="editLink(false);">Add Link</button>
      <br /><br />
      <div id="link_list">{include file="link_list.tpl"}</div>
    </td>
    <td>
      <p><b>Link Types:</b></p>
      <button id="add_link_type" type="button" onclick="editLinkType(false);">Add Link Type</button>
      <br /><br />
      <div id="link_type_list">{include file="link_type_list.tpl"}</div>
    </td>
  </tr>
</table>