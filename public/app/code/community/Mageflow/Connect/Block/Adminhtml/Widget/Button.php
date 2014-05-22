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
 * Button.php
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
 * Mageflow_Connect_Block_Adminhtml_Widget_Button
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

class Mageflow_Connect_Block_Adminhtml_Widget_Button
    extends Mage_Adminhtml_Block_Widget_Button
{
    /**
     * custom attributes
     *
     * @var array
     */
    protected $_customAttributes = array();

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Adds custom attribute that will be added to final
     * button tag when rendered
     *
     * @param $name
     * @param $value
     */
    public function addAttribute($name, $value)
    {
        $this->_customAttributes[$name] = $value;
    }

    /**
     * Let's make the standard button a little bit more flexible
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = $this->getBeforeHtml() . '<button '
            . ($this->getId() ? ' id="' . $this->getId() . '"' : '')
            . ($this->getElementName() ?
                ' name="' . $this->getElementName() . '"' : '')
            . ' title="'
            . Mage::helper('core')->quoteEscape(
                $this->getTitle() ? $this->getTitle() : $this->getLabel()
            )
            . '"'
            . ($this->getType() ? ' type="' . $this->getType() . '"' : '')
            . ' class="scalable ' . $this->getClass() . ($this->getDisabled()
                ? ' disabled' : '') . '"'
            . ($this->getOnClick() ? ' onclick="' . $this->getOnClick() . '"'
                : '')
            . ($this->getStyle() ? ' style="' . $this->getStyle() . '"' : '')
            . ($this->getValue() ? ' value="' . $this->getValue() . '"' : '')
            . ($this->getDisabled() ? ' disabled="disabled"' : '');
        foreach ($this->_data as $name => $value) {
            if (substr($name, 0, 4) == 'data') {
                $html .= sprintf(' %s="%s"', $name, $value);
            }
        }
        $html .= '><span><span><span>' . $this->getLabel()
            . '</span></span></span></button>' . $this->getAfterHtml();

        return $html;
    }
}