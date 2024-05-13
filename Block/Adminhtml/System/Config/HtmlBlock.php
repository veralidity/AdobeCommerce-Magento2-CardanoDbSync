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
namespace Veralidity\CardanoDbSync\Block\Adminhtml\System\Config;

/**
 * Cardano DbSync PostgreSQL test connection block
 */
class HtmlBlock extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Render HTML content for the configuration field
     *
     * This method is responsible for rendering the HTML content
     * for the configuration field defined in the system configuration.
     * It retrieves the template content and returns it as HTML.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Instantiate a Magento Object Manager instance
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // Create a template block instance
        $templateBlock = $objectManager->create(\Magento\Framework\View\Element\Template::class);
        // Set the template file for the block instance
        $templateBlock->setTemplate('Veralidity_CardanoDbSync::system/config/html.phtml');
        // Render the block to HTML
        $html = $templateBlock->toHtml();

        return $html;
    }

}
