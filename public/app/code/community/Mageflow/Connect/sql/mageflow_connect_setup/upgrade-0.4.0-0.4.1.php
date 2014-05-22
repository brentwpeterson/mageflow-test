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
 * upgrade-0.4.0-0.4.1.php
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
 *
 *
 */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();
$setup->addAttribute(
    'catalog_category',
    'mf_guid',
    array(
         'group'        => 'General Information',
         'input'        => 'text',
         'type'         => 'varchar',
         'label'        => 'MF guid',
         'backend'      => '',
         'visible'      => 0,
         'required'     => 0,
         'user_defined' => 1,
         'global'       =>
             Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    )
);
$setup->endSetup();