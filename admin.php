<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+



// Chech whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// Fetch the template.
global $template,$prefixeTable;

//Reading tag_access_management Users
  $query = sprintf('SELECT * FROM ' . $prefixeTable . 'tag_access_management_users;');
  $result = pwg_query($query);
  $tam_databaseentries = array(); 
    while ($row = pwg_db_fetch_assoc($result)) {
    $tam_databaseentries[] = $row;
    
  }
  $template->assign('tam_databaseentries', $tam_databaseentries);
  
  
  
  

$tam_bugtracing = "1";
//Definition of $tam_action
if (isset($_POST['action'])) $tam_action = tam_check_action($_POST['action']); else $tam_action = "0";

//Checking if changes should be applied on database
 if ($tam_action == "delete") {
 $query = sprintf(
    'DELETE FROM ' . $prefixeTable . 'tag_access_management_users
     WHERE id = ' . $_POST['tam_post_id'] . ' AND user_id = ' . $_POST['tam_post_piwigo_user_id'] . '
    ;');
     if ($tam_bugtracing == "1") echo "Tag id: ".$_POST['tam_post_id']." von User ".$_POST['tam_post_piwigo_user_id']."erfolgreich gelöscht!";
      
  $result = pwg_query($query);
 }

if ($tam_action == "apply") {

if ($tam_bugtracing == "1") echo "changes should be applied on database<br />";
//Reading all associations
if ($tam_bugtracing == "1") echo "Aplying changes... ";
tam_aplying_changes();
if ($tam_bugtracing == "1") echo "done.<br />";
}

if (isset($_POST['submit']))
{
if ($tam_bugtracing == "1") echo "submit ($tam_action)<br />";   
  
    if ($tam_action == "add") {
      $query = sprintf(
    'INSERT INTO ' . $prefixeTable . 'tag_access_management_users (user_id, keyword_id)
    VALUES (' . $_POST['tam_post_piwigo_user_id'] . ',' . $_POST['tag_id'] . ')
    ;');
      
  $result = pwg_query($query); 
    }
  
  if ($tam_action == "save") {
  if ($tam_bugtracing == "1") echo "save";   
  //echo "save tag" . $_POST['tag_id'] . "für userid " .  $_POST['user_id'];
 
  
  if ($_POST['tam_post_id']) {
   $query = sprintf(
    'SELECT *
    FROM ' . $prefixeTable . 'tag_access_management_users
    WHERE id = ' . $_POST['tam_post_id'] . '
    ;'); }
    
    elseif ($_POST['tam_post_piwigo_user_id']) {
    $query = sprintf(
    'SELECT *
    FROM ' . $prefixeTable . 'tag_access_management_users
    WHERE user_id = ' . $_POST['tam_post_piwigo_user_id'] . '
    ;'); }
   
  
  $result = pwg_query($query);
  $row = pwg_db_fetch_assoc($result);
  
  if ($row) {
    //user already has an associated tag which should be altered
   $query = sprintf(
    'UPDATE ' . $prefixeTable . 'tag_access_management_users
    SET keyword_id = ' . $_POST['tag_id'] . '
    WHERE id = ' . $_POST['tam_post_id'] . '
    ;');
      
  $result = pwg_query($query);
//  echo "update";
  
  }
  else {
  
     
    $query = sprintf(
    'INSERT INTO ' . $prefixeTable . 'tag_access_management_users (user_id, keyword_id)
    VALUES (' . $_POST['tam_post_piwigo_user_id'] . ',' . $_POST['tag_id'] . ')
    ;');
      
  $result = pwg_query($query); 
    
//  echo "neu erstellt";
  
  }
}
else
{
if (isset($_POST['tam_post_id'])) {
$template->assign('tam_post_id', $_POST['tam_post_id']);}

if (isset($_POST['tam_post_piwigo_user_id'])) {
$template->assign('tam_post_piwigo_user_id', $_POST['tam_post_piwigo_user_id']);}




}}

//Get saved information about associations from users and tags
    $query = sprintf(
    'SELECT *
    FROM ' . $prefixeTable . 'tag_access_management_users
    ;');
  
  $result = pwg_query($query);
$tam_tamsers = array(); 
    while ($row = pwg_db_fetch_assoc($result)) {
    $tam_tamsers[] = $row;
    
  }

//Get all the tags from the database
  
  $query = sprintf(
    'SELECT `id`,`name`
    FROM ' . $prefixeTable . 'tags
    ;');
  
  $result = pwg_query($query);

    while ($row = pwg_db_fetch_assoc($result)) {
    $tam_tagsfetch[] = $row;
  }
  
  
$template->assign('tam_tags', $tam_tagsfetch);
$template->assign('tam_path', TAM_PATH);



//Get all users from the database
 $query = sprintf(
    'SELECT `id`,`username`	
    FROM ' . $prefixeTable . 'users
    ;');
  
  $user_database_piwigo = pwg_query($query);
    //For every user in piwigo database
    while ($tam_piwigo_user_info = pwg_db_fetch_assoc($user_database_piwigo)) {
    
    //For all entries in tag_access_management database named $tam_user
    foreach ($tam_tamsers as $tam_user) { 
    //For every user in tag_access_management_users there will be a $key array compared with the id from the user database for eqality
    if ($tam_user['user_id'] == $tam_piwigo_user_info['id']) {
    //
    //echo $tam_user['user_id']." == ".$tam_piwigo_user_info['id']."<br />";
    
    foreach ($tam_tagsfetch as $tam_tag) { 
    if ($tam_user['keyword_id'] == $tam_tag['id']) {
    $tam_piwigo_user_info["associated_tag"] = $tam_tag['name'];
    $tam_piwigo_user_info["piwigo_user_id"] = $tam_user['user_id'];
    $tam_piwigo_user_info["keyword_id"] = $tam_user['keyword_id'];
    $tam_piwigo_user_info["tam_id"] = $tam_user['id'];
    //echo "accosicated_tag = ".$tam_piwigo_user_info["associated_tag"];
    //echo " piwigo_user_id = ".$tam_piwigo_user_info["piwigo_user_id"];
    //echo " keyword_id = ".$tam_piwigo_user_info["keyword_id"];
    //echo " tam_id = ".$tam_piwigo_user_info["tam_id"];
    //echo "<br />";
    
    }
    
    }
    if (!isset($tam_piwigo_user_info["associated_tag"])) {
    $tam_piwigo_user_info["associated_tag"] = '';
}
    
    }}
    
    
    $tam_userfetch[] = $tam_piwigo_user_info;
    
  }
  
$template->assign('tam_users', $tam_userfetch);
$template->assign('tam_action', $tam_action);







// Add our template to the global template
$template->set_filenames(
  array(
    'plugin_admin_content' => dirname(__FILE__).'/admin.tpl'
  )         
);

// Assign the template contents to ADMIN_CONTENT
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');



?>
