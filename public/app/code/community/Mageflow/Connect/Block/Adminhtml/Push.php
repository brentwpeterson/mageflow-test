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
 * Push.php
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
 * Mageflow_Connect_Block_Adminhtml_Push
 * This class is used to display migration grid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Push
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->removeButton('add');
        $this->_controller = 'adminhtml_push';
        $this->_blockGroup = 'mageflow_connect';
        $this->_headerText = Mage::helper('mageflow_connect')->__(
            'Push changes'
        );

        /**
         * @var Mageflow_Connect_Helper_Type $typeHelper
         */
        $typeHelper = Mage::helper('mageflow_connect/type');

        if ($typeHelper->isTypeEnabled('media_file')) {
            $this->_addButton(
                'refresh',
                array(
                     'label'   => 'Refresh Media Index',
                     'onclick' => 'new Ajax.Request(\'' .
                     $this->getUrl('*/*/refreshmediaindex') . '\')',
                     'class'   => '',
                )
            );
        }
    }
}
