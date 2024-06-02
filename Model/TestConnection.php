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

use Magento\Framework\Model\AbstractModel;
use Veralidity\CardanoDbSync\Model\PostgresConnector;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;

/**
 * Handler for logging Whitelisted IPs
 */
class TestConnection extends AbstractModel
{
    protected $postgresConnector;

    protected $collectionFactory;

    public $message;

    /**
     * @var ModuleDirReader
     */
    private $moduleDirReader;

    public function __construct(
        PostgresConnector $postgresConnector
    ) {
        $this->postgresConnector = $postgresConnector;
    }

    /**
     * Test the PostgreSQL database connection.
     *
     * @return string
     */
    public function test()
    {
        try {
            //$connection = $this->postgresConnector->getConnection();
            //return $connection;//$this->getSyncPercent();
            return $this->listTables();
            //$this->message = 'Connected Successfully';

        } catch (\Exception $e) {
            $this->message = 'Failed to execute the SQL query: ' . $e->getMessage();
        }

        return $this->message;
    }

    public function getSyncPercent()
    {
        $connection = $this->postgresConnector->getConnection();

        $select = $connection->select()
            ->from(['b' => 'block'], [])
            ->columns([
                'sync_percent' => new \Zend_Db_Expr('100 * (EXTRACT(epoch FROM (MAX(time) AT TIME ZONE \'UTC\')) - 
                    EXTRACT(epoch FROM (MIN(time) AT TIME ZONE \'UTC\'))) / 
                    (EXTRACT(epoch FROM (NOW() AT TIME ZONE \'UTC\')) - 
                    EXTRACT(epoch FROM (MIN(time) AT TIME ZONE \'UTC\')))')
            ]);

        return $connection->fetchRow($select);
    }

    public function listTables()
    {
        try {
            $connection = $this->postgresConnector->getConnection();
            $this->message = $connection->listTables();
        } catch (\Exception $e) {
            $this->message = 'Failed to execute the SQL query: ' . $e->getMessage();
        }

        return $this->message;
    }
}
