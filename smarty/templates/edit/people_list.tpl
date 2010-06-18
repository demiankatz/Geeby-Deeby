{foreach from=$people item=current}
  <a href="?page=edit_person&id={$current.Person_ID|escape:"url"}">
    {$current.Last_Name|escape}, {$current.First_Name|escape} {$current.Middle_Name|escape}
  </a><br />
{/foreach}
