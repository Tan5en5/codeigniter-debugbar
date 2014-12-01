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

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * CodeIgniterCollector Class
 *
 * @package     CodeIgniterDebugBar
 * @subpackage  Libraries
 * @category    Collectors
 * @author      Anthony Tansens <atansens@gac-technology.com>
 */
class CodeIgniterCollector extends DataCollector implements Renderable
{
    /** 
     * CodeIgniter instance
     * 
     * @var CI_Controller $CI 
     */
    protected $CI;

    /**
     * 
     * @param CI_Controller $CI
     */
    public function setCI(CI_Controller $CI = null)
    {
        $this->CI = $CI;
    }

    public function collect()
    {
        return array(
            "version" => CI_VERSION,
            "environment" => ENVIRONMENT,
            "locale" => $this->CI->config->item('language'),
        );
    }

    public function getName()
    {
        return 'codeigniter';
    }

    public function getWidgets()
    {
        return array(
            "version" => array(
                "icon" => "github",
                "tooltip" => "Version",
                "map" => "codeigniter.version",
                "default" => ""
            ),
            "environment" => array(
                "icon" => "desktop",
                "tooltip" => "Environment",
                "map" => "codeigniter.environment",
                "default" => ""
            ),
            "locale" => array(
                "icon" => "flag",
                "tooltip" => "Current locale",
                "map" => "codeigniter.locale",
                "default" => "",
            ),
        );
    }
}

/* End of file CodeIgniterCollector.php */
/* Location: ./codeigniter-debugbar/librairies/collectors/CodeIgniterCollector.php */