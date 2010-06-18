<select id="Language_ID">
  {foreach from=$languages item=current}
    <option value="{$current.Language_ID}"{if $current.Language_ID==$selected} selected="selected"{/if}>
      {$current.Language_Name|escape}
    </option>
  {/foreach}
</select>
