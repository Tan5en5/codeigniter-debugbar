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

use Psr\Log\AbstractLogger;

/**
 * Console Class
 * 
 * This class enables you to display messages and exceptions
 * in order to help with debugging and optimization using CodeIgniter Debug Bar.
 *
 * @package     CodeIgniterDebugBar
 * @subpackage  Libraries
 * @category    Libraries
 * @author      Anthony Tansens <a.tansens+github@gmail.com>
 */
class Console extends AbstractLogger
{
    /**
     * Messages and Exceptions container
     *
     * @var array 
     */
    protected $logs = array(
        'messages'   => array(),
        'exceptions' => array()
    );

    /**
     * 
     * @return array
     */
    public function getExeptions()
    {
        return $this->logs['exceptions'];
    }

    /**
     * 
     * @return array
     */
    public function getMessages()
    {
        return $this->logs['messages'];
    }

    /**
     * Log Exception method
     * 
     * @param Exception $exception
     */
    public function exception(Exception $exception)
    {
        $this->log('exception', $exception);
    }

    /**
     * Log method
     * 
     * @param string $level
     * @param mixed $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        $key = ($message instanceof Exception) ? 'exceptions' : 'messages';

        $this->logs[$key][] = array(
            'data' => $message,
            'type' => $level
        );
    }
}
