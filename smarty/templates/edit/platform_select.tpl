<select id="{if !empty($idPrefix)}{$idPrefix}{/if}Platform_ID">
  {foreach from=$platforms item=current}
    <option value="{$current.Platform_ID}"{if $current.Platform_ID==$selected} selected="selected"{/if}>
      {$current.Platform|escape}
    </option>
  {/foreach}
</select>
