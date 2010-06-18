<h1>Edit Series</h1>
<div class="edit_container" style="width: 800px;">
  {include file="series_edit.tpl"}
</div>
<hr />
<div id="tabs">
  <ul>
    <li><a href="#items-tab"><span>Attached Items</span></a></li>
    <li><a href="#alt-titles-tab"><span>Alternate Titles</span></a></li>
    <li><a href="#categories-tab"><span>Categories</span></a></li>
    <li><a href="#material-tab"><span>Material Types</span></a></li>
    <li><a href="#publishers-tab"><span>Publishers</span></a></li>
    <li><a href="#translations-tab"><span>Translations</span></a></li>
  </ul>
  
  <div id="material-tab" class="edit_tab_contents">
    {include file="material_select.tpl" idPrefix="Series_"} <button onclick="addMaterial();">Add</button>
    <div id="material_list">
      {include file="series_material_list.tpl"}
    </div>
  </div>
  
  <div id="publishers-tab" class="edit_tab_contents">
    <div class="edit_container">
      <table class="edit">
        <tr>
          <th>Publisher:</th>
          <td><input id="Publisher_ID" /></td>
        </tr>
        <tr>
          <th>Country:</th>
          <td>{include file="country_select.tpl"}</td>
        </tr>
        <tr>
          <th>Note:</th>
          <td><input id="Publisher_Note_ID" class="Note_ID" /></td>
        </tr>
        <tr>
          <th>Imprint:</th>
          <td><input type="text" id="Imprint" /></td>
        </tr>
        <tr>
          <td></td>
          <td><button onclick="addPublisher();">Add</button></td>
        </tr>
      </table>
    </div>
    <div id="publisher_list">
      {include file="series_publisher_list.tpl"}
    </div>
  </div>
  
  <div id="alt-titles-tab" class="edit_tab_contents">
    <b>Title:</b> <input type="text" id="Alt_Title" />
    <b>Note: </b> <input id="Alt_Title_Note" class="Note_ID" />
    <button onclick="addAltTitle();">Add</button>
    <div id="alt_title_list">
      {include file="series_alt_titles.tpl"}
    </div>
  </div>
  
  <div id="categories-tab" class="edit_tab_contents">
    {foreach from=$categories item=current}
      <input type="checkbox" id="Category_ID_{$current.Category_ID}" class="Category_ID" value="{$current.Category_ID}" {if in_array($current.Category_ID, $selected_categories)}checked="checked" {/if}/>
      <label for="Category_ID_{$current.Category_ID}">{$current.Category|escape}</label><br />
    {foreachelse}
      No categories defined.
    {/foreach}
    {if count($categories) > 0}
      <button id="save_categories" onclick="saveCategories();">Save</button>
      <div id="save_categories_status"></div>
    {/if}
  </div>
  
  <div id="translations-tab" class="edit_tab_contents">
    <select id="trans_type">
      <option value='from'>Translated From</option>
      <option value='into'>Translated Into</option>
    </select>
    <input id="trans_name" />
    <button onclick="saveTranslation();">Add Translation</button>
    <table>
      <tr>
        <td>
          <b>Translated From:</b>
          <div id="trans_from">
            {include file="series_trans_from.tpl"}
          </div>
        </td>
        <td>
          <b>Translated Into:</b>
          <div id="trans_into">
            {include file="series_trans_into.tpl"}
          </div>
        </td>
      </tr>
    </table>
  </div>
  
  <div id="items-tab" class="edit_tab_contents">
    <input id="item_name" />
    <button onclick="addExistingItem();">Add Existing Item</button>
    <button onclick="addNewItem();">Add New Item</button>
    <div id="item_list">
      {include file="series_item_list.tpl"}
    </div>
  </div>
</div>