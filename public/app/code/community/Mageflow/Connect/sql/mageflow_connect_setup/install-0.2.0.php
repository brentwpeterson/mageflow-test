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
 * install-0.2.0.php
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

$tableName = 'mageflow_performance_history';
if (!$installer->tableExists($tableName)) {
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
            'request_path',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                 'nullable' => false,
            ),
            'Request path'
        )
        ->addColumn(
            'memory',
            Varien_Db_Ddl_Table::TYPE_BIGINT,
            null,
            array(
                 'nullable' => false,
            ),
            'Current memory usage in bytes'
        )
        ->addColumn(
            'sessions',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                 'nullable' => false,
            ),
            'Number of active sessions'
        )
        ->addColumn(
            'cpu_load',
            Varien_Db_Ddl_Table::TYPE_FLOAT,
            null,
            array(
                 'nullable' => false,
            ),
            'Current CPU load'
        )
        ->addColumn(
            'created_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(
                 'nullable' => false
            ),
            'Creation time'
        )
        ->addIndex('ix_request_path', array('request_path'))
        ->addIndex('ix_created_at', array('created_at'))
        ->addIndex(
            'ix_request_path_created_at',
            array('request_path', 'created_at')
        );
    $installer->getConnection()->createTable($table);
}
$installer->endSetup();