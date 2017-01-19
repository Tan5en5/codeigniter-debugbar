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

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;

/**
 * CodeIgniterRequestCollector Class
 *
 * @package     CodeIgniterDebugBar
 * @subpackage  Libraries
 * @category    Collectors
 * @author      Anthony Tansens <a.tansens+github@gmail.com>
 */
class CodeIgniterRequestCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /**
     *
     * @var array
     */
    protected $data = array();

    /**
     * List of authorize $_SERVER variables
     *
     * @var array 
     */
    protected $server_vars = array(
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
    );

    /**
     * 
     * @param CI_Input $input
     * @param type $key
     * @return \CodeIgniterRequestCollector
     */
    public function setRequestData(CI_Input $input, $key = null)
    {
        if (method_exists($input, $key)) {
            $value = $input->$key(($key === 'server') ? $this->server_vars : null);
            $this->data['$_' . strtoupper($key)] = $this->getDataFormatter()->formatVar($value);
        }

        return $this;
    }

    /**
     * 
     * @param type $key
     * @param type $value
     * @return \CodeIgniterRequestCollector
     */
    public function setAdditionalData($key = null, $value = null)
    {
        $this->data[$key] = $this->getDataFormatter()->formatVar($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        return $this->data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'request';
    }

    /**
     * {@inheritdoc}
     */
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
