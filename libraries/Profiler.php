<?php 
/**
 * CodeIgniter Debug Bar
 *
 * @package     CodeIgniterDebugBar
 * @author      Anthony Tansens <a.tansens+github@gmail.com>
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
 * @author      Anthony Tansens <a.tansens+github@gmail.com>
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
    protected static $CI;

    /** 
     * Reference to the DebugBar instance
     * 
     * @var DebugBar 
     */
    protected static $debugbar;

    /**
     * Reference to the JavascriptRenderer instance
     *
     * @var JavascriptRenderer 
     */
    public static $renderer;

    /**
     * Configuration data
     *
     * @var array 
     */
    protected static $config;

    /**
     * Class constructor
     *
     * Initialize Profiler
     *
     * @param array $config Parameters
     */
    public function __construct($config = array())
    {
        self::$debugbar = new DebugBar();
        self::$config =& $config;
        // Backward compatibility
        isset(self::$config['display_assets']) OR self::$config['display_assets'] = TRUE;
        isset(self::$config['display_javascript']) OR self::$config['display_javascript'] = TRUE;
        self::$CI =& get_instance();
        self::$CI->load->language('profiler');
        self::$renderer = self::$debugbar->getJavascriptRenderer();
        self::$renderer->setOptions(self::$config);

        // default all sections to display
        foreach ($this->_available_sections as $section)
        {
            if ( ! isset($config[$section]))
            {
                $this->_compile_{$section} = TRUE;
            }
        }

        $this->set_sections($config);
        log_message('info', 'Profiler Class Initialized');
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
        if (isset($config['query_toggle_count']))
        {
            $this->_query_toggle_count = (int) $config['query_toggle_count'];
            unset($config['query_toggle_count']);
        }

        foreach ($config as $method => $enable)
        {
            if (in_array($method, $this->_available_sections))
            {
                $this->_compile_{$method} = ($enable !== FALSE);
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
        self::$CI->load->library('collectors/CodeIgniterCollector', NULL, 'codeIgniterCollector');
        self::$debugbar->addCollector(self::$CI->codeIgniterCollector);
    }

    /**
     * Get PHP version
     *
     * @return void
     */
    protected function _compile_php_info()
    {
        self::$debugbar->addCollector(new PhpInfoCollector());
    }

    /**
     * Compile console messages
     *
     * @return void
     */
    protected function _compile_messages()
    {
        if ( ! isset(self::$CI->console))
        {
            return;
        }

        self::$debugbar->addCollector(new MessagesCollector());
        $logs = self::$CI->console->getMessages();

        foreach ($logs as $log)
        {
            self::$debugbar['messages']->addMessage($log['data'], $log['type']);
        }
    }

    /**
     * Compile console exceptions
     *
     * @return void
     */
    protected function _compile_exceptions()
    {
        if ( ! isset(self::$CI->console))
        {
            return;
        }

        self::$debugbar->addCollector(new ExceptionsCollector());
        $logs = self::$CI->console->getExeptions();

        foreach ($logs as $log)
        {
            self::$debugbar['exceptions']->addException($log['data']);
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
        self::$CI->load->library('collectors/BenchmarkCollector', NULL, 'benchmarkCollector');
        self::$CI->benchmarkCollector->addBenchmarkMeasure(self::$CI->benchmark);
        self::$debugbar->addCollector(self::$CI->benchmarkCollector);
    }

    /**
     * Compile Queries
     *
     * @return void
     */
    protected function _compile_queries()
    {
        self::$CI->load->library('collectors/QueryCollector', NULL, 'queryCollector');
        self::$CI->queryCollector->setDbs(self::$CI);
        self::$debugbar->addCollector(self::$CI->queryCollector);
    }

    /**
     * Compile $_GET Data
     *
     * @return void
     */
    protected function _compile_get()
    {
        self::$CI->load->library('collectors/CodeIgniterRequestCollector', NULL, 'codeIgniterRequestCollector');
        self::$CI->codeIgniterRequestCollector->setRequestData(self::$CI->input, 'get');
    }

    /**
     * Compile $_POST Data
     *
     * @return void
     */
    protected function _compile_post()
    {
        self::$CI->load->library('collectors/CodeIgniterRequestCollector', NULL, 'codeIgniterRequestCollector');
        self::$CI->codeIgniterRequestCollector->setRequestData(self::$CI->input, 'post');
    }

    /**
     * Show query string
     *
     * @return void
     */
    protected function _compile_uri_string()
    {
        self::$CI->load->library('collectors/CodeIgniterRequestCollector', NULL, 'codeIgniterRequestCollector');
        self::$CI->codeIgniterRequestCollector->setAdditionalData(self::$CI->lang->line('profiler_uri_string'), self::$CI->uri->uri_string);
    }

    /**
     * Show the controller and function that were called
     *
     * @return void
     */
    protected function _compile_controller_info()
    {
        self::$CI->load->library('collectors/CodeIgniterRequestCollector', NULL, 'codeIgniterRequestCollector');
        self::$CI->codeIgniterRequestCollector->setAdditionalData(self::$CI->lang->line('profiler_controller_info'), self::$CI->router->class.'/'.self::$CI->router->method);
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
        self::$debugbar->addCollector(new MemoryCollector());
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
        self::$CI->load->library('collectors/CodeIgniterRequestCollector', NULL, 'codeIgniterRequestCollector');
        self::$CI->codeIgniterRequestCollector->setRequestData(self::$CI->input, 'server');
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
        self::$debugbar->addCollector(new ConfigCollector(self::$CI->config->config));
    }

    /**
     * Compile session userdata
     *
     * @return void
     */
    protected function _compile_session_data()
    {
        if ( ! isset(self::$CI->session))
        {
            return;
        }

        self::$CI->load->library('collectors/SessionCollector', NULL, 'sessionCollector');
        self::$CI->sessionCollector->setSession(self::$CI->session);
        self::$debugbar->addCollector(self::$CI->sessionCollector);
    }

    /**
     * Compile included files
     *
     * @return void
     */
    protected function _compile_included_files()
    {
        self::$CI->load->library('collectors/IncludedFileCollector', NULL, 'fileCollector');
        self::$debugbar->addCollector(self::$CI->fileCollector);
    }

    /**
     * Run the Profiler
     *
     * @return string
     */
    public function run()
    {
        foreach ($this->_available_sections as $section)
        {
            if ($this->_compile_{$section} !== FALSE)
            {
                $func = '_compile_'.$section;
                $this->{$func}();
            }
        }

        // Add request data collector
        if (isset(self::$CI->codeIgniterRequestCollector))
        {
            self::$debugbar->addCollector(self::$CI->codeIgniterRequestCollector);
        }

        return $this->_render();
    }

    /**
     * 
     * @return string
     */
    protected function _render()
    {
        if (self::$CI->input->is_ajax_request())
        {
            $use_open_handler = $this->_set_storage();
            self::$debugbar->sendDataInHeaders($use_open_handler);
            $return = NULL;
        }
        else
        {
            $assets        = (bool) self::$config['display_assets'] ? $this->_get_assets() : NULL;
            $inline_script = (bool) self::$config['display_javascript'] ? self::inline_script() : NULL;
            $return        = $assets.$inline_script;
        }

        return $return;
    }

    /**
     * Set storage for debugbar
     * 
     * @return boolean
     */
    protected function _set_storage()
    {
        if ( ! isset(self::$config['open_handler_url']))
        {
            return FALSE;
        }

        $path       = self::$config['cache_path'];
        $cache_path = ($path === '') ? APPPATH.'cache/debugbar/' : $path;
        file_exists($cache_path) OR mkdir($cache_path, DIR_WRITE_MODE, TRUE);
        self::$debugbar->setStorage(new FileStorage($cache_path));

        return TRUE;
    }

    /**
     * Return all CSS/JS assets
     * 
     * @return string
     */
    protected function _get_assets()
    {
        ob_start();

        echo '<style type="text/css">'."\n";
        echo self::css_assets();
        echo '</style>'."\n";
        echo '<script type="text/javascript">'."\n";
        echo self::js_assets();
        echo '</script>'."\n";

        return ob_get_clean();
    }

    /**
     * Return inline script generated by DebugBar
     * 
     * @return string
     */
    public static function inline_script()
    {
        return self::$renderer->render( ! self::$CI->input->is_ajax_request());
    }

    /**
     * Return all CSS assets or write in a file
     * 
     * @param string $targetFilename
     * @return mixed
     */
    public static function css_assets($targetFilename = NULL)
    {
        ob_start();

        self::$renderer->dumpCssAssets();
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
        . '}'
        . '.phpdebugbar-widgets-value.phpdebugbar-widgets-warning { color: #f39c12; }';
        $content = str_replace(['PhpDebugbarFontAwesome', 'phpdebugbar-fa-', '.phpdebugbar-fa'], ['FontAwesome', 'fa-', '.fa'], ob_get_clean());

        if ($targetFilename !== NULL)
        {
            return file_put_contents($targetFilename, $content);
        }
        else
        {
            return $content;
        }
    }

    /**
     * Return all JS assets or write in a file
     * 
     * @param string $targetFilename
     * @return mixed
     */
    public static function js_assets($targetFilename = NULL)
    {
        ob_start();

        self::$renderer->dumpJsAssets();
        $content = str_replace('phpdebugbar-fa', 'fa', ob_get_clean());

        if ($targetFilename !== NULL)
        {
            return file_put_contents($targetFilename, $content);
        }
        else
        {
            return $content;
        }
    }
}
