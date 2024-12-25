<?php
defined('TAM_PATH') or die('Hacking attempt!');

/**
 * admin plugins menu link
 */
function tam_admin_menu_links($menu) {
    $menu[] = array(
        'name' => 'Tag Access Management',
        'url' => TAM_ADMIN,
        'active' => (strpos($_SERVER['REQUEST_URI'], 'plugin-' . TAM_ID) !== false)
    );
    return $menu;
}
?>
