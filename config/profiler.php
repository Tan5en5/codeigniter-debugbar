<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Academic Free License version 3.0
 *
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the files license_afl.txt / license_afl.rst.
 * It is also available through the world wide web at this URL:
 * http://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

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

$config['php_info']             = true;
$config['codeigniter_info']     = true;
$config['messages']             = true;
$config['exceptions']           = true;
$config['benchmarks']           = true;
$config['get']                  = true;
$config['memory_usage']         = true;
$config['post']                 = true;
$config['uri_string']           = true;
$config['controller_info']      = true;
$config['queries']              = true;
$config['http_headers']         = true;
$config['session_data']         = true;
$config['config']               = true;
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

$config['base_url']                     = get_instance()->config->base_url('assets/php-debugbar');
$config['include_vendors']              = true;
$config['enable_jquery_noconflict']     = false;

/* End of file profiler.php */
/* Location: ./codeigniter-debugbar/config/profiler.php */