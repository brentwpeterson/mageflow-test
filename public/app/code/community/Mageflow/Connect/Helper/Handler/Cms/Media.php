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
 * Media.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Helper_Handler_Cms_Media
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Handler_Cms_Media
    extends Mageflow_Connect_Helper_Handler_Abstract
{


    /**
     * Create changeset item from Mageflow_Connect_Model_Media_Index
     *
     * @param $content
     *
     * @return array|void
     */
    public function packContent($content)
    {
        Mage::helper('mageflow_connect/log')->log(print_r($content, true));
        $content['hex'] = bin2hex(file_get_contents($content['filename']));
        return $content;
    }

    /**
     * update or create CMS Media
     *
     * @param $filteredData
     *
     * @return array|null
     */
    public function handle($filteredData)
    {
        Mage::helper('mageflow_connect/log')->log(
            "Error occurred while tried to save media. Data follows:\n"
            . print_r($filteredData, true)
        );
        return $filteredData;
    }
}