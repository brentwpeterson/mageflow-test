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
 * upgrade-0.3.9-0.4.0.php
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

/* @var $installer Mageflow_Connect_Model_Resource_Setup */
/**
 * This update script adds special "unique id" column to each entity
 * that is manageable by MageFlow. Currently these entities are:
 * - cms block
 * - cms page
 * - configuration item
 * - catalog category
 * - backend user
 * - oauth consumers
 * - product attribute
 * - product attribute set
 * - product attribute group
 */
$installer = $this;

$installer->startSetup();

//just in case to ensure class loading ...
$dummy = Mage::getModel('mageflow_connect/types_supported');
$tablesToBeChecked =
    Mageflow_Connect_Model_Types_Supported::getSupportedTypes();

$guidColumn = 'mf_guid';
$updatedAtColumn = 'updated_at';
$createdAtColumn = 'created_at';

foreach ($tablesToBeChecked as $tableName) {
    //check for table because we have some non-table types, too
    if ($installer->tableExists($tableName)) {
        //add GUID column
        if (!$installer->getConnection()
            ->tableColumnExists($tableName, $guidColumn)
        ) {
            $installer->getConnection()->addColumn(
                $installer->getConnection()->getTableName($tableName),
                $guidColumn,
                'VARCHAR(64) NULL'
            );
            $installer->getConnection()->addIndex(
                $tableName,
                'ix_' . $guidColumn,
                array($guidColumn),
                Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
            );
        }
        //add created_at column
        if (!$installer->getConnection()
            ->tableColumnExists($tableName, $createdAtColumn)
        ) {
            $installer->getConnection()->addColumn(
                $installer->getConnection()->getTableName($tableName),
                $createdAtColumn,
                'DATETIME NULL'
            );
        }
        //add updated_at column
        if (!$installer->getConnection()->tableColumnExists(
            $tableName,
            $updatedAtColumn
        )
        ) {
            $installer->getConnection()->addColumn(
                $installer->getConnection()->getTableName($tableName),
                $updatedAtColumn,
                'DATETIME NULL'
            );
        }
    }
}

$installer->endSetup();