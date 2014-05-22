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
 * Data.php
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
 * Mageflow_Connect_Helper_Data
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Returns hash of pretty random bytes
     *
     * @return string
     */
    public function randomHash()
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            return sha1(openssl_random_pseudo_bytes(64));
        }

        return md5(uniqid(uniqid(mt_rand(0, PHP_INT_MAX), true), true));
    }


    /**
     * create changesetitem object of type from content
     * type must be with ":", like "cms:block"
     * content must be array from getData()
     *
     * @param $type
     * @param $content
     *
     * @return mixed
     */
    public function createChangesetFromItem($type, $content)
    {
        $changesetItem = Mage::getModel('mageflow_connect/changeset_item');

        $content = $this->cleanupContent($content);

        Mage::helper('mageflow_connect/log')->log('Working with ' . $type);

        if ($type == 'base_url') {

            $this->updateBaseUrl($content);

            return false;
        }

        $packer = $this->getPacker($type);

        if ($packer instanceof Mageflow_Connect_Helper_Handler_Abstract) {

            $content = $packer->packContent($content);
            Mage::helper('mageflow_connect/log')->log($type);
            $encodedContent = json_encode(
                $content,
                JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE
            );

            $now = new Zend_Date();
            $changesetItem->setContent($encodedContent);
            $changesetItem->setType($type);
            $changesetItem->setEncoding('json');
            $changesetItem->setCreatedAt($now->toString('c'));
            $changesetItem->setUpdatedAt($now->toString('c'));
        }

        return $changesetItem;
    }

    /**
     * Puts base URL to MageFlow API
     *
     * @param $content
     */
    private function updateBaseUrl($content)
    {
        Mage::helper('mageflow_connect/log')->log('base_url change');

        $company = Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_COMPANY
        );
        $data = array(
            'command'      => 'change base url',
            'company'      => $company,
            'value'        => $content['value'],
            'path'         => $content['path'],
            'value'        => $content['value'],
            'scope'        => $content['scope'],
            'scope_id'     => $content['scope_id'],
            'website_code' => $content['website_code'],
            'store_code'   => $content['store_code']
        );

        $instanceKey = Mage::app()->getStore()
            ->getConfig(
                Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY
            );
        $client = Mage::helper('mageflow_connect/oauth')->getApiClient();
        $response = $client->put('instance/' . $instanceKey, $data);

        Mage::helper('mageflow_connect/log')->log(
            'response ' . print_r($response, true)
        );
    }

    /**
     * Cleans up content array
     *
     * @param $content
     *
     * @return mixed
     */
    private function cleanupContent($content)
    {
        if (isset($content['block_id'])) {
            unset($content['block_id']);
        }
        if (isset($content['page_id'])) {
            unset($content['page_id']);
        }
        if (isset($content['attribute_id'])) {
            unset($content['attribute_id']);
        }
        if (isset($content['entity_id'])) {
            unset($content['entity_id']);
        }
        if (isset($content['config_id'])) {
            unset($content['config_id']);
        }
        if (isset($content['user_id'])) {
            unset($content['user_id']);
        }
        return $content;
    }

    /**
     * Packer factory that helps to get packer for given type
     *
     * @param $type
     *
     * @return Mage_Core_Helper_Abstract|null
     */
    private function getPacker($type)
    {
        $packer = null;
        if ($type == 'Mage_Eav_Model_Entity_Attribute_Set'
            || $type == 'eav:entity_attribute_set'
            || $type == 'catalog:attributeset'
        ) {
            $packer = Mage::helper(
                'mageflow_connect/handler_catalog_attributeset'
            );
        }

        if ($type == 'Mage_Core_Model_Store' || $type == 'core:store'
            || $type == 'Mage_Core_Model_Website'
            || $type == 'core:website'
            || $type == 'Mage_Core_Model_Store_Group'
            || $type == 'core:store_group'
        ) {
            $type = 'core:website';
            $packer = Mage::helper('mageflow_connect/handler_system_website');

        }

        if ($type == 'Mage_Core_Model_Config_Data'
            || $type == 'system:configuration'
        ) {
            $packer = Mage::helper(
                'mageflow_connect/handler_system_configuration'
            );
        }

        if ($type == 'Mage_Admin_Model_User' || $type == 'admin:user') {
            $packer = Mage::helper('mageflow_connect/handler_system_user');
        }

        if ($type == 'Mage_Cms_Model_Page' || $type == 'cms:page'
            || $type == 'Mage_Cms_Model_Block'
            || $type == 'cms:block'
        ) {
            $packer = Mage::helper('mageflow_connect/handler_cms_abstract');
        }

        if ($type == 'Mage_Catalog_Model_Resource_Eav_Attribute'
            || $type == 'catalog:resource_eav_attribute'
            || $type == 'catalog:attribute'
        ) {
            $packer = Mage::helper(
                'mageflow_connect/handler_catalog_attribute'
            );
        }

        if ($type == 'Mage_Catalog_Model_Category'
            || $type == 'catalog:category'
        ) {
            $packer = Mage::helper('mageflow_connect/handler_catalog_category');
        }

        if ($type == 'Mageflow_Connect_Model_Media_Index'
            || $type == 'media:file'
        ) {
            $packer = Mage::helper('mageflow_connect/handler_cms_media');
        }

        return $packer;
    }


    /**
     * get item collection from mf api
     *
     * @return Varien_Data_Collection
     */
    public function getItemCollectionFromMfApi()
    {
        $company = Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_COMPANY
        );
        $project = Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_PROJECT
        );
        $data = array(
            'company' => $company,
            'project' => $project
        );

        $client = Mage::helper('mageflow_connect/oauth')->getApiClient();
        $response = $client->get('changeset', $data);
        $changesetDataArr = json_decode($response, true);
        $changesetData = array();
        if (is_array($changesetDataArr)) {
            $changesetData = $changesetDataArr['items'];
        }
        $itemCollection = new Varien_Data_Collection();

        foreach ($changesetData as $changeset) {
            $data['id'] = $changeset['id'];
            $response = $client->get('changeset', $data);
            $changesetItemData = json_decode($response, true);
            foreach (
                $changesetItemData['items'][0]['items'] as $changesetItem
            ) {
                $itemCollection->addItem(
                    new Varien_Object(
                        [
                            'id' => $changesetItem['id'],
                            'changeset' => $changeset['name'],
                            'type' => $changesetItem['type']
                        ]
                    )
                );
            }
        }
        return $itemCollection;
    }
}
