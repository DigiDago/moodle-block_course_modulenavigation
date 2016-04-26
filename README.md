# moodle-block_course_navigation

Introduction
============
A block that show the user's summary of a course : section title and ressources and activties. 


Required version of Moodle
==========================
This version works with Moodle version 2015111600.00 3.0 (Build: 20151116) and above within the 3.0 branch until the
next release.

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'docs.moodle.org/30/en/Installing_Moodle'.

Installation
============
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  
 2. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no 
    users using it bar you as the administrator - if you have not already done so.
 3. Copy 'course_navigation' to '/blocks/' if you have not already done so.
 4. Login as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
 5.  Put Moodle out of Maintenance Mode.

Upgrade Instructions
====================
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.
 2. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 3. In '/blocks/' move old 'course_navigation' directory to a backup folder outside of Moodle.
 4. Follow installation instructions above.
 5. If automatic 'Purge all caches' appears not to work by lack of display etc. then perform a manual 'Purge all caches'
    under 'Home -> Site administration -> Development -> Purge all caches'.
 6. Put Moodle out of Maintenance Mode.

Uninstallation
==============
 1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 2. In '/blocks/' remove the folder 'course_navigation'.
 4. In the database, remove the for 'course_navigation' ('plugin' attribute) in the table 'config_plugins'.  If
    using the default prefix this will be 'mdl_config_plugins'.
 5. Put Moodle out of Maintenance Mode.

Version Information
===================
See Changes.md.

Us
==
Bas Brands
Clément Prudhomme (@copyright 2016 Digidago)

