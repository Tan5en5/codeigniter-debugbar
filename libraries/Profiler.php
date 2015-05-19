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
use DebugBar\JavascriptRenderer;
use DebugBar\Storage\FileStorage;

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
        'config',
        'included_files',
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
        if ( ! isset($this->CI->console)) {
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
        if ( ! isset($this->CI->console)) {
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
        $this->CI->load->library('collectors/CodeIgniterRequestCollector', null, 'codeIgniterRequestCollector');
        $this->CI->codeIgniterRequestCollector->setRequestData($this->CI->input, 'get');
    }

    /**
     * Compile $_POST Data
     *
     * @return void
     */
    protected function _compile_post()
    {
        $this->CI->load->library('collectors/CodeIgniterRequestCollector', null, 'codeIgniterRequestCollector');
        $this->CI->codeIgniterRequestCollector->setRequestData($this->CI->input, 'post');
    }

    /**
     * Show query string
     *
     * @return void
     */
    protected function _compile_uri_string()
    {
        $this->CI->load->library('collectors/CodeIgniterRequestCollector', null, 'codeIgniterRequestCollector');
        $this->CI->codeIgniterRequestCollector->setAdditionalData($this->CI->lang->line('profiler_uri_string'), $this->CI->uri->uri_string);
    }

    /**
     * Show the controller and function that were called
     *
     * @return void
     */
    protected function _compile_controller_info()
    {
        $this->CI->load->library('collectors/CodeIgniterRequestCollector', null, 'codeIgniterRequestCollector');
        $this->CI->codeIgniterRequestCollector->setAdditionalData($this->CI->lang->line('profiler_controller_info'), $this->CI->router->class.'/'.$this->CI->router->method);
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
        $this->CI->load->library('collectors/CodeIgniterRequestCollector', null, 'codeIgniterRequestCollector');
        $this->CI->codeIgniterRequestCollector->setRequestData($this->CI->input, 'server');
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
     * Compile included files
     *
     * @return void
     */
    protected function _compile_included_files()
    {
        $this->CI->load->library('collectors/IncludedFileCollector', null, 'fileCollector');
        $this->debugbar->addCollector($this->CI->fileCollector);
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
        if (isset($this->CI->codeIgniterRequestCollector)) {
            $this->debugbar->addCollector($this->CI->codeIgniterRequestCollector);
        }

        return $this->render();
    }

    /**
     * 
     * @return string
     */
    protected function render()
    {
        $renderer = $this->debugbar->getJavascriptRenderer();
        $renderer->setOptions($this->config);
        $is_ajax = $this->CI->input->is_ajax_request();
        $initialize = (!$is_ajax) ? true : false;
        $assets = (!$is_ajax) ? $this->getAssets($renderer) : null;

        if ($is_ajax && $this->isJsonOutput()) {
            $use_open_handler = $this->setStorage();
            $this->debugbar->sendDataInHeaders($use_open_handler);
            return;
        } else {
            return $assets.$renderer->render($initialize);
        }
    }

    /**
     * Set storage for debugbar
     * 
     * @return boolean
     */
    protected function setStorage()
    {
        if (!isset($this->config['open_handler_url'])) {
            return false;
        }

        $path = $this->config['cache_path'];
        $cache_path = ($path === '') ? APPPATH.'cache/debugbar/' : $path;
        file_exists($cache_path) OR mkdir($cache_path, DIR_WRITE_MODE, true);
        $this->debugbar->setStorage(new FileStorage($cache_path));

        return true;
    }

    /**
     * 
     * @return boolean
     */
    protected function isJsonOutput()
    {
        return (stripos($this->CI->output->get_content_type(), 'json') !== false);
    }

    /**
     * 
     * @param JavascriptRenderer $renderer
     * @return string
     */
    protected function getAssets(JavascriptRenderer $renderer)
    {
        ob_start();
        echo '<style type="text/css">'."\n";
        $renderer->dumpCssAssets();
        // Change icon to CI icon, based on https://github.com/bcit-ci/ci-design/blob/master/website/assets/images/ci-logo.png
        echo 'div.phpdebugbar-header, a.phpdebugbar-restore-btn, div.phpdebugbar-openhandler .phpdebugbar-openhandler-header {'
        . 'background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAATCAYAAACZZ43PAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U'
        . '29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIRSURBVHjapJQ9TFRBEMdn7zj5iF8JhpwhxkQxxILgRxAKEkNvuAY7C2oTGxpjtLA1hEISKOwMl'
        . 'HZqLLDRyoSKBo0Up4QQcgXBEAGB2/U3b+cd7+DOxs39d+ftm/nvf2bnnatclOYjJ+erHbKPtdPodfF7kBbXIv8aNwt7csuLTDU/wzFnEFiqBsYNMJ4L0'
        . 'gPkOBKCuvgQYQ632RtjuwAGmilomACB/c7JC8x2cBic+OSEUOdjKZyMHiJ4GqsTHIBtsCSmrjZCY4I7LhbsLPgD8uB33sl6rl7AURE9EV4tL9eYVXaHB'
        . 'Rcspt17Kfoo+yHL3foauFotnoILeqLShSBvSGU0IXQyidsa66Dl/hy8SxSoNCpe4uWABSvZL4Jfcvy8PV8Fw3a7yvEMXI81OJRe5I9awWIyIqdRUIRgB'
        . 'nsC/LB6aEramWe4mfGEAKMXzssZAj3xHHsTkBSIWIDyAaHv7VpVwR5TX9pI3VY4PbkVfAar7JdIb8RKv8XymPVDxvdUqqBiVdfgMrfyBOe32IucVU7bG'
        . 'zWKWcxN892KNfCyzPozyTtWdhfSV0Tdx15JestFqfxWefxmfbKQKvhK4Gtrqm37HgLe1eP9DbQ+mvIXn5e5WieGyDZJ4D3WtrTPXaZ9Q0ylxLRBmo+C/'
        . 'Ue4yqWjnoZ1OFeVNsyP2X43gh44rrD1SWuGr3SVERpCkP8ZfwUYAL2WpEUbzbyiAAAAAElFTkSuQmCC") no-repeat scroll 5px 4px #efefef;'
        . '}';
        echo '</style>'."\n";
        echo '<script type="text/javascript">'."\n";
        $renderer->dumpJsAssets();
        echo '</script>'."\n";

        return ob_get_clean();
    }
}
