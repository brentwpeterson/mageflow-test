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
 * Head.php
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
 * Mageflow_Connect_Block_Adminhtml_Page_Head is a wrapper between
 * normal Mage_Page_Block_Html_Head. It's main task is to avoid errors
 * when we have no scripts to load (elsewhere than under MageFlow pages)crea
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

class Mageflow_Connect_Block_Adminhtml_Page_Head
    extends Mage_Page_Block_Html_Head
{
    /**
     * Overload of mage class to avoid errors when we
     * don't need to load any MageFlow scripts
     *
     * @return string
     */
    public function getCssJsHtml()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        $module = $this->getAction()->getRequest()->getModuleName();
        if ($section == 'mageflow_connect' || $module == 'mageflow_connect') {
            return parent::getCssJsHtml();
        }
    }
}