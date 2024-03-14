<?php
/**
 * AppFactory Magento Extension
 *
 * NOTICE OF LICENSE
 *
 * This file is part of the AppFactory Magento Extension project.
 * Unauthorized copying or distribution of this file, via any 
 * medium is strictly prohibited. Proprietary and confidential.
 * 
 * @copyright  Copyright (c) 2024 Charla LLC 
 *             <hello@getcharla.com>
 * @author Charla Integration Team <hello@getcharla.com>
 *
 */

namespace Charla\Widget\Helper;
use Magento\Framework\App\Helper\AbstractHelper;


class Log extends AbstractHelper
{
    /**
     * This method writes log message to modules log file
     * and system.log
     *
     * @param mixed  $message
     * @param string $method
     * @param string $line
     * @param      $level
     */
    public function log(
        $message,
        $method = null,
        $line = null
    )
    {


        if (is_null($method)) {
            $method = __METHOD__;
        }
        if (is_null($line)) {
            $line = __LINE__;
        }
        if (function_exists('debug_backtrace')) {
            $backtrace = debug_backtrace();
            $method = $backtrace[1]['class'] . '::' . $backtrace[1]['function'];
            $line = $backtrace[0]['line'];
        }
        $message = print_r($message, true);


        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/charla.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info(
            sprintf('%s(%s): %s', $method, $line, $message)     
        );


    }
}