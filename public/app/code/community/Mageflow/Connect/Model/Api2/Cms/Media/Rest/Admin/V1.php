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
 * V1.php
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
 * Mageflow_Connect_Model_Api2_Cms_Media_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Cms_Media_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Cms
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'cms_media';

    /**
     * Class constructor
     *
     * @return \Mageflow_Connect_Model_Api2_Cms_Media_Rest_Admin_V1
     */
    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        $out = array();
        /**
         * @var Mageflow_Connect_Model_Media_Index $mediaIndexModel
         */
        foreach (
            $this->getWorkingModel()->getCollection() as $mediaIndexModel
        ) {
            $out[] = $this->packItem($mediaIndexModel);
        }
        return $out;
    }

    /**
     * pack item
     *
     * @param Mageflow_Connect_Model_Media_Index $mediaIndexModel
     *
     * @return array
     */
    private function packItem($mediaIndexModel)
    {
        $a = array();
        $a['basename'] = $mediaIndexModel->getBasename();
        $a['path'] = $mediaIndexModel->getPath();
        $a['mtime'] = $mediaIndexModel->getMtime();
        $a['size'] = $mediaIndexModel->getSize();
        $a['type'] = $mediaIndexModel->getType();
        $a['mf_guid'] = $mediaIndexModel->getMfGuid();
        return $a;
    }

    /**
     * GET request to retrieve a single CMS media index item by its MF GUID
     *
     * @return array|mixed
     */
    public function _retrieve()
    {
        Mage::log(
            sprintf(
                '%s(%s): %s',
                __METHOD__,
                __LINE__,
                print_r($this->getRequest()->getParams(), true)
            )
        );
        $mfGuid = $this->getRequest()->getParam('key', -1);
        $out = array();

        $item = $this->findItem($mfGuid);
        if (null !== $item) {
            $out[] = $this->packItem($item);
        }

        return $out;
    }

    /**
     * find item
     *
     * @param $mfGuid
     *
     * @return Mageflow_Connect_Model_Media_Index
     */
    private function findItem($mfGuid)
    {
        $collection = $this->getWorkingModel()->getCollection();
        $collection->addFilter('mf_guid', $mfGuid);
        $collection->load();
        if ($collection->getFirstItem() instanceof
            Mageflow_Connect_Model_Media_Index
            && $collection->getFirstItem()->getId() > 0
        ) {
            return $collection->getFirstItem();
        }
        return null;
    }

    /**
     * PUT request to update a single CMS page
     *
     * @param array $filteredData
     *
     * @return array|string|void
     */
    public function _update(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log(sprintf('%s', $filteredData));
        return $this->_create($filteredData);
    }

    /**
     * Handles create (POST) request for cms/page
     *
     * @param array $filteredData
     *
     * @return Mage_Core_Model_Abstract
     */
    public function _create(array $filteredData)
    {
        $out = array();
        $item = $this->findItem($filteredData['mf_guid']);
        if (null === $item) {
            $item = Mage::getModel('mageflow_connect/media_index');
            $item->setData($filteredData);
        }
        $filePath
            = Mage::getBaseDir('base') . '/' . ltrim($item->getPath(), '/');
        Mage::helper('mageflow_connect/log')->log(
            'Saving file to ' . $filePath
        );
        $dirPath = dirname($filePath);
        if (!file_exists($dirPath)) {
            @mkdir($dirPath, 0777, true);
            $this->logPhpError(error_get_last());
        }
        @file_put_contents($filePath, hex2bin($filteredData['hex']));
        $this->logPhpError(error_get_last());

        $item->setMtime(time());
        $item->setSize(filesize($filePath));
        $item->save();
        Mage::helper('mageflow_connect/log')->log($item);
        $this->_successMessage(
            'Created media file',
            200,
            $this->packItem($item)
        );
        return $out;
    }

    /**
     * log php errors
     *
     * @param array $errors
     */
    private function logPhpError($errors = array())
    {
        if (is_array($errors) && sizeof($errors) > 0) {
            Mage::helper('mageflow_connect/log')->log($errors);
        }
    }

    /**
     * DELETE to delete a collection of pages
     *
     * @param array $filteredData
     */
    public function _multiDelete(array $filteredData)
    {

    }

    /**
     * multiCreate
     *
     * @param array $filteredData
     *
     * @return array|void
     */
    public function _multiCreate(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log($filteredData);
        $out = array();
        foreach ($filteredData as $data) {
            $out[] = $this->_create($data);
        }
        Mage::helper('mageflow_connect/log')->log($out);
        return $out;
    }

    /**
     * multiUpdate
     *
     * @param array $filteredData
     */
    public function _multiUpdate(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log($filteredData);
        $out = array();
        foreach ($filteredData as $data) {
            $this->_update($data);
        }
        Mage::helper('mageflow_connect/log')->log('OK');
    }

}
