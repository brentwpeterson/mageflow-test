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
 * upgrade-0.2.1-0.2.2.php
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
$installer = $this;

$installer->startSetup();

$tableName = 'mageflow_changeset_item';

if (!$installer->getConnection()->isTableExists(
    $installer->getTable('mageflow_connect/changeset_item')
)
) {
    $table = $installer->getConnection()->newTable($tableName)
        ->addColumn(
            'id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                 'identity' => true,
                 'unsigned' => true,
                 'nullable' => false,
                 'primary'  => true,
            ),
            'Record ID'
        )
        ->addColumn(
            'content',
            Varien_Db_Ddl_Table::TYPE_VARBINARY,
            null,
            array(
                 'nullable' => false,
                 'length'   => Varien_Db_Ddl_Table::MAX_VARBINARY_SIZE,
            ),
            'Changeset content'
        )
        ->addColumn(
            'encoding',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            24,
            array(
                 'nullable' => true,
                 'default'  => 'json'
            ),
            'Encoder/decoder of content'
        )
        ->addColumn(
            'type',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            32,
            array(
                 'nullable' => false,
            ),
            'Entity type of content'
        )
        ->addColumn(
            'status',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            16,
            array(
                 'nullable' => false,
                 'default'  => 'new'
            ),
            'Status of changeset: new, sent, rejected, failed'
        )
        ->addColumn(
            'created_at',
            Varien_Db_Ddl_Table::TYPE_DATETIME,
            null,
            array(
                 'nullable' => false
            ),
            'Creation time'
        )
        ->addColumn(
            'updated_at',
            Varien_Db_Ddl_Table::TYPE_DATETIME,
            null,
            array(
                 'nullable' => false
            ),
            'Update time'
        )
        ->addIndex('ix_updated_at', array('updated_at'))
        ->addIndex('ix_created_at', array('created_at'))
        ->addIndex('ix_type', array('type'));
    $installer->getConnection()->createTable($table);
}
$installer->endSetup();