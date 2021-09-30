<?php

/*
 * ------------------------------------------------------
 *  CIUnit Version
 * ------------------------------------------------------
 */
define('CIUnit_Version', '0.18-dev_for_CI2.1.0');

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
if (! defined('ENVIRONMENT')){ define('ENVIRONMENT', 'testing'); }

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * By default CI runs with error reporting set to -1.
 *
 */

error_reporting(-1);


/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

/* This chdir() causes error when run tests by folder.
	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}
*/

if (realpath($system_path) !== FALSE) {
    $system_path = realpath($system_path) . '/';
}

// ensure there's a trailing slash
$system_path = rtrim($system_path, '/') . '/';

// Is the system path correct?
if (!is_dir($system_path)) {
    exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: " . pathinfo(__FILE__, PATHINFO_BASENAME));
}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
// The name of THIS file
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// The PHP file extension
// this global constant is deprecated.
define('EXT', '.php');

// Path to the system folder
define('BASEPATH', str_replace("\\", "/", $system_path));

// Path to the front controller (this file)
define('FCPATH', str_replace(SELF, '', __FILE__));

// Name of the "system folder"
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));


// The path to the "application" folder
if (is_dir($application_folder)) {
    define('APPPATH', realpath($application_folder) . '/');
} else {
    if (!is_dir(BASEPATH . $application_folder . '/')) {
        exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: " . SELF);
    }

    define('APPPATH', realpath(BASEPATH . $application_folder) . '/');
}

// The path to the "views" folder
if (is_dir($view_folder)) {
    define ('VIEWPATH', $view_folder . '/');
} else {
    if (!is_dir(APPPATH . 'views/')) {
        exit("Your view folder path does not appear to be set correctly. Please open the following file and correct this: " . SELF);
    }

    define ('VIEWPATH', APPPATH . 'views/');
}

// The path to CIUnit
define('CIUPATH', __DIR__ . '/');

// The path to the Tests folder
define('TESTSPATH', realpath($tests_folder) . '/');

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */

// Load the CIUnit CodeIgniter Core
require_once CIUPATH . 'core/CodeIgniter.php';

// Load the CIUnit Framework
require_once CIUPATH . 'libraries/CIUnit.php';

//=== and off we go ===
$CI =& set_controller('CIU_Controller', CIUPATH . 'core/');
$CI->load->add_package_path(CIUPATH);

CIUnit::$spyc = new Spyc();

require_once(CIUPATH . 'libraries/Fixture.php');

$CI->fixture = new Fixture();
CIUnit::$fixture =& $CI->fixture;
