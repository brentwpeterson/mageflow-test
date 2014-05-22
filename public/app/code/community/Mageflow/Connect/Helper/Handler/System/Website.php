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
 * Website.php
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
 * Mageflow_Connect_Helper_Handler_System_Website
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Handler_System_Website
    extends Mageflow_Connect_Helper_Handler_Abstract
{


    /**
     * create or update website from changeset
     * all used categories must already exist with correct mf_guid's
     *
     * @param $filteredData
     *
     * @throws Exception
     * @return array|null
     */
    public function handle($filteredData)
    {
        $categoryIdList = array();
        foreach ($filteredData['groups'] as $group) {
            $categoryIdList[] = $group['root_category_id'];
        }
        $catalogCollection = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('mf_guid', $categoryIdList);

        $foundCategories = $catalogCollection->getSize();

        $websiteEntity = Mage::getModel('core/website')
            ->load($filteredData['code'], 'code');

        $originalData = null;
        if (!is_null($websiteEntity)) {
            $originalData = $websiteEntity->getData();
        }

        $websiteEntity->setCode($filteredData['code']);
        $websiteEntity->setName($filteredData['name']);
        $websiteEntity->setSortOrder($filteredData['sort_order']);
        $websiteEntity->setIsDefault($filteredData['is_default']);
        $websiteEntity->save();

        Mage::helper('mageflow_connect/log')->log(
            sprintf(
                'Saved website with ID %s',
                print_r($websiteEntity->getId(), true)
            )
        );

        foreach ($filteredData['groups'] as $group) {
            $groupCollection = Mage::getModel('core/store_group')
                ->getCollection()
                ->addFieldToFilter('name', $group['name'])
                ->addFieldToFilter(
                    'website_id',
                    $websiteEntity->getWebsiteId()
                );

            $groupEntity = Mage::getModel('core/store_group')
                ->load($groupCollection->getFirstItem()->getGroupId());

            $groupEntity->setName($group['name']);

            $catalogCollection = Mage::getModel('catalog/category')
                ->getCollection()
                ->addFieldToFilter('mf_guid', $group['root_category_id']);
            $rootCategory = $catalogCollection->getFirstItem();
            $groupEntity->setRootCategoryId($rootCategory->getEntityId());
            $groupEntity->setWebsiteId($websiteEntity->getWebsiteId());
            $groupEntity->save();

            if ($groupEntity->getName() == $filteredData['default_group_id']) {
                $websiteEntity->setDefaultGroupId($groupEntity->getGroupId());
                $websiteEntity->save();
            }

            foreach ($group['stores'] as $store) {
                $storeEntity = Mage::getModel('core/store')
                    ->load($store['code'], 'code');

                $storeEntity->setCode($store['code']);
                $storeEntity->setName($store['name']);
                $storeEntity->setSortOrder($store['sort_order']);
                $storeEntity->setIsActive($store['is_active']);
                $storeEntity->setWebsiteId($websiteEntity->getWebsiteId());
                $storeEntity->setGroupId($groupEntity->getGroupId());
                $storeEntity->save();

                if ($storeEntity->getCode() == $group['default_store_id']) {
                    $groupEntity->setDefaultStoreId($storeEntity->getStoreId());
                }

            }
        }
        Mage::helper('mageflow_connect/log')->log(get_class($websiteEntity));

        if ($websiteEntity instanceof Mage_Core_Model_Website) {
            return array(
                'entity'        => $websiteEntity,
                'original_data' => $originalData
            );
        }
        Mage::helper('mageflow_connect/log')->log(
            "Error occurred while tried to save Website. Data follows:\n"
            . print_r($filteredData, true)
        );
        return null;

    }

    /**
     * pack content
     *
     * @param $content
     *
     * @return array|mixed
     */
    public function packContent($content)
    {
        $website = Mage::getModel('core/website')
            ->load($content['website_id']);

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
        return $content;
    }

}