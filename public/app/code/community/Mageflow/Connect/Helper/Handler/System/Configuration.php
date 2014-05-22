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
 * Configuration.php
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
 * Mageflow_Connect_Helper_Handler_System_Configuration
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Handler_System_Configuration
    extends Mageflow_Connect_Helper_Handler_Abstract
{

    /**
     * create or update core/config_data from data array
     *
     * @param $filteredData
     *
     * @return array|null
     */
    public function handle($filteredData)
    {
        $itemModel = null;

        switch ($filteredData['scope']) {
            case 'default':
                $oldValue = Mage::app()->getStore()
                    ->getConfig($filteredData['path']);
                Mage::helper('mageflow_connect/log')->log($oldValue);
                $scopeId = 0;
                break;
            case 'websites':
                $website = Mage::getModel('core/website')
                    ->load($filteredData['website_code'], 'code');
                $oldValue = $website->getConfig($filteredData['path']);
                Mage::helper('mageflow_connect/log')->log($oldValue);
                $scopeId = $website->getWebsiteId();
                break;
            case 'stores':
                $store = Mage::getModel('core/store')
                    ->load($filteredData['store_code'], 'code');
                $oldValue = $store->getConfig($filteredData['path']);
                Mage::helper('mageflow_connect/log')->log($oldValue);
                $scopeId = $store->getStoreId();
                break;
        }

        $originalData = null;
        if (!is_null($oldValue)) {
            $originalData = $filteredData;
            $originalData['value'] = $oldValue;
        }

        Mage::helper('mageflow_connect/log')->log($scopeId);
        try {
            Mage::getModel('core/config')->saveConfig(
                $filteredData['path'],
                $filteredData['value'],
                $filteredData['scope'],
                $scopeId
            );
            Mage::helper('mageflow_connect/log')
                ->log('Config saved');
            return array(
                'entity'        => $filteredData,
                'original_data' => $originalData
            );
        } catch (Exception $e) {
            Mage::helper('mageflow_connect/log')->log(
                sprintf(
                    'Error occurred while saving item: %s',
                    $e->getMessage()
                )
            );
        }
        return null;
    }

    /**
     * pack content
     *
     * @param $content
     *
     * @return array
     */
    public function packContent($content)
    {
        Mage::helper('mageflow_connect/log')->log(print_r($content, true));
        $cleanedContent = array
        (
            'group_id'     => isset($content['group_id'])?$content['group_id']:null,
            'store_code'   => isset($content['store_code'])?$content['store_code']:null,
            'website_code' => isset($content['website_code'])?$content['website_code']:null,
            'scope'        => isset($content['scope'])?$content['scope']:null,
            'scope_id'     => isset($content['scope_id'])?$content['scope_id']:null,
            'path'         => isset($content['path'])?$content['path']:null,
            'value'        => isset($content['value'])?$content['value']:null,
            'updated_at'   => isset($content['updated_at'])?$content['updated_at']:null,
            'created_at'   => isset($content['created_at'])?$content['created_at']:null,
            'mf_guid'      => isset($content['mf_guid'])?$content['mf_guid']:null,
        );
        $content = $cleanedContent;
        return $content;
    }

}