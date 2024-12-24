<?php
defined('TAM_PATH') or die('Hacking attempt!');

/**
 * admin plugins menu link
 */
function tam_admin_menu_links($menu) 
{
  array_push($menu, array(
    'NAME' => 'Tag Access Management',
    'URL' => TAM_ADMIN,
  ));
  return $menu;
}
?>
