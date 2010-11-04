<!DOCTYPE html>
<html>
  <head>
    {foreach from=$css item=current}
      <link rel="stylesheet" type="text/css" href="../css/{$current}" />
    {/foreach}
    {foreach from=$js item=current}
      <script language="JavaScript" type="text/javascript" src="../js/{$current}"></script>
    {/foreach}
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
    <title>
      {if $pageTitle}{$pageTitle|escape} - {/if}{$siteTitle|escape}
    </title>
  </head>
  <body>
    <div class="headerControls">
      {if $loggedIn}<a href="?page=logout">Log Out</a>{/if}
    </div>
    {include file=$subPage}
    <div class="footer">
      <p>
        <a href="/">{$siteTitle|escape}</a>
        (c) 1998-{$smarty.now|date_format:'%Y'} Demian Katz
      </p>
      <p class="fineprint">
        Individual reviews are the property of their authors.<br />
        Trademarks and graphics remain the property of their respective owners and
        are used here solely for the educational purpose of documenting the history
        and scope of interactive storytelling. No infringement is intended. If you
        have any questions or complaints, please contact <a 
        href="mailto:{$siteEmail|escape}">{$siteEmail|escape}</a>.
      </p>
    </div>
  </body>
</html>