{* Smarty *}
{include file="header.tpl" page_title="Admin - Add Projects" style_sheet="../styles/default.css"}

<body>

 <p>
   Welcome to Homescape Admin Module - Add Projects!
 </p>

 <form action="admin_projects.php?action=ADD" method="post">

  {* form for entering basic project info *}

  <hr>

  {* form for entering the contact info *}

  <select name="contact_old">
    <option value="new">Choose an existing contact</option>
  {section name=c loop=$contacts}
    {if $contact_old == $contacts[c].id}
      <option value="{$contacts[c].id}" selected>{$contacts[c].content}</option>
    {else}
      <option value="{$contacts[c].id}">{$contacts[c].content}</option>
    {/if}
  {/section}
  </select>

  <p>
  -OR-
  </p>

  <table>
   <tr>
    <td>
     Name
    <td>
     <input type="text" name="contact_new[name]" value="{$contact_new.name}">
   <tr>
    <td>
     Street
    <td>
     <input type="text" name="contact_new[street]" value="{$contact_new.street}"></label>
   <tr>
    <td>
     City
    <td>
     <input type="text" name="contact_new[city]" value="{$contact_new.city}"></label>
   <tr>
    <td>
     State
    <td>
     <input type="text" name="contact_new[state]" value="{$contact_new.state}"></label>
   <tr>
    <td>
     Zip Code
    <td>
     <input type="text" name="contact_new[zip]" value="{$contact_new.zip}"></label>
   <tr>
    <td>
     Phone
    <td>
     <input type="text" name="contact_new[phone]" value="{$contact_new.phone}"></label>
  </table>

  <hr>

  {* form for selecting which thumbnails to use *}

  <table>
  {section name=tb loop=$thumbnails}
    <tr>
      <td>
        <label>
          <img src="../media.php?file={$thumbnails[tb].scaled}" width="{$thumbnails[tb].width}" height="{$thumbnails[tb].height}">
          <br>
          {if isset($selected_media) && in_array($thumbnails[tb].original, $selected_media)}
            <input type="checkbox" name="selected_media[]" value="{$thumbnails[tb].original}" checked>
          {else}
            <input type="checkbox" name="selected_media[]" value="{$thumbnails[tb].original}">
          {/if}
          {$thumbnails[tb].original|regex_replace:"/^.*\//":""}
        </label>
      </td>
      <td>
        <label>
          Caption:
          <br>
          <input type="text" name="selected_caption[]" size="50" value="{$selected_caption[tb]}">
          <br>
        </label>
        <label>
          Description:
          <br>
          <textarea name="selected_desc[]" rows="5" cols="50">{$selected_desc[tb]}</textarea>
        </label>
      </td>
    </tr>
  {sectionelse}
    <tr>
      <td>
        No media were found to be added!
      </td>
    </tr>
  {/section}
  </table>

  <input type="submit"> <input type="reset">

 </form>

</body>

{include file="footer.tpl"}
