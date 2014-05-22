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
 * Mageflow_Connect_Block_Adminhtml_Extensions_Available_Grid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Extensions_Available_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class constructor
      */
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('id');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection
     *
     * @return $this|this
     */
    protected function _prepareCollection()
    {
        $limit = (int)$this->getParam(
            $this->getVarNameLimit(),
            $this->_defaultLimit
        );
        $currentPage = (int)$this->getParam(
            $this->getVarNamePage(),
            $this->_defaultPage
        );
        $collection = Mage::getModel('mageflow_connect/client_magepit_api')
            ->getAvailablePackages();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * after load collection
     *
     * @return $this|void
     */
    public function _afterLoadCollection()
    {
        parent::_afterLoadCollection();
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
                             'Install'
                         ),
                         'url'     => array('base' => '*/*/upgrade'),
                         'field'   => 'id'
                     ),
                     array(
                         'caption' => Mage::helper('mageflow_connect')->__(
                             'Show details'
                         ),
                         'url'     => array('base' => '*/*/show'),
                         'field'   => 'id'
                     ),
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
     * get row url
     *
     * @param $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            array(
                 'id' => $row->getId()
            )
        );
    }

}
