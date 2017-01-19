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
 * IncludedFileCollector Class
 *
 * @package     CodeIgniterDebugBar
 * @subpackage  Libraries
 * @category    Collectors
 * @author      Anthony Tansens <a.tansens+github@gmail.com>
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

        foreach ($data as $file_path) {
            // Include only APPPATH
            if (strpos($file_path, APPPATH) !== false && strpos($file_path, APPPATH.'third_party') === false) {
                $file = str_replace(APPPATH, '', $file_path);
                $files[] = array(
                    'message' => "'".$file."',",
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
