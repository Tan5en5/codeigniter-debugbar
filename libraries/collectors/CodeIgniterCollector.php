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
use DebugBar\DataCollector\Renderable;

/**
 * CodeIgniterCollector Class
 *
 * @package     CodeIgniterDebugBar
 * @subpackage  Libraries
 * @category    Collectors
 * @author      Anthony Tansens <a.tansens+github@gmail.com>
 */
class CodeIgniterCollector extends DataCollector implements Renderable
{
    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        return array(
            "version"       => CI_VERSION,
            "environment"   => ENVIRONMENT,
            "locale"        => config_item('language'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'codeigniter';
    }

    /**
     * {@inheritdoc}
     */
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
