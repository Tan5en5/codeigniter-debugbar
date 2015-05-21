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
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;

/**
 * IncludedFileCollector Class
 *
 * @package     CodeIgniterDebugBar
 * @subpackage  Libraries
 * @category    Collectors
 * @author      Anthony Tansens <atansens@gac-technology.com>
 */
class IncludedFileCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $files = array();
        $data = get_included_files();
        $len = strlen(APPPATH);

        foreach ($data as $file_path) {
            // Include only APPPATH
            if (substr($file_path, 0, $len) === APPPATH && substr($file_path, 0, $len + 11) !== APPPATH.'third_party') {
                $file = substr($file_path, $len);
                $files[] = array(
                    'message' => "'" . APPPATH . $file . "',",
                    'is_string' => true,
                );
            }
        }

        return array(
            'messages' => $files,
            'count' => count($files),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgets()
    {
        return array(
            "file" => array(
                "icon" => "files-o",
                "widget" => "PhpDebugBar.Widgets.MessagesWidget",
                "map" => "file.messages",
                "default" => "{}"
            ),
            "file:badge" => array(
                "map" => "file.count",
                "default" => "null"
            )
        );
    }
}
