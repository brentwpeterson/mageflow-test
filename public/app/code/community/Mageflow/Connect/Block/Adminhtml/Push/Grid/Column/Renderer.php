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
 * Renderer.php
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
 * Mageflow_Connect_Block_Adminhtml_Migrate_Grid_Column_Renderer
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Push_Grid_Column_Renderer
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * render
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $output = 'Preview N/A';
        if ($row->getType()) {
            switch ($row->getType()) {
                case Mageflow_Connect_Model_Changeset_Item::TYPE_CMS_BLOCK:
                    $content = json_decode($row->getContent());
                    if ($content->title) {
                        $output = $content->title;
                    }
                    break;
                case Mageflow_Connect_Model_Changeset_Item::TYPE_CMS_PAGE:
                    $content = json_decode($row->getContent());
                    if ($content->title) {
                        $output = $content->title;
                    }
                    break;
                case
                Mageflow_Connect_Model_Changeset_Item::
                TYPE_SYSTEM_CONFIGURATION:
                    $content = json_decode($row->getContent());
                    $out = '';
                    if (isset($content->path)) {
                        $out = sprintf(
                            '%s=%s',
                            $content->path,
                            $content->value
                        );
                    }
                    $output = $out;
                    break;
                case
                Mageflow_Connect_Model_Changeset_Item::TYPE_CATALOG_CATEGORY:
                    $content = json_decode($row->getContent());
                    if ($content->name) {
                        $output = $content->name;
                    }
                    break;
                case
                Mageflow_Connect_Model_Changeset_Item::TYPE_CATALOG_ATTRIBUTE:
                    $content = json_decode($row->getContent());
                    if ($content->attribute_code) {
                        $output = $content->attribute_code;
                    }
                    break;
                case
                Mageflow_Connect_Model_Changeset_Item::
                TYPE_CATALOG_ATTRIBUTESET:
                    $content = json_decode($row->getContent());
                    if ($content->attribute_set_name) {
                        $output = $content->attribute_set_name;
                    }
                    break;
                case Mageflow_Connect_Model_Changeset_Item::TYPE_CORE_WEBSITE:
                    $content = json_decode($row->getContent());
                    if ($content->name) {
                        $output = $content->name;
                    }
                    break;
                case Mageflow_Connect_Model_Changeset_Item::TYPE_ADMIN_USER:
                    $content = json_decode($row->getContent());
                    if ($content->username) {
                        $output = $content->username;
                    }
                    break;
                case Mageflow_Connect_Model_Changeset_Item::TYPE_MEDIA_FILE:
                    $content = json_decode($row->getContent());
                    if (null !== $content->basename) {
                        $output = sprintf(
                            "%s (%s KB)", $content->basename,
                            round(filesize($content->filename) / 1024)
                        );
                    }
                    break;
            }
        }
        if (strlen($output) > 100) {
            $output = substr($output, 0, 100) . '...';
        }
        return $output;
    }

}
