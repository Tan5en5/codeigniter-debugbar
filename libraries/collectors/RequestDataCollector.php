<?php
/**
 * CodeIgniter PHP Debug Bar
 *
 * @package		CodeIgniter PHP Debug Bar
 * @author		Anthony Tansens <atansens@gac-technology.com>
 * @license     http://opensource.org/licenses/MIT MIT
 * @link		http://www.gac-technology.com
 * @since		Version 1.0
 * @filesource
 */ 
defined('BASEPATH') OR exit('No direct script access allowed');

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;

/**
 * Description of SessionCollector
 *
 * @package		CodeIgniter PHP Debug Bar
 * @subpackage	Libraries
 * @category	Collectors
 * @author      Anthony Tansens <atansens@gac-technology.com>
 */
class RequestDataCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    protected $globals = array();
    protected $additional_infos = array();
    protected $available_globals = array(
        'get'    => null, 
        'post'   => null, 
        'server' => array(
            'HTTP_ACCEPT', 
            'HTTP_USER_AGENT', 
            'HTTP_CONNECTION', 
            'SERVER_PORT', 
            'SERVER_NAME', 
            'REMOTE_ADDR', 
            'SERVER_SOFTWARE', 
            'HTTP_ACCEPT_LANGUAGE', 
            'SCRIPT_NAME', 
            'REQUEST_METHOD',
            'HTTP_HOST', 
            'REMOTE_HOST', 
            'CONTENT_TYPE', 
            'SERVER_PROTOCOL', 
            'QUERY_STRING', 
            'HTTP_ACCEPT_ENCODING', 
            'HTTP_X_FORWARDED_FOR', 
            'HTTP_DNT'
        )
    );

    public function setGlobal($global = null)
    {
        if (!empty($global) && !isset($this->globals[$global])) {
            $this->globals[$global] = '_'.strtoupper($global);
        }
    }

    public function setAdditionalInfos($key = null, $value = null)
    {
        if (isset($key) && isset($value) && !isset($this->additional_infos[(string) $key])) {
            $this->additional_infos[(string) $key] = $this->getDataFormatter()->formatVar((string) $value);
        }
    }

    protected function filteredGlobal($global = null, $filters = array())
    {
        $filtered_global = array();
        
        foreach ($GLOBALS[$global] as $global_key => $global_value) {
            if (in_array($global_key, $filters)) {
                $filtered_global[$global_key] = $global_value;
            }
        }

        return $filtered_global;
    }

    public function collect()
    {
        $data = array();

        foreach ($this->globals as $k => $global) {
            if (isset($this->available_globals[$k])) {
                $values = $this->filteredGlobal($global, $this->available_globals[$k]);
            } else {
                $values = $GLOBALS[$global];
            }
            $data["$" . $global] = $this->getDataFormatter()->formatVar($values);
        }

        return array_merge($data, $this->additional_infos);
    }
    

    public function getName()
    {
        return 'request';
    }

    public function getWidgets()
    {
        $widgets = array(
            "request" => array(
                "icon" => "tags",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "request",
                "default" => "{}"
            )
        );

        return $widgets;
    }
}

/* End of file RequestDataCollector.php */
/* Location: ./codeigniter-debugbar/librairies/collectors/RequestDataCollector.php */