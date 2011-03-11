<select id="{if !empty($idPrefix)}{$idPrefix}{/if}File_Type_ID">
  {foreach from=$fileTypes item=current}
    <option value="{$current.File_Type_ID}"{if $current.File_Type_ID==$selected} selected="selected"{/if}>
      {$current.File_Type|escape}
    </option>
  {/foreach}
</select>
