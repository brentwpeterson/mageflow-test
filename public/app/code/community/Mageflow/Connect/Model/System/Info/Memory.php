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
 * Memory.php
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
 * Mageflow_Connect_Model_System_Info_Memory
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Info_Memory extends Varien_Object
{
    /**
     * Class constructor
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Returns integer bytes of free memory
     *
     * @return int
     */
    public function getFreeMemory()
    {
        $out = 0;
        if (function_exists('exec')) {
            if (Mage::getModel('mageflow_connect/system_info_os')->getOsType()
                == Mageflow_Connect_Model_System_Info_Os::OS_OSX
            ) {
                $cmd = "/usr/bin/top -l 1 | awk '/PhysMem:/ {print $10}'";
                $retval = exec($cmd, $out);
                $memory
                    = (int)$out[0] * 1024 * 1024; //convert megabytes to bytes
                Mage::helper('mageflow_connect/log')->log($memory);
                return $memory;
            } elseif ($this->getOsType()
                == Mageflow_Connect_Model_System_Info_Os::OS_LINUX
            ) {
                $cmd = 'free';
                $retval = exec($cmd, $out);
                Mage::helper('mageflow_connect/log')->log($out);

                if (isset($out[1])) {
                    $retval = preg_match(
                        '/^Mem:\s*(\d*)\s*(\d*)\s*(\d*).*/i',
                        $out[1],
                        $matches
                    );
                    if (is_array($matches) && sizeof($matches) > 1) {
//                        $outarr['total'] = $matches[1];
//                        $outarr['used'] = $matches[2];
//                        $outarr['free'] = $matches[3];
                        return (int)$matches[3];
                    }
                }
            } else {
                return 0;
            }
        }
    }

    /**
     * Returns int bytes of total memory
     * in the machine
     *
     * @return int
     */
    public function getTotalMemory()
    {
        if (function_exists('exec')) {
            if (Mage::getModel('mageflow_connect/system_info_os')->getOsType()
                == Mageflow_Connect_Model_System_Info_Os::OS_OSX
            ) {
                $cmd = '/usr/sbin/sysctl -n hw.memsize';
                $retval = exec($cmd, $out);
                Mage::helper('mageflow_connect/log')->log($cmd);
                $memory = (int)$out[0];
                return $memory;
            } elseif ($this->getOsType()
                == Mageflow_Connect_Model_System_Info_Os::OS_LINUX
            ) {
                return 0;
            }
        }
        return 0;
    }

}
