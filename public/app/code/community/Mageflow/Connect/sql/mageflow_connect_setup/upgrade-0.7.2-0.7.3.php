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
 * upgrade-0.7.2-0.7.3.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Sql Install & Upgrade
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

/**
 * This updatge script adds new attribute , mf_guid to catalog/category
 */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();
$collection = Mage::getModel('catalog/category')->getCollection();
$collection->addFieldToFilter('parent_id', 0);
$collection->load();
$absoluteRoot = $collection->getFirstItem();

Mage::log(
    sprintf(
        '%s(%s): Found absolute root ID: %s',
        __METHOD__,
        __LINE__,
        $absoluteRoot->getId()
    )
);

$collection = Mage::getModel('catalog/category')->getCollection();
$collection->addFieldToFilter('parent_id', $absoluteRoot->getId());
$collection->load();

foreach ($collection as $categoryEntity) {
    if ($categoryEntity->getParentId() == $absoluteRoot->getId()) {
        $mfguid = md5($categoryEntity->getPath());
        $categoryEntity->setMfGuid($mfguid);
        $categoryEntity->save();
    }
}

$setup->endSetup();