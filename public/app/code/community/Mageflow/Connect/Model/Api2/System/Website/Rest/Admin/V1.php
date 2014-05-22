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
 * Mageflow_Connect_Model_Api2_System_Website_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Website_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_website';

    /**
     * Class constructor
     *
     * @return \Mageflow_Connect_Model_Api2_System_Website_Rest_Admin_V1
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {
        $items = array();
        $websiteCollection = Mage::getModel('core/website')->getCollection();

        foreach ($websiteCollection as $website) {
            $content = $website->getData();
            $groups = array();
            $groupCollection = Mage::getModel('core/store_group')
                ->getCollection()
                ->addFieldToFilter('website_id', $website->getWebsiteId());

            foreach ($groupCollection as $group) {
                $stores = array();
                $storeCollection = Mage::getModel('core/store')
                    ->getCollection()
                    ->addFieldToFilter('group_id', $group->getGroupId());

                foreach ($storeCollection as $store) {
                    $storeData = $store->getData();
                    unset($storeData['store_id']);
                    unset($storeData['website_id']);
                    unset($storeData['group_id']);

                    $stores[] = $storeData;
                }

                $groupData = $group->getData();
                unset($groupData['website_id']);
                unset($groupData['group_id']);
                $groupData['stores'] = $stores;
                $rootCategory = Mage::getModel('catalog/category')
                    ->load($groupData['root_category_id']);
                $defaultStore = Mage::getModel('core/store')
                    ->load($groupData['default_store_id']);

                $groupData['root_category'] = $rootCategory->getUrlKey();
                $groupData['root_category_id'] = $rootCategory->getMfGuid();
                $groupData['default_store_id'] = $defaultStore->getCode();
                $groups[] = $groupData;
            }

            $content = $website->getData();
            $content['groups'] = $groups;
            unset($content['website_id']);

            $defaultGroup = Mage::getModel('core/store_group')
                ->load($content['default_group_id']);
            $content['default_group_id'] = $defaultGroup->getName();

            $items[] = $content;
        }
        return $items;
    }

    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        return $this->_retrieve();
    }

    /**
     * update
     *
     * @param array $filteredData
     *
     * @return mixed
     */
    public function _update(array $filteredData)
    {
        return $this->_create($filteredData);
    }

    /**
     * create
     *
     * @param array $filteredData
     *
     * @return mixed
     */
    public function _create(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log(
            sprintf('%s', print_r($filteredData, true))
        );
        $originalCacheSettings = Mage::helper('mageflow_connect/system')
            ->cacheSettings();

        Mage::helper('mageflow_connect/system')->cacheSettings(
            array('all' => 0)
        );

        //we shouldn't have any original data in case of creation
        $originalData = null;
        $handlerReturnArray = Mage::helper(
            'mageflow_connect/handler_system_website'
        )
            ->handle($filteredData);

        if (is_null($handlerReturnArray)) {
            $this->_error("Could not save System Website.", 500);
        }

        $entity = $handlerReturnArray['entity'];
        $originalData = $handlerReturnArray['original_data'];

        $rollbackFeedback = array();
        // send overwritten data to mageflow
        if (!is_null($originalData)) {
            $rollbackFeedback = $this->sendRollback(
                str_replace('_', ':', $this->_resourceType),
                $filteredData,
                $originalData
            );
        }
        $out = $entity->getData();
        Mage::helper('mageflow_connect/system')->cacheSettings(
            $originalCacheSettings
        );
        $this->_successMessage("Successfully saved System Website", 0, $out);
        Mage::helper('mageflow_connect/log')->log(
            sprintf('%s', print_r($out, true))
        );
        return $out;
    }

}