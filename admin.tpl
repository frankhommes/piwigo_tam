<!-- Show the title of the plugin -->
<div class="titlePage">
  <h2>{'tag access management plugin'|@translate}</h2>
</div>
  {if $tam_action eq "edit"}
  <span style="color:red">Modus: edit</span><br />
  <!-- editing -->
     
  <script src="{$tam_path}jquery.min.js"></script>
  {literal}
<script>
    jQuery.fn.filterByText = function(textbox, selectSingleMatch) {
        return this.each(function() {
            var select = this;
            var options = [];
            $(select).find('option').each(function() {
                options.push({value: $(this).val(), text: $(this).text()});
            });
            $(select).data('options', options);
            $(textbox).bind('change keyup', function() {
                var options = $(select).empty().data('options');
                var search = $.trim($(this).val());
                var regex = new RegExp(search,"gi");
              
                $.each(options, function(i) {
                    var option = options[i];
                    if(option.text.match(regex) !== null) {
                        $(select).append(
                           $('<option>').text(option.text).val(option.value)
                        );
                    }
                });
                if (selectSingleMatch === true && $(select).children().length === 1) {
                    $(select).children().get(0).selected = true;
                }
            });            
        });
    };

    $(function() {
        $('#select').filterByText($('#textbox'), true);
    });  
</script>

{/literal}

  
  
  <!-- Für jeden Piwigo-user als $user: !-->
   {foreach from=$tam_users item=user}
      
      
  {if $tam_post_piwigo_user_id eq $user.id}
  <center><strong><big>User: {$user.username}</center></strong></big>
  <table> 
  <tr><td width="200"><strong>Linked Tags</strong></td><td width="50"><strong>Modify</strong></td></tr>
  

   
   {foreach from=$tam_databaseentries item=entries}
        {if $entries.user_id eq $user.id}
  <form action="" method="post" name="form"><tr><td> {foreach from=$tam_tags item=tag} 
{if $tag.id eq $entries.keyword_id }
{$tag.name}
{/if}
{/foreach} </td>
<td align="center" width="50">
<input type="hidden" name="tam_post_id" value="{$entries.id}">
<input type="hidden" name="tam_post_piwigo_user_id" value="{$tam_post_piwigo_user_id}">
<input type="hidden" name="action" value="delete">
<input class="submit" type="submit" name="submit" value="delete"/></form></td></tr>
{/if}
{/foreach}
<tr><td></td></tr>
<tr><td><strong>Adding a new tag to this user:</strong></td></tr>
<form action="" method="post" name="form"><tr><td><select size="10" id ="select" multiple name="tag_id"> <option value="none" selected>none</option> {foreach from=$tam_tags item=tag} <option value="{$tag.id}"
>{$tag.name}</option>{/foreach}

</select></td>
<td align="center" width="50">
<input type="hidden" name="tam_post_piwigo_user_id" value="{$tam_post_piwigo_user_id}">
<input type="hidden" name="action" value="add">
<input class="submit" type="submit" name="submit" value="add"/></form></td> </tr>
<tr><td>Filter: <input id="textbox" type="text" /></td></tr>

  {/if}
  {/foreach}
  </table>
  
  <!-- finished editing -->
  
  {else}
  
  
  <!-- Showing all users -->
  
  
  <table> 
  <tr><td width="200"><strong>Username</strong></td><td width="200"><strong>Linked Tag</strong></td><td width="50"><strong>Modify</strong></td></tr>
  
 {foreach from=$tam_users item=user}
  <form action="" method="post" name="form">

  {if isset($user.associated_tag) && $user.associated_tag != ''}
  <input type="hidden" name="tam_post_id" value="{$user.tam_id}">
  {/if}
  <input type="hidden" name="tam_post_piwigo_user_id" value="{$user.id}">

  <tr>
    <td align="left">{$user.username}</td>
    
    
    {if isset($user.associated_tag) && $user.associated_tag != ''}
    <td align="left" width="100">{$user.associated_tag}</td>
    {else}
    <td align="left" width="100">No associated tag</td>
    {/if}
  
  <td align="center" width="50"><input type="hidden" name="action" value="edit"><input class="submit" type="submit" name="submit" value="edit"/></td>

  </tr></form> {/foreach} </table>
  

<form action="" method="post" name="form"> <input type="hidden" name="action" value="apply">
  <br /> <p>
  <input class="submit" type="submit" name="submit" value="apply settings"/>
  </p>
  

</form>

* Linked tags may contain more then one keyword. In this case only the recently added keyword will be shown.
{/if}
