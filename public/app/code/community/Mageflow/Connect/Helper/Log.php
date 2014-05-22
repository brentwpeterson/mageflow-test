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
 * Log.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Helper_Log
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Log extends Mage_Core_Helper_Abstract
{


    /**
     * This method writes log message to modules log file
     * and system.log
     *
     * @param mixed  $message
     * @param string $method
     * @param string $line
     * @param      $level
     */
    public function log(
        $message,
        $method = null,
        $line = null,
        $level = Zend_Log::DEBUG
    )
    {
        $currentLevel = \Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY
        );

        // presuming we use only INFO & DEBUG levels
        // if we have logging on INFO, then log only with level == INFO
        if ($currentLevel == Zend_Log::INFO && $level != Zend_Log::INFO) {
            return;
        }
        if (is_null($method)) {
            $method = __METHOD__;
        }
        if (is_null($line)) {
            $line = __LINE__;
        }
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
            $method = $backtrace[1]['class'] . '::' . $backtrace[1]['function'];
            $line = $backtrace[0]['line'];
        }
        $message = print_r($message, true);
        if (strlen($message) > 1024) {
            $message = substr($message, 0, 1024) . ' ...';
        }
        Mage::log(
            sprintf('%s(%s): %s', $method, $line, $message),
            null,
            'mageflow.log'
        );
    }

}
