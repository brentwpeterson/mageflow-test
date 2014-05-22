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
 * Abstract.php
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
 * Mageflow_Connect_Helper_Handler_Abstract
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Handler_Abstract
    extends Mageflow_Connect_Helper_Data
{
    /**
     * sets data from array and saves object
     *
     * @param $itemModel
     * @param $filteredData
     *
     * @return array
     */
    public function saveItem($itemModel, $filteredData)
    {
        if (is_null($itemModel)) {
            return null;
        }

        try {
            $itemModel->setData($filteredData);
            $itemModel->save();
            Mage::helper('mageflow_connect/log')
                ->log(sprintf('Saved item with ID %s', $itemModel->getId()));
        } catch (Exception $e) {
            Mage::helper('mageflow_connect/log')->log(
                sprintf(
                    'Error occurred while saving item: %s',
                    $e->getMessage()
                )
            );
            Mage::helper('mageflow_connect/log')->log($e->getTraceAsString());
            return null;
        }
        return $itemModel;
    }

    /**
     * Creates changesetitem content from entity
     *
     * @param $content
     *
     * @return array
     */
    public function packContent($content)
    {
        return array();
    }
}