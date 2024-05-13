<?php
namespace Veralidity\CardanoDbSync\Plugin\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Veralidity\CardanoDbSync\Model\PostgresConnector;

class CustomResolver implements ResolverInterface
{
    /**
     * @var PostgresConnector
     */
    protected $postgresConnector;

    /**
     * CustomResolver constructor.
     * @param PostgresConnector $postgresConnector
     */
    public function __construct(
        PostgresConnector $postgresConnector
    ) {
        $this->postgresConnector = $postgresConnector;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): Value
    {
        // Implement logic to fetch data from PostgreSQL using the PostgresConnector
        $data = $this->postgresConnector->test(); // Implement this method in your PostgresConnector class

        // Return the data
        return new Value($data);
    }
}
