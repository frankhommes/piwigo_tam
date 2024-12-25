<?php 
/*
Version: 0.8a
Plugin Name: Tag Access Management
Plugin URI: https://github.com/frankhommes/piwigo_tam
Author: Frank Hommes
Author URI: http://www.freanki.net/
Description: Plugin for advanced access management via tags.

This plugin grants access to albums based on tags. If a tag appears at least once in an album, 
the user associated with that tag is granted access to the album.
It scans images associated with specific tags and automatically grants users access 
to the corresponding categories.

Features:
- Automatic assignment of categories based on tags.
- Support for multiple users and tag combinations.
- Fully integrated with the Piwigo database structure.
- Debugging options for easier troubleshooting.

Requirements:
- Piwigo version 11.0 or later.
- Database write access to the Piwigo tables.

Notes:
- Ensure that tag assignments are accurate before applying changes.
- This plugin has been tested for compatibility with the latest versions of Piwigo.
- The Plugin has to be run after adding new pictures
*/

// Check whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) {
    die('Hacking attempt!');
}

// Global table prefix for the plugin
global $prefixeTable;

// Define constants for the plugin
defined('TAM_ID') or define('TAM_ID', basename(dirname(__FILE__)));
define('TAM_PATH', PHPWG_PLUGINS_PATH . TAM_ID . '/');
define('TAM_TABLE', $prefixeTable . 'tag_access_management');
define('TAM_ADMIN', get_root_url() . 'admin.php?page=plugin-' . TAM_ID);
define('TAM_PUBLIC', get_absolute_root_url() . make_index_url(array('section' => 'tam')) . '/');
define('TAM_DIR', PWG_LOCAL_DIR . 'tam/');
define('TAM_VERSION', '0.8a');

// Initialize admin-specific actions if in the admin area
if (defined('IN_ADMIN')) {
    // Add event handler to manage admin menu links
    add_event_handler('get_admin_plugin_menu_links', 'tam_admin_menu_links');
    
    // Include the file for all predefined admin events
    require_once(TAM_PATH . 'include/admin_events.inc.php');
    
    // Initialize admin-specific settings
    add_event_handler('init', 'tam_admin_init');
}

// Handle public-specific actions
if (defined('IN_PUBLIC')) {
    // Include public-related actions (e.g., accessing the plugin features)
    require_once(TAM_PATH . 'public_events.inc.php');
    
    // Event handler for any public-specific initialization
    add_event_handler('init', 'tam_public_init');
}

// Database table creation and schema initialization
function tam_install() {
    global $prefixeTable;
    
    // Create the table for tag access management if it does not exist
    $query = "
    CREATE TABLE IF NOT EXISTS " . TAM_TABLE . " (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        tag_id INT(11) NOT NULL,
        user_group_id INT(11) NOT NULL,
        access_level VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB;
    ";
    pwg_query($query);
}

// Function to handle access checks
function tam_check_access($tag_id, $user_group_id) {
    // Query to check access level for a specific tag and user group
    $query = "
    SELECT access_level
    FROM " . TAM_TABLE . "
    WHERE tag_id = ? AND user_group_id = ?
    LIMIT 1
    ";
    $result = pwg_db_fetch_assoc(pwg_query($query, array($tag_id, $user_group_id)));
    
    // Return the access level if found, otherwise return null
    return isset($result['access_level']) ? $result['access_level'] : null;
}

// Admin initialization settings
function tam_admin_init() {
    // Check if the plugin needs to perform any admin-specific setup on load
    // Add any admin initialization logic here if needed
}

// Public initialization settings
function tam_public_init() {
    // Add any public-specific initialization logic here if needed
}

// Uninstall function to clean up the plugin
function tam_uninstall() {
    // Drop the plugin table if uninstalling
    $query = "DROP TABLE IF EXISTS " . TAM_TABLE;
    pwg_query($query);
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
