<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Veralidity\CardanoDbSync\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Veralidity\CardanoDbSync\Model\PostgresConnector;
use Magento\Framework\Filesystem\Driver\File;

class Data extends AbstractHelper
{
    /**
     * @var PostgresConnector
     * 
     */
    public $connection;

    /**
     * @var File
     * 
     */
    protected $fileDriver;

    /**
     * @param Context $context
     * @param PostgresConnector $connection
     */
    public function __construct(
        Context $context,
        PostgresConnector $connection,
        File $fileDriver
    ) {
        parent::__construct($context);
        $this->connection = $connection;
        $this->fileDriver = $fileDriver;
    }

    public function testConnection($config)
    {
        $result = $this->connection->testConnection($config);
        return $result;
    }

    /**
     * 
     * Get Cardano Network Stats
     * 
     */
    public function getNetworkStats($pgsqlConnection)
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/network_all.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $pgsqlConnection->fetchAll($sqlQuery);
        return $result;
    }

    /**
     * 
     * Get Latest Cardano Blocks Stats
     * 
     */
    public function getBlocksLatest($pgsqlConnection)
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/blocks/blocks_latest.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $pgsqlConnection->fetchAll($sqlQuery);
        return $result;
    }

    /**
     * 
     * Get Cardano DbSync Network Sync Percentage
     * 
     */
    public function getSyncPercent($pgsqlConnection)
    {
        $select = $pgsqlConnection->select()
            ->from(['b' => 'block'], [])
            ->columns([
                'sync_percent' => new \Zend_Db_Expr('100 * (EXTRACT(epoch FROM (MAX(time) AT TIME ZONE \'UTC\')) - 
                    EXTRACT(epoch FROM (MIN(time) AT TIME ZONE \'UTC\'))) / 
                    (EXTRACT(epoch FROM (NOW() AT TIME ZONE \'UTC\')) - 
                    EXTRACT(epoch FROM (MIN(time) AT TIME ZONE \'UTC\')))')
            ]);

        return $pgsqlConnection->fetchRow($select);
    }

}
