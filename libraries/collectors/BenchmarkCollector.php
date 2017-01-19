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

use DebugBar\DataCollector\TimeDataCollector;

/**
 * BenchmarkCollector Class
 *
 * @package     CodeIgniterDebugBar
 * @subpackage  Libraries
 * @category    Collectors
 * @author      Anthony Tansens <a.tansens+github@gmail.com>
 */
class BenchmarkCollector extends TimeDataCollector
{
    /**
     * Adds a CI Benchmark measure
     *
     * @param CI_Benchmark $benchmark
     */
    public function addBenchmarkMeasure(CI_Benchmark $benchmark)
    {
        $markers = array();
        $this->requestStartTime = $benchmark->marker['total_execution_time_start'];

        foreach ($benchmark->marker as $key => $val) {
            // We match the "end" marker so that the list ends
            // up in the order that it was defined
            if (preg_match('/(.+?)_end$/i', $key, $match)
                && isset($benchmark->marker[$match[1].'_end'], $benchmark->marker[$match[1].'_start'])
                && $match[1] !== 'total_execution_time'
            ) {
                $markers[$match[1]] = array(
                    'start' => $benchmark->marker[$match[1].'_start'], 
                    'end' => $benchmark->marker[$key]
                );
            }
        }

        foreach ($markers as $label => $marker) {
            $this->addMeasure($label, $marker['start'], $marker['end']);
        }
    }
}
