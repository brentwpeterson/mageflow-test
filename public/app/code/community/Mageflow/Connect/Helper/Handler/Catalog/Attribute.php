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
 * Attribute.php
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
 * Mageflow_Connect_Helper_Handler_Catalog_Attribute
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Handler_Catalog_Attribute
    extends Mageflow_Connect_Helper_Handler_Abstract
{
    /**
     * update or create catalog/resource_eav_attribute from data array
     *
     * @param $filteredData
     *
     * @return array|null
     */
    public function handle($filteredData)
    {
        $itemFoundByIdentifier = false;
        $itemFoundByMfGuid = false;
        $foundItemsMatch = false;
        $itemModel = false;


        $itemModelByIdentifier = Mage::getModel('eav/entity_attribute')
            ->load($filteredData['attribute_code'], 'attribute_code');
        $itemModelByMfGuid = Mage::getModel('eav/entity_attribute')
            ->load($filteredData['mf_guid'], 'mf_guid');

        if ($itemModelByIdentifier->getAttributeId()) {
            $itemFoundByIdentifier = true;
        }
        if ($itemModelByMfGuid->getAttributeId()) {
            $itemFoundByMfGuid = true;
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid) {
            $idByIdent = $itemModelByIdentifier->getAttributeId();
            $idByGuid = $itemModelByMfGuid->getAttributeId();

            Mage::helper('mageflow_connect/log')->log(
                'by mf_guid ' . $idByGuid
            );
            Mage::helper('mageflow_connect/log')->log('by ident ' . $idByIdent);

            if ($idByGuid == $idByIdent) {
                $foundItemsMatch = true;
            }
        }

        if ($itemFoundByIdentifier && !$itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 01');
            $itemModel = $itemModelByIdentifier;
            $filteredData['attribute_id'] = $itemModel->getAttributeId();
        }
        if (!$itemFoundByIdentifier && $itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 10 - error');
            //$itemModel = $itemModelByMfGuid;
            $filteredData['attribute_id'] = $itemModel->getAttributeId();
        }
        if (!$itemFoundByIdentifier && !$itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 00');
            $itemModel = Mage::getModel('catalog/resource_eav_attribute');
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid && $foundItemsMatch) {
            Mage::helper('mageflow_connect/log')->log('case 11-1');
            $itemModel = $itemModelByMfGuid;
            $filteredData['attribute_id'] = $itemModel->getAttributeId();
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid && !$foundItemsMatch) {
            Mage::helper('mageflow_connect/log')->log('case 11-0 error');
            //$itemModel = $itemModelByMfGuid;
            $filteredData['attribute_id'] = $itemModel->getAttributeId();
        }

        $originalData = null;
        $originalOptionValues = array();
        $originalOptionOrder = array();
        $originalDefaults = null;

        if (!is_null($itemModel)) {
            $originalData = $itemModel->getData();
            Mage::helper('mageflow_connect/log')->log($originalData);

            $originalDefaults = array
            (
                'default'                => array(
                    $originalData['default_value']
                ),
                'default_value'          => $originalData['default_value'],
                'default_value_text'     => $originalData['default_value'],
                'default_value_yesno'    => $originalData['default_value'],
                'default_value_textarea' => $originalData['default_value'],
            );

            Mage::helper('mageflow_connect/log')->log($originalDefaults);

            $storeCollection = Mage::getModel('core/store')
                ->getCollection()
                ->load();

            $originalOptionCollection = Mage::getModel(
                'eav/entity_attribute_option'
            )
                ->getCollection()
                ->addFieldToFilter('attribute_id', $itemModel->getAttributeId())
                ->load();

            foreach ($originalOptionCollection as $optionEntity) {
                foreach ($storeCollection as $storeEntity) {
                    $valueCollection = Mage::getModel(
                        'eav/entity_attribute_option'
                    )
                        ->getCollection()
                        ->setStoreFilter($storeEntity->getStoreId())
                        ->join(
                            'attribute',
                            'attribute.attribute_id=main_table.attribute_id',
                            'attribute_code'
                        )
                        ->addFieldToFilter(
                            'main_table.option_id',
                            array('eq' => $optionEntity->getOptionId())
                        )
                        ->load();

                    foreach ($valueCollection as $value) {
                        Mage::helper('mageflow_connect/log')->log(
                            print_r($value->getData(), true)
                        );
                        $originalOptionValues[$optionEntity->getOptionId()][0]
                            = $value->getDefaultValue();
                        $originalOptionValues[$optionEntity->getOptionId(
                        )][$storeEntity->getStoreId()]
                            = $value->getValue();
                        $originalOptionOrder[$optionEntity->getOptionId()]
                            = $value->getSortOrder();
                    }
                }
            }
        }

        Mage::helper('mageflow_connect/log')->log(
            print_r($originalOptionValues, true)
        );
        Mage::helper('mageflow_connect/log')->log(
            print_r($originalOptionOrder, true)
        );

        if (isset($filteredData['store_labels'])) {
            foreach ($filteredData['store_labels'] as $key => $label) {
                if ($key != "0") {
                    $storeEntity = Mage::getModel('core/store')
                        ->load($key, 'code');
                    $filteredData['store_labels'][$storeEntity->getId()]
                        = $label;
                    unset($filteredData['store_labels'][$key]);
                }
            }
        }

        $optionArray = null;

        if (isset($filteredData['option'])) {
            foreach (
                $filteredData['option']['value'] as $valueSetKey => $valueSet
            ) {
                foreach ($valueSet as $key => $value) {
                    if ($key != "0") {
                        $storeEntity = Mage::getModel('core/store')
                            ->load($key, 'code');
                        $filteredData['option']['value'][$valueSetKey]
                        [$storeEntity->getId()]
                            = $value;
                        unset(
                        $filteredData['option']['value'][$valueSetKey][$key]
                        );
                    }
                }
            }
            $optionArray = $filteredData['option'];
            unset($filteredData['option']);
        }


        if (count($originalOptionValues)) {
            $filteredData['option']['value'] = $originalOptionValues;
            $filteredData['option']['order'] = $originalOptionOrder;
        }

        Mage::helper('mageflow_connect/log')->log('first iteration');
        Mage::helper('mageflow_connect/log')->log(
            sprintf('%s', print_r($filteredData, true))
        );
        $savedEntity = $this->saveItem($itemModel, $filteredData);

        $attributeId = $savedEntity->getAttributeId();
        $filteredData = $savedEntity->getData();

// start rebuilding new option values

        if (!is_null($originalDefaults)) {
            $filteredData = array_merge($filteredData, $originalDefaults);
        }

        if (!is_null($optionArray)) {
            foreach ($optionArray['value'] as $key => $optionArrayValue) {

                $duplicateOption = false;
                foreach ($filteredData['option']['value'] as $existingOption) {
                    if ($existingOption == $optionArray['value'][$key]) {
                        $duplicateOption = true;
                    }
                }

                if ($duplicateOption) {
                    continue;
                }

                $optionEntity = Mage::getModel('eav/entity_attribute_option');
                $optionEntity->setData(
                    array
                    (
                    'attribute_id' => $attributeId,
                    'sort_order'   => $optionArray['order'][$key]
                    )
                );
                $optionEntity->save();

                $filteredData['option']['value'][$optionEntity->getOptionId()]
                    = $optionArray['value'][$key];

                $filteredData['option']['order'][$optionEntity->getOptionId()]
                    = $optionArray['order'][$key];

                if ($filteredData['default'][0] == $key) {

                    $filteredData['default']
                        = array($optionEntity->getOptionId());
                    $filteredData['default_value_text']
                        = $optionEntity->getOptionId();
                    $filteredData['default_value_yesno']
                        = $optionEntity->getOptionId();
                    $filteredData['default_value_textarea']
                        = $optionEntity->getOptionId();

                }
            }
        }

        Mage::helper('mageflow_connect/log')->log(
            sprintf('%s', print_r($filteredData, true))
        );
        Mage::helper('mageflow_connect/log')->log(get_class($itemModel));
        $savedEntity = $this->saveItem($itemModel, $filteredData);
        Mage::helper('mageflow_connect/log')->log(get_class($savedEntity));

        if ($savedEntity instanceof Mage_Eav_Model_Entity_Attribute) {
            return array(
                'entity'        => $savedEntity,
                'original_data' => $originalData
            );
        }
        Mage::helper('mageflow_connect/log')->log(
            "Error occurred while tried to save
            Catalog Attribute. Data follows:\n"
            . print_r($filteredData, true)
        );
        return null;
    }

    /**
     * pack content
     *
     * @param $content
     *
     * @return array
     */
    public function packContent($content)
    {
        Mage::helper('mageflow_connect/log')->log('processing attribute');
        foreach ($content['option']['value'] as $valueSetKey => $valueSet) {
            foreach ($valueSet as $key => $value) {
                if ($key != 0) {
                    $storeEntity = Mage::getModel('core/store')
                        ->load($key, 'store_id');
                    $content['option']['value'][$valueSetKey]
                    [$storeEntity->getCode()]
                        = $value;
                    unset($content['option']['value'][$valueSetKey][$key]);
                }
            }
        }
        foreach ($content['store_labels'] as $key => $label) {
            Mage::helper('mageflow_connect/log')->log(
                print_r($content['store_labels'], true)
            );
            if ($key != 0) {
                $storeEntity = Mage::getModel('core/store')
                    ->load($key, 'store_id');
                $content['store_labels'][$storeEntity->getCode()] = $label;
                unset($content['store_labels'][$key]);
            }
            Mage::helper('mageflow_connect/log')->log(
                print_r($content['store_labels'], true)
            );
        }
        Mage::helper('mageflow_connect/log')->log(print_r($content, true));
        return $content;
    }
}