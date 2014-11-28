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

/**
 * Profiler Class
 *
 * This class enables you to display benchmark, query, and other data
 * in order to help with debugging and optimization using PHP Debug Bar.
 *
 * @package     CodeIgniterDebugBar
 * @subpackage  Libraries
 * @category    Libraries
 * @author      Anthony Tansens <atansens@gac-technology.com>
 */

use DebugBar\DebugBar;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\ExceptionsCollector;

class CI_Profiler
{
    /**
     * List of profiler sections available to show
     *
     * @var array
     */
    protected $_available_sections = array(
        'php_info',
        'codeigniter_info',
        'messages',
        'exceptions',
        'benchmarks',
        'get',
        'memory_usage',
        'post',
        'uri_string',
        'controller_info',
        'queries',
        'http_headers',
        'session_data',
        'config'
    );

    /**
     * Number of queries to show before making the additional queries togglable
     *
     * @var int
     */
    protected $_query_toggle_count = 25;

    /**
     * Reference to the CodeIgniter singleton
     *
     * @var object
     */
    protected $CI;

    /** 
     * Reference to the DebugBar instance
     * 
     * @var DebugBar 
     */
    protected $debugbar;

    /**
     *
     * @var array 
     */
    protected $config;

    /**
     * Class constructor
     *
     * Initialize Profiler
     *
     * @param array $config Parameters
     */
    public function __construct($config = array())
    {
        $this->debugbar = new DebugBar();
        $this->config =& $config;
        $this->CI =& get_instance();
        $this->CI->load->language('profiler');

        if (isset($config['query_toggle_count'])) {
            $this->_query_toggle_count = (int) $config['query_toggle_count'];
            unset($config['query_toggle_count']);
        }

        // default all sections to display
        foreach ($this->_available_sections as $section) {
            if ( ! isset($config[$section])) {
                $this->_compile_{$section} = true;
            }
        }

        $this->set_sections($config);
    }

    /**
     * Set Sections
     *
     * Sets the private _compile_* properties to enable/disable Profiler sections
     *
     * @param mixed $config
     * @return void
     */
    public function set_sections($config)
    {
        if (isset($config['query_toggle_count'])) {
            $this->_query_toggle_count = (int) $config['query_toggle_count'];
            unset($config['query_toggle_count']);
        }

        foreach ($config as $method => $enable) {
            if (in_array($method, $this->_available_sections)) {
                $this->_compile_{$method} = ($enable !== false);
            }
        }
    }

    /**
     * Compile CodeIgniter version, language and environment
     *
     * @return void
     */
    protected function _compile_codeigniter_info()
    {
        $this->CI->load->library('collectors/CodeIgniterCollector', null, 'codeIgniterCollector');
        $this->CI->codeIgniterCollector->setCI($this->CI);
        $this->debugbar->addCollector($this->CI->codeIgniterCollector);
    }

    /**
     * Get PHP version
     *
     * @return void
     */
    protected function _compile_php_info()
    {
        $this->debugbar->addCollector(new PhpInfoCollector());
    }

    /**
     * Compile console messages
     *
     * @return void
     */
    protected function _compile_messages()
    {
        if (!class_exists('Console')) {
            return;
        }

        $this->debugbar->addCollector(new MessagesCollector());
        $logs = $this->CI->console->getMessages();

        foreach ($logs as $log) {
            $this->debugbar['messages']->addMessage($log['data'], $log['type']);
        }
    }

    /**
     * Compile console exceptions
     *
     * @return void
     */
    protected function _compile_exceptions()
    {
        if (!class_exists('Console')) {
            return;
        }

        $this->debugbar->addCollector(new ExceptionsCollector());
        $logs = $this->CI->console->getExeptions();

        foreach ($logs as $log) {
            $this->debugbar['exceptions']->addException($log['data']);
        }
    }

