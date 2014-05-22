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
 * Info.php
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
 * Mageflow_Connect_Model_System_Info
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Info extends Varien_Object
{

    const PERFORMANCE_HISTORY_DISPLAY_ITEMS = 10;

    /**
     * Class constructor
     *
     * @return Info
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * Returns array with request/memory/cpu/sessions history
     *
     * @return array
     */
    public function getPerformanceHistory()
    {
        $memoryUsageModelCollection = Mage::getModel(
            'mageflow_connect/system_info_performance'
        )
            ->getCollection()->setPageSize(
                self::PERFORMANCE_HISTORY_DISPLAY_ITEMS
            );
        $memoryUsageModelCollection->addOrder('created_at', 'DESC');
        $out = $memoryUsageModelCollection->toArray();
        return $out['items'];
    }

}
