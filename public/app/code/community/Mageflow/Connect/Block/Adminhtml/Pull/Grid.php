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
 * Grid.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Block_Adminhtml_Pullgrid_Grid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Pull_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * items
     *
     * @var false|Mage_Core_Model_Abstract
     */
    private $_items;

    /**
     * logger
     *
     * @var Mageflow_Connect_Helper_Log
     */
    private $_logger;

    /**
     * get logger
     *
     * @return Mageflow_Connect_Helper_Log
     */
    private function getLogger()
    {
        if (is_null($this->_logger)) {
            $this->_logger = Mage::helper('mageflow_connect/log');
        }
        return $this->_logger;
    }

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_items = Mage::getModel('mageflow_connect/data_collection');
        $this->setId('pullGrid');
        $this->setDefaultSort('id');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Returns item collection
     *
     * @return Varien_Data_Collection
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * prepare collection
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::helper('mageflow_connect/data')->getItemCollectionFromMfApi();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => Mage::helper('mageflow_connect')->__('ID'),
                'width' => '50px',
                'index' => 'id',
                'type' => 'text',
            )
        );
        $this->addColumn(
            'ChangeSet',
            array(
                'header' => Mage::helper('mageflow_connect')->__('ChangeSet'),
                'index' => 'changeset',
                'type' => 'text',
            )
        );
        $this->addColumn(
            'Type',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Type'),
                'index' => 'type',
                'type' => 'text',
            )
        );

        $this->addColumn(
            'action',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('mageflow_connect')->__(
                                'Pull'
                            ),
                        'url' => array('base' => '*/*/pull'),
                        'field' => 'id'
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'id',
                'is_system' => true,
            )
        );

        $this->addExportType(
            '*/*/exportCsv',
            Mage::helper('customer')->__('CSV')
        );
        $this->addExportType(
            '*/*/exportXml',
            Mage::helper('customer')->__('Excel XML')
        );
        return parent::_prepareColumns();
    }

    /**
     * Returns row url
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        $url = $this->getUrl('*/*/*');
        return $url;
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * prepare massaction
     *
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    public function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'pull',
            array(
                'label' => Mage::helper('mageflow_connect')->__(
                        'Pull from MageFlow'
                    ),
                'url' => $this->getUrl('*/*/pull'),
                'confirm' => Mage::helper('mageflow_connect')->__(
                    'Are you sure you want to pull these objects from MageFlow?'
                    )
            )
        );
        $this->_exportTypes = array();
        return $this;
    }

}
