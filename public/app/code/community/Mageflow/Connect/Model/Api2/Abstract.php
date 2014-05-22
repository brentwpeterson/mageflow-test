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
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Api2_Abstract
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Abstract extends Mage_Api2_Model_Resource
{

    const ACTION_TYPE_INFO = 'info';
    const STATUS_SUCCESS = 0;
    const STATUS_ERROR = 1;

    /**
     * version
     *
     * @var int
     */
    protected $_version = 1;

    /**
     * entity fields
     *
     * @var array
     */
    protected $_entityFields = array();

    /**
     * Class constructor
     */
    public function __construct()
    {
        //include Mageflow client lib and its autoloader
        @include_once 'Mageflow/Connect/Module.php';
        $m = new \Mageflow\Connect\Module();

        return $this;
    }

    /**
     * Retrieve information about customer
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    public function _retrieve()
    {
        return array();
    }

    /**
     * Returns information about concrete resource:
     * - type
     * - attributes
     * - other info (if available)
     *
     * @return Array Resource info
     */
    public function _getResourceInfo()
    {
        $out = array();
        $out['resource_url']
            = '/api/' . $this->getApiType() . '/' . str_replace(
                '_',
                '/',
                $this->getResourceType()
            );
        $out['resource_type'] = $this->_resourceType;
        $routes = $this->getConfig()->getNode(
            'resources/' . $this->_resourceType . '/routes'
        );
        $routesArr = array();
        foreach ($routes->children() as $name => $route) {
            $item = array(
                'name' => $name,
                'route' => (string)$route->route
            );
            $routesArr[] = $item;
        }
        $out['attributes'] = $this->getAvailableAttributesFromConfig();
        $out['routes'] = $routesArr;
        return $out;
    }

    /**
     * Gets lest of resources with detailed info about each resource.
     * It's mainly used for help index
     *
     * @return Varien_Simplexml_Element
     */
    public function getDetailedResourceList()
    {
        $resources = $this->getConfig()->getNode('resources');
        return $resources;
    }

    /**
     * Dispatches API request
     *
     * @return json
     */
    public function dispatch()
    {
        switch ($this->getActionType() . $this->getOperation()) {
            case self::ACTION_TYPE_INFO . self::OPERATION_RETRIEVE:
                $this->_errorIfMethodNotExist('_getResourceInfo');
                $retrievedData = $this->_getResourceInfo();
                $this->_render($retrievedData);
                return;
            default:
                parent::dispatch();
        }
        return;
    }

    /**
     * Log helper for all api2 resources
     *
     * @param type $object
     */
    protected function log($object)
    {
        Mage::helper('mageflow_connect/log')->log($object);
    }

    /**
     * Return collection of items. The type of items is defined by
     * workingModel and _resourceType
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        $out = array();
        $itemCollection = $this->getWorkingModel()->getCollection();

        $entityFields = $this->getEntityFields();

        foreach ($itemCollection as $item) {
            $a = array();
            foreach ($entityFields as $field => $entityField) {
                $a[$field] = $item->$entityField;
            }
            $out[] = $a;
        }
        Mage::helper('mageflow_connect/log')->log($out);
        return $out;
    }

    /**
     * Returns array of entity fields where attribute names are mapped to
     * actual entity fields
     *
     * @return array
     */
    public function getEntityFields()
    {
        if (empty($this->_entityFields)) {
            $node = $this->getConfig()->getNode(
                'resources/' . $this->_resourceType . '/attributes'
            );
            foreach ($node->children() as $child) {
                $entityField = $child->getName();
                if (trim($child->getAttribute('entity_field')) != '') {
                    $entityField = trim($child->getAttribute('entity_field'));
                }
                $this->_entityFields[$child->getName()] = $entityField;
            }
//            Mage::helper('mageflow_connect/log')->log($this->_entityFields);
        }
        return $this->_entityFields;
    }

    /**
     * add metadata to response array
     *
     * @param array $data
     *
     * @return array
     */
    protected function prepareResponse($data = array())
    {
        $responseMeta = array(
            'response_meta' => array(
                'timestamp' => time(),
                'item_count' => sizeof($data['items'])
            )
        );
        $out = array_merge($responseMeta, $data);
        $this->log($out);
        return $out;
    }

    /**
     * json encode $data and put into response body
     * add content type header to response
     *
     * @param array $data
     *
     * @return array
     */
    protected function sendJsonResponse(array $data)
    {
        return $data;

    }

    /**
     * prepares API client
     *
     * @return \Mageflow\Connect\Model\Api\Mageflow\Client
     */
    public function getApiClient()
    {
        $client = Mage::helper('mageflow_connect/oauth')->getApiClient();

        return $client;
    }

    /**
     * send overwritten data to MF
     *
     * @param       $type
     * @param array $filteredData
     * @param       $originalData
     * @return array
     */
    public function sendRollback($type, $filteredData, $originalData)
    {
        if (!isset($filteredData['deploymentpackage'])) {
            return ['rollback response' => 'no rollback target given'];
        }

        $now = new Zend_Date();

        $changesetItem = Mage::helper('mageflow_connect/data')
            ->createChangesetFromItem($type, $originalData);

        $dataItem = array(
            'type' => str_replace(
                array('::', ':'),
                '/',
                $changesetItem->getType()
            ),
            'content' => $changesetItem->getContent(),
            'encoding' => $changesetItem->getEncoding(),
        );

        if ($changesetItem->getMetainfo()) {
            $dataItem['metainfo'] = $changesetItem->getMetainfo();
        } else {
            $dataItem['metainfo'] = array();
        }

        $company = Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_COMPANY
        );
        $data = array(
            'company' => $company,
            'deploymentpackage' => $filteredData['deploymentpackage'],
            'items' => json_encode([$dataItem]),
        );

        $client = $this->getApiClient();
        $response = $client->post('rollback', $data);

        return ['rollback response' => $response];
    }

    /**
     * Mimic multicreate because Magento API is a bit weird about it:)
     *
     * @param array $filteredData
     */
    public function _multiCreate(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log(__METHOD__);
        $out = array();
        foreach ($filteredData as $data) {
            if (!isset($data['mf_guid'])) {
                $data['mf_guid'] = null;
            }
            $out[] = $this->_create($data);
        }
        Mage::helper('mageflow_connect/log')->log('OK');
    }

}
