<select id="Country_ID">
  {foreach from=$countries item=current}
    <option value="{$current.Country_ID}"{if $current.Country_ID==$selected} selected="selected"{/if}>
      {$current.Country_Name|escape}
    </option>
  {/foreach}
</select>
