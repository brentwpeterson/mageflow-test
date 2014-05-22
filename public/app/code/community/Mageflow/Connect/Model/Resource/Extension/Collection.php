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
 * Mageflow_Connect_Model_Resource_Extension_Collection
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Resource_Extension_Collection
    extends Varien_Data_Collection
{
    /**
     * original items
     *
     * @var array
     */
    protected $_originalItems = array();

    /**
     * Class constructor
     *
     * @return Collection
     */
    public function __construct(array $options = array())
    {
        return $this;
    }

    /**
     * get items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * add field to filter
     *
     * @param array $filter
     *
     * @return $this
     */
    public function addFieldToFilter(array $filter = array())
    {
        return $this;
    }

    /**
     * Retrieve collection all items count.
     *
     * Overloads the original getSize because
     * we use _originalItems internaly
     *
     * @return int
     */
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $this->_totalRecords
                =
                count($this->_originalItems) > 0 ? count($this->_originalItems)
                    : count($this->_items);
        }
        return intval($this->_totalRecords);
    }

    /**
     * Loads extensions from ESC
     *
     * @param bool $printQuery
     * @param bool $logQuery
     *
     * @return $this|Varien_Data_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $offset = ($this->getCurPage() - 1) * $this->getPageSize();
//        if ( $this_ ) $this->_originalItems = $this->getItems();
            $this->_originalItems = $this->_items;
            $this->_totalRecords = sizeof($this->_items);
            $this->_items = array_slice(
                $this->_items,
                $offset,
                $this->getPageSize(),
                true
            );
            $this->_isCollectionLoaded = true;
        }
        return $this;
    }

}
