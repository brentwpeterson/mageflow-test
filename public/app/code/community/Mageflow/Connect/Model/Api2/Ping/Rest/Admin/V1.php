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
 * V1.php
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
 * Mageflow_Connect_Model_Api2_Ping_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Ping_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{

    /**
     * Class constructor
     *
     * @return \Mageflow_Connect_Model_Api2_Ping_Rest_Admin_V1
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * retrieve
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array();
        $out['timestamp'] = time();
        $load = Mage::getModel('mageflow_connect/system_info_cpu')
            ->getSystemLoad();
        $coreCount = Mage::getModel('mageflow_connect/system_info_cpu')
            ->getCpuCores();
        $coreCount = ($coreCount > 0) ? $coreCount : 1;
        $balancedLoad = $load / $coreCount;
        $out['system_load'] = round(
            $balancedLoad,
            2
        );
        $freeDisk = disk_free_space(dirname(__FILE__));
        $totalDisk = disk_total_space(dirname(__FILE__));
        $out['free_disk'] = round(($freeDisk / $totalDisk) * 100, 2);
        $out['active_sessions'] = Mage::getModel(
            'mageflow_connect/system_info_session'
        )
            ->getNumberOfActiveSessions();
        $out['mfx_version'] = Mage::app()->getConfig()
            ->getNode('modules/Mageflow_Connect/version')
            ->asArray();
        return $out;
    }

}
