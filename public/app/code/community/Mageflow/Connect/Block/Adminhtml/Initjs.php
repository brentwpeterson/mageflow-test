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
 * Initjs.php
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
 * Mageflow_Connect_Block_Adminhtml_Initjs
 * block for MageFlow backend that loads custom JS
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Initjs
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Include JS in the head if section is Mageflow
     */
    protected function _prepareLayout()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        $module = $this->getAction()->getRequest()->getModuleName();
        if ($section == 'mageflow_connect' || $module == 'mageflow_connect') {
            $this->getLayout()
                ->getBlock('head')
                ->addCss('mageflow/styles.css');

            $this->getLayout()
                ->getBlock('mageflow_js_container')
                ->addJs('mageflow/jquery.js')
                ->addJs('mageflow/noconflict.js')
                ->addJs('mageflow/core.js');
        }
        parent::_prepareLayout();
    }

    /**
     * to html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'mageflow_connect') {
            return parent::_toHtml();
        } else {
            return '';
        }
    }

    /**
     * get module version
     *
     * @return string
     */
    public function getModuleVersion()
    {
        return (string)Mage::getConfig()->getNode(
        )->modules->Mageflow_Connect->version;
    }

}
