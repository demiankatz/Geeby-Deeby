<select id="Role_ID">
  {foreach from=$roles item=current}
    <option value="{$current.Role_ID}"{if $current.Role_ID==$selected} selected="selected"{/if}>
      {$current.Role_Name|escape}
    </option>
  {/foreach}
</select>
