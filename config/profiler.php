<?php
/**
 * CodeIgniter Debug Bar
 *
 * @package     CodeIgniterDebugBar
 * @author      Anthony Tansens <atansens@gac-technology.com>
 * @license     http://opensource.org/licenses/MIT MIT
 * @since       Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Custom Profiler Sections
| -------------------------------------------------------------------------
| This file lets you determine whether or not various sections of Profiler
| data are displayed when the Profiler is enabled.
|
*/

$config['php_info']             = TRUE;
$config['codeigniter_info']     = TRUE;
$config['messages']             = TRUE;
$config['exceptions']           = TRUE;

/*
| -------------------------------------------------------------------------
| Profiler Sections
| -------------------------------------------------------------------------
| This file lets you determine whether or not various sections of Profiler
| data are displayed when the Profiler is enabled.
| Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/profiling.html
|
*/

$config['benchmarks']           = TRUE;
$config['config']               = TRUE;
$config['controller_info']      = TRUE;
$config['get']                  = TRUE;
$config['http_headers']         = TRUE;
$config['memory_usage']         = TRUE;
$config['post']                 = TRUE;
$config['queries']              = TRUE;
$config['uri_string']           = TRUE;
$config['session_data']         = TRUE;
$config['query_toggle_count']   = 25;

/*
| -------------------------------------------------------------------
| PHP Debug Bar Javascript Renderer Sections
| -------------------------------------------------------------------
| These are the config lines for PHP Debug Bar Javascript Renderer.
|
| Options available, make sure you know what you are doing :
|
|   base_path
|   base_url
|   include_vendors
|   javascript_class
|   variable_name
|   initialization
|   enable_jquery_noconflict
|   controls
|   disable_controls
|   ignore_collectors
|   ajax_handler_classname
|   ajax_handler_bind_to_jquery
|   open_handler_classname
|   open_handler_url
|
|   http://phpdebugbar.com/docs/rendering.html#rendering
|
*/

$config['base_url']                     = NULL;
$config['include_vendors']              = FALSE;
$config['enable_jquery_noconflict']     = FALSE;
