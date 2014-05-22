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
 * Item.php
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
 * Mageflow_Connect_Model_Changeset_Item
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 *
 * @method int getId()
 * @method string getContent()
 * @method string getEncoding()
 * @method string getType()
 * @method string getStatus()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 *
 * @method setContent($value)
 * @method setEncoding($value)
 * @method setType($value)
 * @method setStatus($value) set status to one if (new, sent, rejected, failed)
 */
class Mageflow_Connect_Model_Changeset_Item extends Mage_Core_Model_Abstract
{

    const TYPE_CMS_BLOCK = 'cms:block';
    const TYPE_CMS_PAGE = 'cms:page';
    const TYPE_SYSTEM_CONFIGURATION = 'system:configuration';
    const TYPE_SYSTEM_ADMIN_USER = 'system:admin:user';
    const TYPE_SYSTEM_ADMIN_GROUP = 'system:admin:group';
    const TYPE_CATALOG_CATEGORY = 'catalog:category';
    const TYPE_CATALOG_PRODUCT_ATTRIBUTESET = 'catalog:product:attributeset';
    const TYPE_CATALOG_PRODUCT_ATTRIBUTE = 'catalog:product:attribute';
    const TYPE_CATALOG_ATTRIBUTESET = 'catalog:attributeset';
    const TYPE_CATALOG_ATTRIBUTE = 'catalog:attribute';
    const TYPE_CORE_WEBSITE = 'core:website';
    const TYPE_ADMIN_USER = 'admin:user';
    const TYPE_MEDIA_FILE = 'media:file';

    /**
     * @var string changeset statuses
     */
    const STATUS_NEW = 'new';
    const STATUS_SENT = 'sent';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FAILED = 'failed';

    /**
     * Class constructor
     *
     * @return Item
     */
    public function _construct()
    {
        $this->_init('mageflow_connect/changeset_item');
        return parent::_construct();
    }

}
