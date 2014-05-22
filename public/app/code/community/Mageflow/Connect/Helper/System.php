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
 * System.php
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
 * Mageflow_Connect_Helper_System
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_System extends Mage_Core_Helper_Abstract
{

    /**
     * Clean all Magento caches
     */
    public function cleanCache()
    {
        try {
            $allTypes = Mage::app()->useCache();
            foreach ($allTypes as $type => $blah) {
                Mage::app()->getCacheInstance()->cleanType($type);
            }
        } catch (Exception $e) {
            Mage::helper("mageflow_connect")->log($e->getMessage());
            Mage::helper("mageflow_connect")->log($e->getTraceAsString());
        }
    }

    /**
     * return current cache setting
     * or set cache settings to $settingsArray
     *
     * @param array $settingsArray
     *
     * @return array
     */
    public function cacheSettings($settingsArray = null)
    {
        /*
         * a sample data array
         *
                "block_html": "0",
                "collections": "0",
                "config": "0",
                "config_api": "0",
                "config_api2": "0",
                "eav": "0",
                "layout": "0",
                "translate": "0"
        */
        $currentSettingsArray = Mage::app()->useCache();
        if (is_null($settingsArray)) {
            return $currentSettingsArray;
        }
        if (array_key_exists('all', $settingsArray)) {
            foreach ($currentSettingsArray as $key => $setting) {
                $currentSettingsArray[$key] = $settingsArray['all'];
            }
        } else {
            foreach ($settingsArray as $key => $setting) {
                $currentSettingsArray[$key] = $setting;
            }
        }
        $this->cleanCache();
        Mage::app()->saveUseCache($currentSettingsArray);
        $this->cleanCache();
        $currentSettingsArray = Mage::app()->useCache();
        Mage::helper('mageflow_connect/log')->log(
            sprintf(
                'Applied cache settings: %s',
                print_r($currentSettingsArray, true)
            )
        );
        return $currentSettingsArray;

    }
}