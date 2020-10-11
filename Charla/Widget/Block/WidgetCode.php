<?php
namespace Charla\Widget\Block;
use Magento\Framework\View\Element\Template;

class WidgetCode extends \Magento\Framework\View\Element\Template
{

    protected $_helper;

    public function __construct(
        Template\Context $context, 
        array $data = array(),
        \Charla\Widget\Helper\Data $helper)
    {
        parent::__construct($context, $data);
        $this->_helper = $helper;
    
    }

    public function getPropertyId() {

        return $this->_helper->getPropertyId();
    }
}