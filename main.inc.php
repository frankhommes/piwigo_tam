<?php 
/*
Version: 0.7
Plugin Name: Tag Access Management
Plugin URI: http://www.freanki.net/verschiedenes/tag-access-management-plugin-for-piwigo
Author: Freanki
Author URI: http://www.freanki.net/
Description: Plugin for advanced access management via tags
*/


// Chech whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $prefixeTable;

defined('TAM_ID') or define('TAM_ID', basename(dirname(__FILE__)));
define('TAM_PATH' ,   PHPWG_PLUGINS_PATH . TAM_ID . '/');
define('TAM_TABLE',   $prefixeTable . 'tag_access_management');
define('TAM_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . TAM_ID);
define('TAM_PUBLIC',  get_absolute_root_url() . make_index_url(array('section' => 'tam')) . '/');
define('TAM_DIR',     PWG_LOCAL_DIR . 'tam/');
define('TAM_VERSION', '0.7');



if (defined('IN_ADMIN'))
{
  // admin plugins menu link
  add_event_handler('get_admin_plugin_menu_links', 'tam_admin_menu_links');
  
  // file containing all previous handlers functions
  include_once(TAM_PATH . 'include/admin_events.inc.php');
}


// Check access and exit when user status is not ok


// Add an entry to the 'Plugins' menu.
function tag_access_management_admin_menu($menu) {
  array_push(
    $menu,
    array(
      'NAME'  => 'Tag Access Management',
      'URL'   => get_admin_plugin_menu_link(dirname(__FILE__)).'/admin.php'
    )
  );
  return $menu;
}

function tam_check_action($action) {
return($action);
}

function tam_aplying_changes() {
//reading all users from table tag_access_management_users to $tam_tamsers
global $prefixeTable;
  $query = sprintf('SELECT * FROM ' . TAM_TABLE . '_users ;');
  $result = pwg_query($query);
  $tam_tamsers = array(); 
    while ($row = pwg_db_fetch_assoc($result)) {
    $tam_tamsers[] = $row;
    
  }
   

//Searching for all pictures with the right tag_id

foreach($tam_tamsers AS $tam_single_user) {

$tam_category_already_allowed = array();
$tam_category_new = array();
$tam_category_to_change = array();

$tag_id = $tam_single_user['keyword_id'];
$user_id = $tam_single_user['user_id'];

$query = sprintf(
    'SELECT category_id, id_uppercat
    FROM ' . $prefixeTable . 'image_tag, ' . $prefixeTable . 'image_category,' . $prefixeTable . 'categories
    WHERE ' . $prefixeTable . 'image_tag.image_id = ' . $prefixeTable . 'image_category.image_id
    AND ' . $prefixeTable . 'categories.id = ' . $prefixeTable . 'image_category.category_id
    AND tag_id = ' . $tag_id . '
    GROUP BY category_id;
    ;');
      
  $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result)) {
     
     $tam_category_new[] = $row['category_id'];
     $tam_category_new[] = $row['id_uppercat'];
   
  } 
  
  $query = sprintf(
    'SELECT cat_id
    FROM ' . $prefixeTable . 'user_access
    WHERE user_id = '.$user_id.'
    ;');
      
  $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result)) {
     
     $tam_category_already_allowed[] = $row['cat_id'];
   }
 //Get the categories that have to be change
  $tam_category_to_change = array_diff($tam_category_new,$tam_category_already_allowed);
$tam_category_to_change = array_unique ( $tam_category_to_change );
//Now grant access to the categories
$tam_debug = false;
if ($tam_debug) {
echo "<h1>".$tam_single_user['user_id']."</h1>";
echo "<strong>Anzeige aller neuer</strong><br>";
foreach ($tam_category_new AS $key) {
echo "$key <br />";
}
echo "<strong>Anzeige aller bereits gesetzten</strong><br>";
foreach ($tam_category_already_allowed AS $key) {
echo "$key <br />";
}
echo "<strong>Anzeige aller die nun geändert werden sollen</strong><br>";
foreach ($tam_category_to_change AS $key) {
echo "$key <br />";
}
}


foreach ($tam_category_to_change AS $key) {
global $prefixeTable;
if ($key) {
  $query = sprintf(
    'INSERT INTO ' . $prefixeTable . 'user_access (user_id, cat_id)
    VALUES (' . $user_id . ',' . $key . ')
    ;');
      
  $result = pwg_query($query);
  echo $result;

}
}

}
}



?>
