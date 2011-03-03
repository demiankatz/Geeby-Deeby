<h1>Approve New Users / Content</h1>
<h2>New Users</h2>
<table>
  <tr>
    <th>Username</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Person Record</th>
    <th>Options</th>
  </tr>
  {foreach from=$newUsers item=current}
    <tr id="NewUser_{$current.User_ID|escape}">
      <td><input type="text" id="Username_{$current.User_ID|escape}" value="{$current.Username|escape}"/></td>
      <td><input type="text" id="Name_{$current.User_ID|escape}" value="{$current.Name|escape}"/></td>
      <td><input type="text" id="Address_{$current.User_ID|escape}" value="{$current.Address|escape}"/></td>
      <td><input class="Person_ID" id="Person_ID_{$current.User_ID|escape}" value="-1 (normal user)"/></td>
      <td id="UserButtons_{$current.User_ID|escape}">
        <button onclick="approveUser({$current.User_ID|escape});">Approve</button>
        <button onclick="rejectUser({$current.User_ID|escape});">Reject<button>
      </td>
    </tr>
  {/foreach}
</table>
<h2>New Item Reviews</h2>
<table class="approve">
  <tr>
    <th>Item / User</th>
    <th>Review</th>
    <th>Options</th>
  </tr>
  {foreach from=$pendingReviews item=current}
    <tr id="PendingReview_{$current.User_ID|escape}_{$current.Item_ID|escape}">
      <td>{$current.Item_Name|escape}<br />{$current.Username|escape}</td>
      <td>
        <textarea id="ReviewText_{$current.User_ID|escape}_{$current.Item_ID|escape}">{$current.Review|escape}</textarea>
      </td>
      <td id="ReviewButtons_{$current.User_ID|escape}_{$current.Item_ID|escape}">
        <button onclick="approveReview({$current.User_ID|escape}, {$current.Item_ID|escape});">Approve</button>
        <button onclick="rejectReview({$current.User_ID|escape}, {$current.Item_ID|escape});">Reject</button>
      </td>
    </tr>
  {/foreach}
</table>
<h2>New Series Comments</h2>
<table class="approve">
  <tr>
    <th>Series / User</th>
    <th>Comment</th>
    <th>Options</th>
  </tr>
  {foreach from=$pendingComments item=current}
    <tr id="PendingComment_{$current.User_ID|escape}_{$current.Series_ID|escape}">
      <td>{$current.Series_Name|escape}<br />{$current.Username|escape}</td>
      <td>
        <textarea id="CommentText_{$current.User_ID|escape}_{$current.Series_ID|escape}">{$current.Review|escape}</textarea>
      </td>
      <td id="CommentButtons_{$current.User_ID|escape}_{$current.Series_ID|escape}">
        <button onclick="approveComment({$current.User_ID|escape}, {$current.Series_ID|escape});">Approve</button>
        <button onclick="rejectComment({$current.User_ID|escape}, {$current.Series_ID|escape});">Reject</button>
      </td>
    </tr>
  {/foreach}
</table>