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

namespace Veralidity\CardanoDbSync\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;
use Veralidity\Framework\Model\ResourceModel\Type\Db\Pdo\Pgsql;
use Magento\Framework\Filesystem\Driver\File;

/**
 * Handler for logging Whitelisted IPs
 */
class TestConnection3 extends Pgsql implements \Veralidity\CardanoDbSync\Api\TestConnectionInterface3
{
    protected $postgresConnector;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var Zend_Db_Adapter_Pdo_Pgsql|null
     */
    protected $connection;

    public $message;

    protected $fileDriver;

    public function __construct(
        File $fileDriver,
        ResourceConnection $resourceConnection,
        Pgsql $postgresConnector,
        array $config = null
    ) {
        $this->fileDriver = $fileDriver;
        $this->postgresConnector = $postgresConnector;
        $this->resourceConnection = $resourceConnection;

        $config = $this->resourceConnection->getConnection('pgsql')->getConfig();
        //var_dump($config);
        parent::__construct($config);

        $this->connection = $this->postgresConnector->getConnection();

    }

    /**
     * Test the PostgreSQL database connection.
     *
     * @return string
     */
    public function test()
    {
        try {

            //return $this->listTables();
            return array(
                //$this->getActiveStake(),
                //$this->getLiveStake(),
                $this->getCirculatingSupply(),
                $this->getMaxSupply(),
                $this->getReservesSupply(),
                $this->getTotalSupply(),
                $this->getTreasurySupply(),
                $this->getLockedSupply(),
                $this->connection->getServerVersion()
            );

            //return $this->getCirculatingSupply();

        } catch (\Exception $e) {
            $this->message = 'Failed to execute the SQL query: ' . $e->getMessage();
        }

        return $this->message;
    }

    public function listTables()
    {
        try {
            //$connection = $this->postgresConnector->getConnection();
            $this->message = $this->connection->listTables();
        } catch (\Exception $e) {
            $this->message = 'Failed to execute the SQL query: ' . $e->getMessage();
        }

        return $this->message;
    }

    public function loadSqlFileForQuery()
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/network.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getSyncPercent()
    {
        //$connection = $this->postgresConnector->getConnection();

        $select = $this->connection->select()
            ->from(['b' => 'block'], [])
            ->columns([
                'sync_percent' => new \Zend_Db_Expr('100 * (EXTRACT(epoch FROM (MAX(time) AT TIME ZONE \'UTC\')) - 
                    EXTRACT(epoch FROM (MIN(time) AT TIME ZONE \'UTC\'))) / 
                    (EXTRACT(epoch FROM (NOW() AT TIME ZONE \'UTC\')) - 
                    EXTRACT(epoch FROM (MIN(time) AT TIME ZONE \'UTC\')))')
            ]);

        return $this->connection->fetchRow($select);
    }

/**
 *********************************
 ******* NETWORK
 *********************************
 */

    public function getLastEpoch()
    {
        $select = $this->connection->select()
            ->from('block', ['epoch_no'])
            ->order('id DESC')
            ->limit(1);
            
        $lastEpochNumber = $this->connection->fetchOne($select);
        return $lastEpochNumber;
    }

    /**
     * https://github.com/blockfrost/blockfrost-backend-ryo/blob/master/src/sql/network/network_epoch.sql
     * 
     */
    public function getNetworkEpoch()
    {
        $select = $this->connection->select()
            ->from(['e' => 'epoch'], ['no']) // 'e' is the alias for the table
            ->order('e.no DESC') // ordering by 'no' column in descending order
            ->limit(1); // limiting the result to one row

        $result = $this->connection->fetchRow($select);

        return $result;
    }
    /**
     * https://github.com/blockfrost/blockfrost-backend-ryo/blob/master/src/sql/network/network_protocols.sql
     *
     */
    public function getNetworkProtocols()
    {
        //$connection = $this->postgresConnector->getConnection();

        $select = $this->connection->select()
            ->distinct(true)
            ->from('param_proposal', ['protocol_major', 'epoch_no'])
            ->where('protocol_major IS NOT NULL')
            ->order('epoch_no');

        $results = $this->connection->fetchAll($select);
        return $results;
    }

    public function getNetwork()
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/network_all.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getActiveStake()
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/active_stake.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getLiveStake()
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/live_stake.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getCirculatingSupply()
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/circulating_supply.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getLockedSupply()
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/locked_supply.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getMaxSupply()
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/max_supply.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getReservesSupply()
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/reserves_supply.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getTotalSupply()
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/total_supply.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getTreasurySupply()
    {
        // Path to your .sql file
        $filePath = '/home/bizon/Projects/Veralidity/magento-demo/app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/treasury_supply.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    // public function getLiveStake()
    // {
    //     $network = $this->getNetwork();
    //     if (isset($network[0]['stake'])) {
    //         $stakeData = json_decode($network[0]['stake'], true);
    //         if (isset($stakeData['live'])) {
    //             return $stakeData['live'];
    //         }
    //     }
        
    //     return 'N/A';

    // }
}
