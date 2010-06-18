<select id="{if !empty($idPrefix)}{$idPrefix}{/if}Material_Type_ID">
  {foreach from=$materials item=current}
    <option value="{$current.Material_Type_ID}"{if $current.Material_Type_ID==$selected} selected="selected"{/if}>
      {$current.Material_Type_Name|escape}
    </option>
  {/foreach}
</select>
