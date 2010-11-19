<h1>{$siteTitle|escape} - Administration</h1>

{if $contentEditor}
  <h2>Edit Data</h2>
  <ul>
    <li><a target='_categories' href='?page=categories'>Categories</a></li>
    <li><a target='_countries' href='?page=countries'>Countries</a></li>
    <li>FAQs (not implemented yet)</li>
    <li>Files (not implemented yet)</li>
    <li><a target='_materials' href='?page=languages'>Languages</a></li>
    <li><a target='_links' href='?page=links'>Links</a></li>
    <li><a target='_materials' href='?page=materials'>Material Types</a></li>
    <li><a target='_notes' href='?page=notes'>Notes</a></li>
    <li><a target='_people' href='?page=people'>People</a></li>
    <li><a target='_platforms' href='?page=platforms'>Platforms</a></li>
    <li><a target='_publishers' href='?page=publishers'>Publishers</a></li>
    <li><a target='_series' href='?page=series'>Series</a></li>
  </ul>
{/if}

{if $approver || $userEditor}
  <h2>Extra Tools</h2>
  <ul>
    {if $approver}
      <li><a target='_approve' href='?page=approve'>Approve Users / Content Submissions</a></li>
    {/if}
    {if $userEditor}
      <li>Edit Users (not implemented yet)</li>
    {/if}
  </ul>
{/if}

{if !$contentEditor && !$approver && !$userEditor}
  <p>You do not have permission to access this page.</p>
{/if}