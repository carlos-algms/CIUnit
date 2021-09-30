<?php

/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same  directory
 * as this file.
 *
 * NO TRAILING SLASH!
 *
 * The test should be run from inside the tests folder.  The assumption
 * is that the tests folder is in the same directory path as system.  If
 * it is not, update the paths appropriately.
 */
$system_path = __DIR__ . '/../system';

/*
 *---------------------------------------------------------------
 * APPLICATION FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder then the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server.  If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 *
 * The tests should be run from inside the tests folder.  The assumption
 * is that the tests folder is in the same directory as the application
 * folder.  If it is not, update the path accordingly.
 */
$application_folder = __DIR__ . '/../application';

/*
 *---------------------------------------------------------------
 * VIEW FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want to move the view folder out of the application
 * folder set the path to the folder here. The folder can be renamed
 * and relocated anywhere on your server. If blank, it will default
 * to the standard location inside your application folder.  If you
 * do move this, use the full server path to this folder
 *
 * NO TRAILING SLASH!
 *
 */
$view_folder = '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 *
 */
// $assign_to_config['name_of_config_item'] = 'value of config item';


/**
 * --------------------------------------------------------------
 * UNIT TESTS FOLDER NAME
 * --------------------------------------------------------------
 *
 * This is the path to the tests folder.
 */
$tests_folder = __DIR__;

require_once __DIR__ . '/../vendor/carlos-algms/ciunit/src/bootstrap_phpunit.php';
