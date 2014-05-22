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
 * Testbutton.php
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
 * Mageflow_Connect_Block_System_Config_Api_Testbutton
 * Creates "test api" button
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_System_Config_Api_Testbutton
    extends Mageflow_Connect_Block_System_Config_Api_Basebutton
{
    /**
     * Creates "test api" button
     *
     * @param type $buttonBlock
     *
     * @return string
     */
    public function getButtonData($buttonBlock)
    {
        $afterHtml = $this->getAfterHtml();

        $data = array(
            'label'        => Mage::helper('mageflow_connect')->__(
                "Test API"
            ),
            'class'        => '',
            'comment'      => 'Test MageFlow API',
            'id'           => "btn_apitest",
            'after_html'   => $this->getAfterHtml(),
            'before_html'  => '',
            'onclick'      => 'javascript:;',
            'data-api-url' => Mage::helper("adminhtml")
                ->getUrl('mageflow_connect/ajax/testapi') . '?isAjax=true'

        );
        return $data;
    }

    /**
     * Returns HTML that is prepended to button
     *
     * @return string
     */
    protected function getBeforeHtml()
    {
        $html
            = <<<HTML
        <div style="    margin-top:5px;">
                Test MageFlow API status
        </div>
HTML;

        return $html;
    }

    /**
     * Returns HTML that is appended to button
     *
     * @return string
     */
    protected function getAfterHtml()
    {
        $link = $this->getSignupUrl();
        $html
            = <<<HTML
<div>
    <ul>
        <li>Connection status: <span id="api_test_status">n/a</span></li>
        <li>Name: <span id="api_test_name">n/a</span></li>
        <li>Email: <span id="api_test_email">n/a</span></li>
    </ul>
</div>

HTML;

        return $html;
    }

}