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

use Veralidity\Framework\Model\AbstractModel;
use Veralidity\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Zend_Db_Adapter_Pdo_Pgsql;

/**
 * Class PostgresConnector
 *
 * This class manages the connection to the PostgreSQL database.
 */
class PostgresConnector extends AbstractModel
{

    public const CARDANODBSYNC_CONNECTION = 'pgsql';
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var Zend_Db_Adapter_Pdo_Pgsql|null
     */
    protected $connection;

    /**
     * PostgresConnector constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->connection = $this->getConnection();
    }

    /**
     * Initializes the database connection.
     *
     * @throws LocalizedException
     */
    protected function createConnection($config = null)
    {
        try {
            if (is_null($config)) {
                // Get the database configuration from Magento's resource connection (env.php)
                $config = $this->resourceConnection->getConnection(self::CARDANODBSYNC_CONNECTION)->getConfig();
            }

            $config = $this->unsetKeys($config);

            $this->connection = new \Zend_Db_Adapter_Pdo_Pgsql($config);
            //return ($this->connection->->getConnection() ? $this->connection : false);

        } catch (\Exception $e) {
            throw new LocalizedException(__('Unable to connect to the database: %1', $e->getMessage()));
        }

        if ($this->connection->getConnection()) {
            return $this->connection;
        } else {
            return false;
        }
    }

    /**
     * Unsets Configuration Keys not needed by PostgreSQL
     *
     * @return array
     */
    public function unsetKeys($config)
    {
        unset($config['key']);
        unset($config['isAjax']);
        unset($config['network']);
        unset($config['port']);
        unset($config['form_key']);
        unset($config['model']);
        unset($config['engine']);
        unset($config['initStatements']);
        unset($config['type']);
        unset($config['active']);
        return $config;
    }
    /**
     * Retrieves the database connection.
     *
     * If the connection is not initialized, it initializes the connection.
     *
     * @return Zend_Db_Adapter_Pdo_Pgsql|null
     */
    public function testConnection($config)
    {
        return $this->createConnection($config);
    }

    /**
     * Retrieves the database connection.
     *
     * If the connection is not initialized, it initializes the connection.
     *
     * @return Zend_Db_Adapter_Pdo_Pgsql|null
     */
    public function getConnection($config = null)
    {
        if (!$this->connection) {
            $this->createConnection($config);
        }
        return $this->connection;
    }
}