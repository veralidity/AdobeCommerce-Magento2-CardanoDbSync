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
use Veralidity\Framework\Model\AbstractModel;
use Veralidity\CardanoDbSync\Model\PostgresConnector;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;

/**
 * Handler for logging Whitelisted IPs
 */
class Network implements \Veralidity\CardanoDbSync\Api\TestConnectionInterface2
{
    protected $postgresConnector;

    protected $connection;

    public $message;

    protected $fileDriver;

    /**
     * @var ModuleDirReader
     */
    private $moduleDirReader;

    /**
     * Constructor
     *
     * @codingStandardsIgnoreStart
     * @SupassetsWarnings(PHPMD.ExcessiveParameterList)
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param PostgresConnector $postgresConnector
     * @param ModuleDirReader $moduleDirReader
     */
    public function __construct(
        File $fileDriver,
        PostgresConnector $postgresConnector,
        ModuleDirReader $moduleDirReader
    ) {
        $this->fileDriver = $fileDriver;
        $this->postgresConnector = $postgresConnector;
        $this->moduleDirReader = $moduleDirReader;

        $this->connection = $this->postgresConnector->getConnection();

    }

    public function getConnection()
    {

        if (!$this->connection) {
            $this->connection = $this->postgresConnector->getConnection();
        }
        return $this->connection;
    }

    /**
     * Test the PostgreSQL database connection.
     *
     * @return string
     */
    public function test()
    {
        try {

            return $this->loadSqlFileForQuery();
            //return $this->listTables();
            // return array(
            //    /* // $this->getActiveStake(),
            //     // $this->getLiveStake(),*/
            //     $this->getCirculatingSupply(),
            //     $this->getMaxSupply(),
            //     $this->getReservesSupply(),
            //     $this->getTotalSupply(),
            //     $this->getTreasurySupply(),
            //     $this->getLockedSupply(),
            //     //$this->connection->getServerVersion()
            // );

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

    public function getTableSchema($table)
    {
        //$connection = $this->resourceConnection->getConnection();

        $select = $connection->select()
            ->from(
                ['st' => $table],
                []
            )
            ->joinLeft(
                ['c' => $table],
                'c.table_schema = st.schemaname AND c.table_name = st.relname',
                [
                    'ordinal_position',
                    'table_schema',
                    'table_name',
                    'column_name',
                    'data_type',
                    'udt_name' => 'udt_name'
                ]
            )
            ->joinLeft(
                ['pgd' => $table],
                'pgd.objsubid = c.ordinal_position AND pgd.objoid = st.relid',
                ['description']
            )
            ->where('st.schemaname NOT IN (?)', [$table, 'information_schema']);

        $schemaData = $connection->fetchAll($select);
        
        return $schemaData;

    }

    public function describeTable($table)
    {
        try {
            //$connection = $this->postgresConnector->getConnection();
            $this->message = $this->connection->describeTable($table);
            $sql = "SELECT json_object_keys(to_json(json_populate_record(NULL::schema_name." . $table . ", '{}'::JSON)))";
            $select = $connection->fetchAll($sql);
        } catch (\Exception $e) {
            $this->message = 'Failed to execute the SQL query: ' . $e->getMessage();
        }

        return $this->message;
    }

    public function getFilePath($file)
    {
        if (!empty($file) && !is_null($file)) {
            $moduleName = 'Veralidity_CardanoDbSync'; // The name of your module
            $relativePath = 'sql/cardanodbsync/' . $file;
            
            // Get the absolute path to the module's directory
            $moduleDir = $this->moduleDirReader->getModuleDir('', $moduleName);
            
            // Build the full path to the required file
            $filePath = $moduleDir . '/' . $relativePath;

            return $filePath;
        }
    }

    public function loadSqlFileForQuery()
    {
        // Path to your .sql file
        $filePath = $this->getFilePath('network/network_all.sql');
        //$filePath = 'app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/network_all.sql';

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

    public function getBlocksLatest()
    {
        // Path to your .sql file
        $filePath = $this->getFilePath('blocks/blocks_latest.sql');
        //$filePath = 'app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/blocks/blocks_latest.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getNetwork()
    {
        // Path to your .sql file
        $filePath = $this->getFilePath('network/network_all.sql');
        //$filePath = 'app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/network_all.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getActiveStake()
    {
        // Path to your .sql file
        $filePath = $this->getFilePath('network/active_stake.sql');
        //$filePath = 'app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/active_stake.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getLiveStake()
    {
        // Path to your .sql file
        $filePath = $this->getFilePath('network/live_stake.sql');
        //$filePath = 'app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/live_stake.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getCirculatingSupply()
    {
        // Path to your .sql file
        $filePath = $this->getFilePath('network/circulating_supply.sql');
        //$filePath = 'app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/circulating_supply.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getLockedSupply()
    {
        // Path to your .sql file
        $filePath = $this->getFilePath('network/locked_supply.sql');
        //$filePath = 'app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/locked_supply.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getMaxSupply()
    {
        // Path to your .sql file
        $filePath = $this->getFilePath('network/max_supply.sql');
        //$filePath = 'app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/max_supply.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getReservesSupply()
    {
        // Path to your .sql file
        $filePath = $this->getFilePath('network/reserves_supply.sql');
        //$filePath = 'app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/reserves_supply.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getTotalSupply()
    {
        // Path to your .sql file
        $filePath = $this->getFilePath('network/total_supply.sql');
        //$filePath = 'app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/total_supply.sql';

        // Read the content of the .sql file
        $sqlQuery = $this->fileDriver->fileGetContents($filePath);

        // Execute the SQL query using your PostgresConnector or any other method
        $result = $this->connection->fetchAll($sqlQuery);
        return $result;
    }

    public function getTreasurySupply()
    {
        // Path to your .sql file
        $filePath = $this->getFilePath('network/treasury_supply.sql');
        //$filePath = 'app/code/Veralidity/CardanoDbSync/sql/cardanodbsync/network/treasury_supply.sql';

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
