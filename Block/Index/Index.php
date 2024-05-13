<?php

declare(strict_types=1);

namespace Veralidity\CardanoDbSync\Block\Index;

use Veralidity\CardanoDbSync\Model\TestConnection2;

class Index extends \Magento\Framework\View\Element\Template
{

    protected $testConnection2;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        TestConnection2 $testConnection2,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->testConnection2 = $testConnection2;
        parent::__construct($context, $data);
    }

    public function getBlocksLatest()
    {
        return $this->testConnection2->getBlocksLatest();
    }

    public function getTest()
    {
        return $this->testConnection2->test();
    }

    function getTableSchema($table)
    {
        return $this->testConnection2->getTableSchema($table);
    }

    public function getServerVersion()
    {
        return $this->testConnection2->getConnection()->getServerVersion();
    }


    public function getTables()
    {
        return $this->testConnection2->listTables();
    }

    public function describeTable($table)
    {
        return $this->testConnection2->describeTable($table);
    }

    public function getSyncPercent()
    {
        return $this->testConnection2->getSyncPercent();
    }
}