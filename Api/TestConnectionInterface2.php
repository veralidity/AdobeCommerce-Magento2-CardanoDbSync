<?php
/**
 * Mitchell Robles, Jr.
 *
 * @package    Veralidity
 * @category   CardanoDbSync
 * @copyright  Copyright © 2024 Mitchell Robles, Jr.
 * @license    https://www.veralidity.com/license/
 * @author     Mitchell Robles, Jr. <mitchroblesjr@gmail.com>
 */

namespace Veralidity\CardanoDbSync\Api;

/**
 * Handler for logging Whitelisted IPs
 */
interface TestConnectionInterface2
{
    /**
     * Test the PostgreSQL database connection.
     *
     * @return string
     */
    public function test();
}