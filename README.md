Tag Access Management Plugin for Piwigo

This plugin grants access to albums based on tags. If a tag [keyword in piwigo] appears at least once in an album, the user associated with that tag is automatically granted access to the album.

Features:
* Automatic assignment of album access based on keywords.
* Support for multiple users and complex tag combinations.
* Fully integrated with the Piwigo database structure.
* Debugging options for easier troubleshooting.
* Works with albums and subalbums (currently no support for deeply nested subfolders).

Requirements:
* Piwigo version 11.0 or later.
* Write access to Piwigo database tables.
* Tags must be recognized as keywords by Piwigo. Tags from any program can be used as long as they are imported into Piwigo.

How to Use:
* go to settings of 'tag access management'
* Link the desired tags to existing Piwigo users.
* Press the button to apply the assignments.
* redo the apply button everytime you add new pictures with keywords
That’s it!

Notes:
* New pictures are not automatically processed. The plugin must be rerun after adding new pictures.
* Ensure that tag assignments are accurate before applying changes.
* The plugin has been successfully tested on a collection with over 120,000 pictures and 500 keywords
