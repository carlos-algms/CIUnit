<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
* fooStack, CIUnit for CodeIgniter
* Copyright (c) 2008-2009 Clemens Gruenberger
* Released under the MIT license, see:
* http://www.opensource.org/licenses/mit-license.php
*/

/*
* CodeIgniter source modified for fooStack / CIUnit
*
* If you use MY_Output, change the paraent class.
*/

if (class_exists('MY_Output')) {
    class CIU_CI_or_MY_Output extends MY_Output {}
} else {
    class CIU_CI_or_MY_Output extends CI_Output {}
}

class CIU_Output extends CIU_CI_or_MY_Output
{
    public $views = array();

    function __construct()
    {
        parent::__construct();
        $this->_ci_ob_level = ob_get_level();
        $this->cookies = array();
    }

    /**
     * store cookie headers
     */
    function set_cookie($arr)
    {
        if (!is_array($arr)) {
            $arr = func_get_args();
        }
        $this->cookies[] = $arr;
    }

    /**
     * Add the view name as output context.
     *
     * @param String $view
     */
    function append_view($view)
    {
        array_push($this->views, $view);
    }

    /**
     * Get and reset the views.
     *
     * @return array
     */
    function get_views()
    {
        return $this->views;
    }

    /**
     * Pop Output
     *
     * The final output the output class has stringed together is returned and truncated
     *
     */
    function pop_output()
    {
        $output = $this->final_output;
        $this->final_output = "";
        return $output;
    }

    /**
     * set_no_cache_headers
     * called as a post controller construction hook
     * should count therefore as controller duty
     */
    function set_no_cache_headers()
    {
        //somehow $this can't be used as headers are not set in that case
        $CI =& get_instance();
        $CI->output->soft_set_header('Content-type: text/html; charset=utf-8');
        $CI->output->soft_set_header('Cache-Control: no-cache');
        log_message('debug', 'no cache headers set in output class');
    }

    // --------------------------------------------------------------------

    /**
     * sets headers if not already set
     */
    function soft_set_header($header)
    {
        $key = strtolower(array_shift(split(':', $header)));
        $add = true;
        foreach ($this->headers as $hdr) {
            $h = split(':', $hdr);
            if (strtolower(array_shift($h)) == $key) {
                $add = false;
            }
        }
        $add ? ($this->headers[] = $header) : '';
    }

    /**
     * get headers
     */
    function get_headers()
    {
        return $this->headers;
    }

    /**
     * say
     * like normal echo but puts it in the output_buffer first, so we still can set headers
     * and post process it
     */
    function say($str)
    {
        ob_start();
        echo $str;
        $this->ob_flush_clean();
    }

    /**
     * ob_flush_clean
     * flushes or cleans the buffer depending on if we are finished outputting or still on a nested level
     */
    function ob_flush_clean()
    {
        $CI =& get_instance();
        if (ob_get_level() > $this->_ci_ob_level + 1) {
            ob_end_flush();
        } else {
            $this->append_output(ob_get_contents());
            @ob_end_clean();
        }
    }

    /**
     * Display Output
     *
     * All "view" data is automatically put into this variable by the controller class:
     *
     * $this->final_output
     *
     * This function sends the finalized output data to the browser along
     * with any server headers and profile data. It also stops the
     * benchmark timer so the page rendering speed and memory usage can be shown.
     *
     * @access    public
     * @return    mixed
     */
    function _display($output = '')
    {
        // Note: We use globals because we can't use $CI =& get_instance()
        // since this function is sometimes called by the caching mechanism,
        // which happens before the CI super object is available.
        global $BM, $CFG;

        // Grab the super object if we can.
        if (class_exists('CI_Controller')) {
            $CI =& get_instance();
        }

        // --------------------------------------------------------------------

        // Set the output data
        if ($output == '') {
            $output =& $this->final_output;
        }

        // --------------------------------------------------------------------

        // Do we need to write a cache file? Only if the controller does not have its
        // own _output() method and we are not dealing with a cache file, which we
        // can determine by the existence of the $CI object above
        if ($this->cache_expiration > 0 && isset($CI) && !method_exists($CI, '_output')) {
            $this->_write_cache($output);
        }

        // --------------------------------------------------------------------

        // Parse out the elapsed time and memory usage,
        // then swap the pseudo-variables with the data

        $elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');

        if ($this->parse_exec_vars === TRUE) {
            $memory = (!function_exists('memory_get_usage')) ? '0' : round(memory_get_usage() / 1024 / 1024, 2) . 'MB';

            $output = str_replace('{elapsed_time}', $elapsed, $output);
            $output = str_replace('{memory_usage}', $memory, $output);
        }

        // --------------------------------------------------------------------

        // Is compression requested?
        if ($CFG->item('compress_output') === TRUE && $this->_zlib_oc == FALSE) {
            if (extension_loaded('zlib')) {
                if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
                    ob_start('ob_gzhandler');
                }
            }
        }

        // --------------------------------------------------------------------

        // Are there any server headers to send?
        if (count($this->headers) > 0) {
            foreach ($this->headers as $header) {
                @header($header[0], $header[1]);
                log_message('debug', "header '$header[0], $header[1]' set.");
            }
        }

        // --------------------------------------------------------------------

        // Are there any cookies to set?
        if (count($this->cookies) > 0) {
            foreach ($this->cookies as $cookie) {
                call_user_func_array('setcookie', $cookie);
                log_message('debug', "cookie '" . join(', ', $cookie) . "' set.");
            }
        }


        // --------------------------------------------------------------------

        // If not we know we are dealing with a cache file so we'll
        // simply echo out the data and exit.
        if (!isset($CI)) {
            echo $output;
            log_message('debug', "Final output sent to browser");
            log_message('debug', "Total execution time: " . $elapsed);
            return TRUE;
        }

        // --------------------------------------------------------------------

        // Do we need to generate profile data?
        // If so, load the Profile class and run it.
        if ($this->enable_profiler == TRUE) {
            $CI->load->library('profiler');

            if (!empty($this->_profiler_sections)) {
                $CI->profiler->set_sections($this->_profiler_sections);
            }

            // If the output data contains closing </body> and </html> tags
            // we will remove them and add them back after we insert the profile data
            if (preg_match("|</body>.*?</html>|is", $output)) {
                $output = preg_replace("|</body>.*?</html>|is", '', $output);
                $output .= $CI->profiler->run();
                $output .= '</body></html>';
            } else {
                $output .= $CI->profiler->run();
            }
        }

        // --------------------------------------------------------------------

        // Does the controller contain a function named _output()?
        // If so send the output there. Otherwise, echo it.
        if (method_exists($CI, '_output')) {
            $CI->_output($output);
        } else {
            echo $output; // Send it to the browser!
        }

        log_message('debug', "Final output sent to browser");
        log_message('debug', "Total execution time: " . $elapsed);
    }

    // --------------------------------------------------------------------
}
