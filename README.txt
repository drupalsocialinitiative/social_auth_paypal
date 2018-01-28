CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * How it works
 * Support requests
 * Maintainers


INTRODUCTION
------------

Social Auth Paypal Module is a Paypal Authentication integration for Drupal.


REQUIREMENTS
------------

This module requires the following modules:

 * Social Auth (https://drupal.org/project/social_auth)
 * Social API (https://drupal.org/project/social_api)


INSTALLATION
------------

 * Run composer to install dependencies:
   composer require "drupal/social_auth_paypal:~2.0"

 * Install the dependencies: Social API and Social Auth.

 * Install as you would normally install a contributed Drupal module. See:
   https://drupal.org/documentation/install/modules-themes/modules-8
   for further information.


CONFIGURATION
-------------

 * Add your Paypal project OAuth information in
   Configuration » User Authentication » Paypal.

 * Place a Social Auth Paypal block in Structure » Block Layout.

 * If you already have a Social Auth Login block in the site, rebuild the cache.


HOW IT WORKS
------------

User can click on the Paypal logo on the Social Auth Login block
You can also add a button or link anywhere on the site that points
to /user/login/paypal, so theming and customizing the button or link
is very flexible.

When the user opens the /user/login/paypal link, it automatically takes
user to Paypal Accounts for authentication. Paypal then returns the user to
Drupal site. If we have an existing Drupal user with the same email address
provided by Paypal, that user is logged in. Otherwise a new Drupal user is
created.


SUPPORT REQUESTS
----------------

Before posting a support request, carefully read the installation
instructions provided in module documentation page.

Before posting a support request, check Recent log entries at
admin/reports/dblog

Once you have done this, you can post a support request at module issue queue:
https://www.drupal.org/project/issues/social_auth_paypal

When posting a support request, please inform what does the status report say
at admin/reports/dblog and if you were able to see any errors in
Recent log entries.


MAINTAINERS
-----------

Current maintainers:
 * Getulio Sánchez (gvso) - https://www.drupal.org/u/gvso
 * Himanshu Dixit (himanshu-dixit) - https://www.drupal.org/u/himanshu-dixit
