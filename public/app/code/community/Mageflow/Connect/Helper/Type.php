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
 * Type.php
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
 * Mageflow_Connect_Helper_Type
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Type extends Mage_Core_Helper_Abstract
{

    /**
     * is enabled
     *
     * @param $type
     *
     * @return bool
     */
    public function isTypeEnabled($type)
    {
        $configNode = Mage::app()->getConfig()->getNode(
            'default/mageflow_connect/supported_types/' . $type
        );
        if (null !== $configNode
            && $configNode[0] instanceof Mage_Core_Model_Config_Element
        ) {
            /**
             * @var Mage_Core_Model_Config_Element $el
             */
            $el = $configNode[0];
            return $el->getAttribute('enabled') != 'false';
        }
        return true;
    }

    /**
     * get types
     *
     * @return array|mixed|string
     */
    public function getTypes()
    {
        $cacheId = md5(__METHOD__);
        $cache = Mage::app()->getCache();
        if ($cache->load($cacheId)) {
            $types = unserialize($cache->load($cacheId));
        } else {
            $typeNodeList = Mage::app()->getConfig()->getNode(
                'default/mageflow_connect/supported_types'
            );
            $types = array();
            /**
             * @var Mage_Core_Model_Config_Element $typeNode
             */
            foreach ($typeNodeList->children() as $typeNode) {
                if (
                    null == $typeNode->getAttribute('enabled')
                    || $typeNode->getAttribute('enabled') != 'false'
                ) {
                    $name = $typeNode->getName();
                    $data = $typeNode->asArray();
                    $types[$name] = (empty($data)) ? array() : $data;
                }
            }
            $cache->save(serialize($types), $cacheId);
        }
        return $types;
    }

    /**
     * This method returns list of types that
     * MageFlow supports.
     * NB! This list may change over MFx version changes.
     *
     * @return array
     */
    public function getSupportedTypes()
    {
        $nodeList = Mage::app()->getConfig()->getNode(
            'default/mageflow_connect/supported_types'
        )->asArray();
        $supportedTypes = array_keys($nodeList);
        return $supportedTypes;
    }
}
