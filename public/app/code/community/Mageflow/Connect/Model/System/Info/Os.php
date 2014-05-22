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
 * Os.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_System_Info_Os
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Info_Os extends Varien_Object
{

    const OS_OSX = 'osx';
    const OS_LINUX = 'linux';

    /**
     * os type
     *
     * @var
     */
    private $_osType;

    /**
     * Class constructor
     *
     * @return Os
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * Detects and returns OS type
     *
     * @return string OS Type
     */
    public function getOsType()
    {
        if (is_null($this->_osType)) {
            switch (php_uname('s')) {
                case 'Darwin':
                    $this->_osType = self::OS_OSX;
                    break;
                case 'Linux':
                    $this->_osType = self::OS_LINUX;
                default:
                    $this->_osType = 'N/A';
                    break;
            }
        }
        return $this->_osType;
    }

}
