<?php

/**
 * MageFlow
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageflow.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * If you wish to use the MageFlow Connect extension as part of a paid
 * service please contact licence@mageflow.com for information about
 * obtaining an appropriate licence.
 */

/**
 * Logger.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

namespace Mageflow\Connect\Helper;

/**
 * Logger
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Logger
{

    /**
     * Logs debug level messages
     *
     * @param mixed   $message
     * @param string  $method
     * @param integer $line
     */
    public function debug($message, $method = null, $line = null)
    {

        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
            $method = $backtrace[1]['class'] . '::' . $backtrace[1]['function'];
            $line = $backtrace[0]['line'];
        }

        if (is_null($method)) {
            $method = __METHOD__;
        }
        if (is_null($line)) {
            $line = __LINE__;
        }


        $this->writelog($message, $method, $line, \Zend_Log::DEBUG);
    }

    /**
     * write log
     *
     * @param type $message
     * @param type $method
     * @param type $line
     * @param type $level
     */
    private function writelog($message, $method, $line, $level)
    {


        $apiLog = \Mage::getBaseDir('var') . '/log/api.log';
        $systemLog = \Mage::getBaseDir('var') . '/log/system.log';

        $message = print_r($message, true);

        if (strlen($message) > 1024) {
            $message = substr($message, 0, 1024) . ' ...';
        }
        if (@touch($apiLog)) {
            $moduleWriter = new \Zend_Log_Writer_Stream($apiLog);
            $logger1 = new \Zend_Log($moduleWriter);
            $logger1->log(
                sprintf(
                    '%s(%s): %s',
                    $method,
                    $line,
                    $message
                ),
                $level
            );
        }

        if (@touch($systemLog)) {
            $globalWriter = new \Zend_Log_Writer_Stream($systemLog);
            $logger2 = new \Zend_Log($globalWriter);
            $logger2->log(
                sprintf(
                    '%s(%s): %s',
                    $method,
                    $line,
                    $message
                ),
                $level
            );
        }
    }

    /**
     * Log error level messages
     *
     * @param mixed $message
     */
    public function error($message)
    {
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
            $method = $backtrace[1]['class'] . '::' . $backtrace[1]['function'];
            $line = $backtrace[0]['line'];
        }

        if (is_null($method)) {
            $method = __METHOD__;
        }
        if (is_null($line)) {
            $line = __LINE__;
        }
        $this->writelog($message, $method, $line, \Zend_Log::ERR);
    }

}