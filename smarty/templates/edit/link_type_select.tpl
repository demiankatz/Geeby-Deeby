<select id="{if !empty($idPrefix)}{$idPrefix}{/if}Link_Type_ID">
  {foreach from=$linkTypes item=current}
    <option value="{$current.Link_Type_ID}"{if $current.Link_Type_ID==$selected} selected="selected"{/if}>
      {$current.Link_Type|escape}
    </option>
  {/foreach}
</select>
