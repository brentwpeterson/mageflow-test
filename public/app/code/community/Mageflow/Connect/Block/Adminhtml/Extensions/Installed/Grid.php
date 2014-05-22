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
 * Mageflow_Connect_Block_Adminhtml_Extensions_Installed_Grid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Extensions_Installed_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @return $this|this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel(
            'mageflow_connect/extension_collection'
        );

        $this->setCollection($collection);

        return $this;
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
                 'width'  => '50px',
                 'index'  => 'id',
                 'type'   => 'text',
            )
        );

        $this->addColumn(
            'name',
            array(
                 'header' => Mage::helper('mageflow_connect')->__('Name'),
                 'index'  => 'name'
            )
        );
        $this->addColumn(
            'version',
            array(
                 'header' => Mage::helper('mageflow_connect')->__('Version'),
                 'width'  => '150',
                 'index'  => 'version'
            )
        );
        $this->addColumn(
            'channel',
            array(
                 'header' => Mage::helper('mageflow_connect')->__('Channel'),
                 'width'  => '150',
                 'index'  => 'channel'
            )
        );

        $this->addColumn(
            'action',
            array(
                 'header'    => Mage::helper('mageflow_connect')->__('Action'),
                 'width'     => '150',
                 'type'      => 'action',
                 'getter'    => 'getId',
                 'actions'   => array(
                     array(
                         'caption' => Mage::helper('mageflow_connect')->__(
                             'Show details'
                         ),
                         'url'     => array('base' => '*/*/show'),
                         'field'   => 'id'
                     ),
                     array(
                         'caption' => Mage::helper('mageflow_connect')->__(
                             'Upgrade'
                         ),
                         'url'     => array('base' => '*/*/upgrade'),
                         'field'   => 'id'
                     ),
                     array(
                         'caption' => Mage::helper('mageflow_connect')->__(
                             'Uninstall'
                         ),
                         'url'     => array('base' => '*/*/uninstall'),
                         'field'   => 'id'
                     )
                 ),
                 'filter'    => false,
                 'sortable'  => false,
                 'index'     => 'stores',
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

}
