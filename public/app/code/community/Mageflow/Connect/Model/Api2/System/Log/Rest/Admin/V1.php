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
 * Mageflow_Connect_Model_Api2_System_Log_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Log_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_configuration';

    /**
     * Class constructor
     *
     * @return V1
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array(
            'log'       => array(),
            'exception' => array()
        );
        $this->log($this->getRequest()->getParams());

        try {
            $maxLines = Mage::getStoreConfig(
                'mageflow_connect/advanced/log_lines'
            );
            //failsafe is 100 lines
            if (!$maxLines) {
                $maxLines = 100;
            }

            $file = Mage::getStoreConfig('dev/log/file');
            $exceptionFile = Mage::getStoreConfig('dev/log/exception_file');
            $logDir = Mage::getBaseDir('var') . DS . 'log';

            $logFilePath = $logDir . DS . $file;
            $exceptionFilePath = $logDir . DS . $exceptionFile;
            $logTypes = array(
                'log'       => $logFilePath,
                'exception' => $exceptionFilePath
            );
            //safety output
            $out['log'] = $logFilePath;
            $out['exception'] = $exceptionFilePath;

            foreach ($logTypes as $logType => $path) {
                $cmd = sprintf('wc -l %s', $path);
                $numLines = shell_exec($cmd);
                $cmd = sprintf('tail -%s %s', $maxLines, $path);
                $logStr = shell_exec($cmd);
                $lastLines = explode(PHP_EOL, $logStr);
                $logLines = array_combine(
                    range($numLines - $maxLines, $numLines),
                    $lastLines
                );
                $out[$logType] = $logLines;
            }

        } catch (Exception $e) {
            $this->log($e->getMessage());
        }

        return $out;
    }

    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        return $this->_retrieve();
    }

    /**
     * update
     *
     * @param array $filteredData
     */
    public function _update(array $filteredData)
    {
        $this->log(__METHOD__);
        $this->log($filteredData);
        $this->getResponse()->addMessage(
            'status',
            self::STATUS_SUCCESS,
            array(),
            Mage_Api2_Model_Response::MESSAGE_TYPE_SUCCESS
        );
    }

    /**
     * multi update
     *
     * @param array $filteredData
     */
    public function _multiUpdate(array $filteredData)
    {
        $this->log(__METHOD__);
        foreach ($filteredData as $data) {
            $this->_update($data);
        }
    }

    /**
     * create
     *
     * @param array $filteredData
     */
    public function _create(array $filteredData)
    {
        $this->log(__METHOD__);
        $this->log($filteredData);
        $this->getResponse()->addMessage(
            'status',
            self::STATUS_SUCCESS,
            array(),
            Mage_Api2_Model_Response::MESSAGE_TYPE_SUCCESS
        );
    }

    /**
     * multi create
     *
     * @param array $filteredData
     */
    public function _multiCreate(array $filteredData)
    {
        $this->log(__METHOD__);
        foreach ($filteredData as $data) {
            $this->_create($data);
        }
    }

}