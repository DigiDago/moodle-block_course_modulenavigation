# moodle-block_course_modulenavigation

[![Build Status](https://travis-ci.org/bmbrands/moodle-block_course_modulenavigation.svg?branch=master)](https://travis-ci.org/bmbrands/moodle-block_course_modulenavigation)

Introduction
============
Course module navigation is a block that show the users a summary (like a table of content) of a course with sections name and a list of all resources and activties (except labels and URL). One objective of this block is to replace classical block navigation in a way to present only the course contents and sections title.
- Clicking at section name will display the list of resources and activities of the section
- Clicking at resources and activites will display the page of the resource or activity.

This block use automaticaly names of sections and names of resources and activities. When you use course module navigation, we recommand to use pages to add videos or contents in order to be able to view all resources in the list of the block. If you use labels, contents will be not displayed in course navigation.
If you want to display course module navigation on all pages of the course (main, activities, resources), make sure to check permission of the block and display it on "every page".

== About activity completion ==
If activity completion is used in the course, course navigation block display a circle empty or green to show state of completion. 

== Option ==
This block has an option to display at choice :
- Only the active section
- or all the sections 


Maintainer
============
Course module navigation has been developed by Bas Brand and was based on an request of Digidago (Clément PRUDHOMME). It is currently maintained by DigiDago / Bas Brand.


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
 3. Copy 'course_modulenavigation' to '/blocks/' if you have not already done so.
 4. Login as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
 5.  Put Moodle out of Maintenance Mode.

Upgrade Instructions
====================
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.
 2. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 3. In '/blocks/' move old 'course_modulenavigation' directory to a backup folder outside of Moodle.
 4. Follow installation instructions above.
 5. If automatic 'Purge all caches' appears not to work by lack of display etc. then perform a manual 'Purge all caches'
    under 'Home -> Site administration -> Development -> Purge all caches'.
 6. Put Moodle out of Maintenance Mode.

Uninstallation
==============
 1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 2. In '/blocks/' remove the folder 'course_modulenavigation'.
 4. In the database, remove the for 'course_modulenavigation' ('plugin' attribute) in the table 'config_plugins'.  If
    using the default prefix this will be 'mdl_config_plugins'.
 5. Put Moodle out of Maintenance Mode.

Version Information
===================
See Changes.md.


Any Problems, questions, suggestions
===================
If you have a problem with this block, suggestions for improvement, drop an email at :
- Clément PRUDHOMME :  contact@digidago.com
-  Github : https://github.com/bmbrands/moodle-block_course_modulenavigation


Languages and translation
===================
English and french versions included / versions anglaise et française incluses.


Customization
===================
You can esayli use .css to customize style of the bloc course module navigaiton.



Us
==
Bas Brands (http://sonsbeekmedia.nl/)
Clément Prudhomme (@copyright 2016 Digidago http://digidago.com/)

