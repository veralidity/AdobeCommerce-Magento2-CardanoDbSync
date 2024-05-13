<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Veralidity, LLC
 *
 * @package    Veralidity
 * @category   CardanoDbSync
 * @copyright  Copyright © 2024 Veralidity, LLC
 * @license    https://www.veralidity.com/license/
 * @author     Veralidity, LLC <veralidity@protonmail.com>
 */
namespace Veralidity\CardanoDbSync\Model\Adminhtml\Source;

/**
 * Class Mode
 */
class Network implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Configuration path
     */
    const CONFIG_NETWORK_PATH = 'veraliditycardanodbsync/general/network';

    /**
     * @var array
     */
    protected $options;

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            $this->options = array();
            $this->options[] = array('value' => '', 'label' => '--- Please Select ---');
            foreach ($this->getData() as $value => $label) {
                $this->options[] = array('value' => $value, 'label' => $label);
            }
        }
        return $this->options;
    }

    /**
     * Mode Data
     *
     * @return array
     */
    public function getData(): array
    {
        return [
            'preview' => 'Preview',
            'preprod' => 'Preprod',
            'mainnet' => 'Mainnet',
        ];
    }
}
