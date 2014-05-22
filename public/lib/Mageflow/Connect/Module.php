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
 * Module.php
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

namespace Mageflow\Connect;

define('MODULEROOT', __DIR__);

/**
 * Module
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

final class Module
{

    /**
     * Class constructor
     *
     * @return Module
     */
    public function __construct()
    {
        $this->registerAutoloader();
        $this->registerModule();
        return $this;
    }

    /**
     * register module
     */
    private function registerModule()
    {
        global $moduleRegistry;
        if (!isset($moduleRegistry)) {
            $moduleRegistry = array();
            $moduleRegistry[__CLASS__] = __DIR__;
        }
    }

    /**
     * register autoloader
     */
    private function registerAutoloader()
    {
        spl_autoload_register(array($this, 'autoload'), true, true);
    }

    /**
     * Simple autoloader for Zend2-like module
     *
     * @param string $className
     */
    private function autoload($className)
    {
        if (stristr($className, 'Mageflow\\')) {
            $classPath
                = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR
                . str_replace(
                    '\\',
                    DIRECTORY_SEPARATOR,
                    $className
                ) . '.php';
            $classPath = str_replace('src/Mageflow/Connect', 'src', $classPath);
            include_once $classPath;
        }
    }

    /**
     * Return module's config as array
     *
     * @return array
     */
    public function getConfig()
    {
        return include dirname(__FILE__) . '/config/module.config.php';
    }

}
