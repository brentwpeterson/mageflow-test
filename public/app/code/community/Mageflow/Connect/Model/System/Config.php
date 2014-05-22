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
 * Config.php
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
 * Mageflow_Connect_Model_System_Config
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Config extends Varien_Object
{

    const API_URL = 'mageflow_connect/advanced/api_url';
    const API_CONSUMER_KEY = 'mageflow_connect/api/consumer_key';
    const API_CONSUMER_SECRET = 'mageflow_connect/api/consumer_secret';
    const API_TOKEN = 'mageflow_connect/api/token';
    const API_TOKEN_SECRET = 'mageflow_connect/api/token_secret';
    const API_ENABLED = 'mageflow_connect/api/enabled';
    const API_COMPANY = 'mageflow_connect/api/company';
    const API_PROJECT = 'mageflow_connect/api/project';
    const API_COMPANY_NAME = 'mageflow_connect/api/company_name';
    const API_PROJECT_NAME = 'mageflow_connect/api/project_name';
    const API_INSTANCE_KEY = 'mageflow_connect/api/instance_key';
    const API_LOG_LEVEL = 'mageflow_connect/api/log_level';
}
