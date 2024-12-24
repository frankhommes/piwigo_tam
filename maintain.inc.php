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

function plugin_install($plugin_id, $plugin_version, $errors) { 
 
global $conf, $prefixeTable;
  
  $query = '
CREATE TABLE IF NOT EXISTS ' . $prefixeTable . 'tag_access_management_users (
  id int NOT NULL AUTO_INCREMENT,
  user_id int NOT NULL,
  keyword_id int NOT NULL,
   PRIMARY KEY (id)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
;';
  pwg_query($query);

  
    
}
function plugin_activate($plugin_id, $plugin_version, $errors) { 




}

function plugin_deactivate($plugin_id) { 

}

function plugin_uninstall() {
global $prefixeTable;

// Put anything here that should be executed during uninstallation.
$query = '
DROP TABLE ' . $prefixeTable . 'tag_access_management_users;';
pwg_query($query);

}

?>