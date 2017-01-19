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

use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;

/**
 * QueryCollector Class
 *
 * @package     CodeIgniterDebugBar
 * @subpackage  Libraries
 * @category    Collectors
 * @author      Anthony Tansens <a.tansens+github@gmail.com>
 */
class QueryCollector extends DataCollector implements DataCollectorInterface, Renderable, AssetProvider
{
    /** @var array */
    protected $dbs = array();

    /**
     * 
     * @param CI_Controller $ci
     */
    public function setDbs(CI_Controller $ci)
    {
        // Let's determine which databases are currently connected to
        foreach (get_object_vars($ci) as $name => $cobject) {
            if (is_object($cobject) && $cobject instanceof CI_DB) {
                $this->dbs[get_class($ci).':$'.$name] = $cobject;
            } elseif (is_object($cobject) && $cobject instanceof CI_Model) {
                foreach (get_object_vars($cobject) as $mname => $mobject) {
                    if ($mobject instanceof CI_DB) {
                        $this->dbs[get_class($cobject).':$'.$mname] = $mobject;
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $queries = array();
        $totalExecTime = 0;

        foreach ($this->dbs as $q) {
            foreach ($q->queries as $key => $val) {
                $queries[] = array(
                    'sql' => $val,
                    //'params' => (object) $q['params'],
                    'connection' => $q->database,
                    'duration' => $q->query_times[$key],
                    'duration_str' => $this->formatDuration($q->query_times[$key])
                );
                $totalExecTime += $q->query_times[$key];
            }
        }

        return array(
            'nb_statements' => count($queries),
            'accumulated_duration' => $totalExecTime,
            'accumulated_duration_str' => $this->formatDuration($totalExecTime),
            'statements' => $queries
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ciquerybuilder';
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgets()
    {
        return array(
            "database" => array(
                "icon" => "arrow-right",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "ciquerybuilder",
                "default" => "[]"
            ),
            "database:badge" => array(
                "map" => "ciquerybuilder.nb_statements",
                "default" => 0
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAssets()
    {
        return array(
            'css' => 'widgets/sqlqueries/widget.css',
            'js' => 'widgets/sqlqueries/widget.js'
        );
    }
}
