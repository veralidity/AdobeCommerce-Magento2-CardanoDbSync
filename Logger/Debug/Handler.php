<?php
/**
 * Veralidity, LLC
 *
 * @package    Veralidity
 * @category   CardanoDbSync
 * @copyright  Copyright © 2024 Veralidity, LLC
 * @license    https://www.veralidity.com/license/
 * @author     Veralidity, LLC <veralidity@protonmail.com>
 */

namespace Veralidity\CardanoDbSync\Logger\Debug;

/**
 * Handler for logging Whitelisted IPs
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/veralidity-cardanodbsync-debug.log';
}