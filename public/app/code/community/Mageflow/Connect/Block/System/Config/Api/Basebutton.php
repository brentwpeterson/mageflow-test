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
 * BaseButton.php
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
 * Mageflow_Connect_Block_System_Config_Api_Basebutton
 * BaseButton class that is used to generate buttons
 * in admin config section
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
abstract class Mageflow_Connect_Block_System_Config_Api_Basebutton
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * dummy
     *
     * @var
     */
    protected $_dummy;
    /**
     * field renderer
     *
     * @var
     */
    protected $_fieldRenderer;

    /**
     * render
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $buttonBlock = $element->getForm()->getParent()->getLayout()
            ->createBlock('adminhtml/widget_button');
        $data = $this->getButtonData($buttonBlock);

        $id = $element->getHtmlId();

        $html = sprintf(
            '<tr><td class="label"><label for="%s">%s</label></td>',
            $id,
            $element->getLabel()
        );
        $html .= sprintf(
            '<td class="value">%s</td>',
            $buttonBlock->setData($data)->toHtml()
        );
        $html .= '</tr>';
        return $html;
    }

    /**
     * get button data
     *
     * @param $buttonBlock
     *
     * @return mixed
     */
    public abstract function getButtonData($buttonBlock);

    /**
     * get dummy element
     */
    protected function _getDummyElement()
    {
        if (empty($this->_dummy)) {
            $this->_dummy = new Varien_Object(
                array('show_in_default' => 1,
                      'show_in_website' => 0,
                      'show_in_store'   => 0)
            );
        }
        return $this->_dummy;
    }

    /**
     * get field renderer
     */
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton(
                'adminhtml/system_config_form_field'
            );
        }
        return $this->_fieldRenderer;
    }

}
