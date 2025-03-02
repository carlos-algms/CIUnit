<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package     CodeIgniter
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license     http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

if (class_exists('MY_Exceptions')) {
    class CIU_CI_or_MY_Exceptions extends MY_Exceptions {}
} else {
    class CIU_CI_or_MY_Exceptions extends CI_Exceptions {}
}

/**
 * Exceptions Class
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Exceptions
 * @author      ExpressionEngine Dev Team
 * @link        http://codeigniter.com/user_guide/libraries/exceptions.html
 */
class CIU_Exceptions extends CIU_CI_or_MY_Exceptions
{

    /**
     * Exception Logger
     *
     * This function logs PHP generated error messages
     *
     * @access   private
     * @param    string    the error severity
     * @param    string    the error string
     * @param    string    the error filepath
     * @param    string    the error line number
     * @return   string
     */
    function log_exception($severity, $message, $filepath, $line)
    {
        $severity = (!isset($this->levels[$severity])) ? $severity : $this->levels[$severity];

        log_message('error', 'Severity: ' . $severity . '  --> ' . $message . ' ' . $filepath . ' ' . $line, TRUE);
    }

    // --------------------------------------------------------------------

    /**
     * 404 Page Not Found Handler
     *
     * @access    private
     * @param    string
     * @return    string
     */
    function show_404($page = '', $log_error = TRUE)
    {
        $heading = "404 Page Not Found";
        $message = "The page you requested was not found.";

        // By default we log this, but allow a dev to skip it
        if ($log_error) {
            log_message('error', '404 Page Not Found --> ' . $page);
        }

        echo $this->show_error($heading, $message, 'error_404', 404);
        exit;
    }

    // --------------------------------------------------------------------

    /**
     * General Error Page
     *
     * This function takes an error message as input
     * (either as a string or an array) and displays
     * it using the specified template.
     *
     * @access   private
     * @param    string    the heading
     * @param    string    the message
     * @param    string    the template name
     * @return   string
     */
    function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        $message = implode(' ', (!is_array($message)) ? array($message) : $message);

        if (ob_get_level() > $this->ob_level + 1) {
            ob_end_flush();
        }

        echo "[CIUnit] Error: $status_code Message: $message\n";
        return;
    }

    // --------------------------------------------------------------------

    /**
     * Native PHP error handler
     *
     * @access   private
     * @param    string    the error severity
     * @param    string    the error string
     * @param    string    the error filepath
     * @param    string    the error line number
     * @return   string
     */
    function show_php_error($severity, $message, $filepath, $line)
    {
        $severity = (!isset($this->levels[$severity])) ? $severity : $this->levels[$severity];

        $filepath = str_replace("\\", "/", $filepath);

        // For safety reasons we do not show the full file path
        if (FALSE !== strpos($filepath, '/')) {
            $x = explode('/', $filepath);
            $filepath = $x[count($x) - 2] . '/' . end($x);
        }

        if (ob_get_level() > $this->ob_level + 1) {
            ob_end_flush();
        }

        echo "[CIUnit] PHP Error: $severity - $message File Path: $filepath (line: $line)\n";
    }


}