    /**
     * Auto Profiler
     *
     * This function cycles through the entire array of mark points and
     * matches any two points that are named identically (ending in "_start"
     * and "_end" respectively).  It then compiles the execution times for
     * all points and returns it as an array
     *
     * @return void
     */
    protected function _compile_benchmarks()
    {
        $this->CI->load->library('collectors/BenchmarkCollector', null, 'benchmarkCollector');
        $this->CI->benchmarkCollector->addBenchmarkMeasure($this->CI->benchmark);
        $this->debugbar->addCollector($this->CI->benchmarkCollector);
    }

    /**
     * Compile Queries
     *
     * @return void
     */
    protected function _compile_queries()
    {
        $this->CI->load->library('collectors/QueryCollector', null, 'queryCollector');
        $this->CI->queryCollector->setDbs($this->CI);
        $this->debugbar->addCollector($this->CI->queryCollector);
    }

    /**
     * Compile $_GET Data
     *
     * @return void
     */
    protected function _compile_get()
    {
        $this->CI->load->library('collectors/RequestDataCollector', null, 'requestDataCollector');
        $this->CI->requestDataCollector->setGlobal('get');
    }

    /**
     * Compile $_POST Data
     *
     * @return void
     */
    protected function _compile_post()
    {
        $this->CI->load->library('collectors/RequestDataCollector', null, 'requestDataCollector');
        $this->CI->requestDataCollector->setGlobal('post');
    }

    /**
     * Show query string
     *
     * @return void
     */
    protected function _compile_uri_string()
    {
        $this->CI->load->library('collectors/RequestDataCollector', null, 'requestDataCollector');
        $this->CI->requestDataCollector->setAdditionalInfos($this->CI->lang->line('profiler_uri_string'), $this->CI->uri->uri_string);
    }

    /**
     * Show the controller and function that were called
     *
     * @return void
     */
    protected function _compile_controller_info()
    {
        $this->CI->load->library('collectors/RequestDataCollector', null, 'requestDataCollector');
        $this->CI->requestDataCollector->setAdditionalInfos($this->CI->lang->line('profiler_controller_info'), $this->CI->router->class.'/'.$this->CI->router->method);
    }

    /**
     * Compile memory usage
     *
     * Display total used memory
     *
     * @return void
     */
    protected function _compile_memory_usage()
    {
        $this->debugbar->addCollector(new MemoryCollector());
    }

    /**
     * Compile header information
     *
     * Lists HTTP headers
     *
     * @return void
     */
    protected function _compile_http_headers()
    {
        $this->CI->load->library('collectors/RequestDataCollector', null, 'requestDataCollector');
        $this->CI->requestDataCollector->setGlobal('server');
    }

    /**
     * Compile config information
     *
     * Lists developer config variables
     *
     * @return void
     */
    protected function _compile_config()
    {
        $this->debugbar->addCollector(new ConfigCollector($this->CI->config->config));
    }

    /**
     * Compile session userdata
     *
     * @return void
     */
    protected function _compile_session_data()
    {
        if ( ! isset($this->CI->session)) {
            return;
        }

        $this->CI->load->library('collectors/SessionCollector', null, 'sessionCollector');
        $this->CI->sessionCollector->setSession($this->CI->session);
        $this->debugbar->addCollector($this->CI->sessionCollector);
    }

    /**
     * Run the Profiler
     *
     * @return string
     */
    public function run()
    {
        foreach ($this->_available_sections as $section) {
            if ($this->_compile_{$section} !== false) {
                $func = '_compile_'.$section;
                $this->{$func}();
            }
        }

        // Add request data collector
        if (class_exists('requestDataCollector')) {
            $this->debugbar->addCollector($this->CI->requestDataCollector);
        }

        $debugbarRenderer = $this->debugbar->getJavascriptRenderer();
        $debugbarRenderer->setOptions($this->config);
        $head_src = $this->CI->load->get_var('head_src');
        $sources = array(
            $head_src,
            $debugbarRenderer->renderHead()
        );
        $this->CI->load->vars(array(
            'head_src' => implode("\n", $sources),
        ));

        return $debugbarRenderer->render();
    }

}

/* End of file Profiler.php */
/* Location: ./codeigniter-debugbar/libraries/Profiler.php */