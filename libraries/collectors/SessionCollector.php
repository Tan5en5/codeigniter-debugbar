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
 * SessionCollector Class
 *
 * @package     CodeIgniterDebugBar
 * @subpackage  Libraries
 * @category    Collectors
 * @author      Anthony Tansens <a.tansens+github@gmail.com>
 */
class SessionCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /**  
     * @var  CI_Session $session 
     */
    protected $session;

    /**
     * 
     * @param CI_Session $session
     */
    public function setSession(CI_Session $session)
    {
        $this->session = $session;
    }

    /** 
     * 
     * @return CI_Session 
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $data = array();

        foreach ($this->getSession()->userdata() as $key => $value) {
            $data[$key] = is_string($value) ? $value : $this->formatVar($value);
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'session';
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgets()
    {
        return array(
            "session" => array(
                "icon" => "archive",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "session",
                "default" => "{}"
            )
        );
    }
}
