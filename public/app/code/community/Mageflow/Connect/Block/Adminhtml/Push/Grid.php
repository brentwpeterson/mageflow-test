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
 * Mageflow_Connect_Block_Adminhtml_Migrate_Grid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Push_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * items
     *
     * @var
     */
    private $_items;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('migrationGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
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
        $itemCollection = Mage::getModel('mageflow_connect/changeset_item')
            ->getCollection();
        $this->setCollection($itemCollection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
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
            'type',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Type'),
                'index' => 'type'
            )
        );
        $this->addColumn(
            'preview',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Preview'),
                'index' => 'preview',
                'renderer'
                => 'Mageflow_Connect_Block_Adminhtml_Push_Grid_Column_Renderer',
                'filter' => false
            )
        );
        $this->addColumn(
            'website',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Website'),
                'index' => 'website'
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Created at'),
                'index' => 'created_at'
            )
        );
        $this->addColumn(
            'store',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Store View'),
                'index' => 'store'
            )
        );
        $this->addColumn(
            'status',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Status'),
                'index' => 'status',
                'sortable' => true,
                'type' => 'options',
                'options' => array(
                    Mageflow_Connect_Model_Changeset_Item::STATUS_NEW
                    => Mageflow_Connect_Model_Changeset_Item::STATUS_NEW,
                    Mageflow_Connect_Model_Changeset_Item::STATUS_SENT
                    => Mageflow_Connect_Model_Changeset_Item::STATUS_SENT,
                    Mageflow_Connect_Model_Changeset_Item::STATUS_FAILED
                    => Mageflow_Connect_Model_Changeset_Item::STATUS_FAILED,
                    Mageflow_Connect_Model_Changeset_Item::STATUS_REJECTED
                    => Mageflow_Connect_Model_Changeset_Item::STATUS_REJECTED
                ),
            ),
            'frontend_label'
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
                                'Push'
                            ),
                        'url' => array('base' => '*/*/push'),
                        'field' => 'id'
                    ),
                    array(
                        'caption' => Mage::helper('mageflow_connect')->__(
                                'Apply'
                            ),
                        'url' => array('base' => '*/*/apply'),
                        'field' => 'id'
                    ),
                    array(
                        'caption' => Mage::helper('mageflow_connect')->__(
                                'Discard'
                            ),
                        'url' => array('base' => '*/*/discard'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
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
        $url = $this->getUrl(
            '*/*/*'
        );
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
            'push',
            array(
                'label' => Mage::helper('mageflow_connect')->__(
                        'Push to MageFlow'
                    ),
                'url' => $this->getUrl('*/*/push'),
                'confirm' => Mage::helper('mageflow_connect')->__(
                        'Are you sure you want to
                             push these objects to MageFlow?'
                    )
            )
        );
        $this->getMassactionBlock()->addItem(
            'discard',
            array(
                'label' => Mage::helper('mageflow_connect')->__(
                        'Discard selected'
                    ),
                'url' => $this->getUrl('*/*/discard'),
                'confirm' => Mage::helper('mageflow_connect')->__(
                        'Are you sure you want to discard these changesets?'
                    )
            )
        );
        $this->getMassactionBlock()->addItem(
            'flush',
            array(
                'label' => Mage::helper('mageflow_connect')->__(
                        'Flush all items'
                    ),
                'url' => $this->getUrl('*/*/flush'),
                'confirm' => Mage::helper('mageflow_connect')->__(
                        'Are you sure you want to flush all items?'
                    )
            )
        );

        $this->_exportTypes = array();

        return $this;
    }

}
