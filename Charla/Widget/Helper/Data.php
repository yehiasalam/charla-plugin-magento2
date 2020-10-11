<?php
namespace Charla\Widget\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_scopeConfig; 

    public function __construct( 
            \Magento\Framework\App\Helper\Context $context, 
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig ) {

             parent::__construct($context); 
             $this->_scopeConfig = $scopeConfig;
    }

    public function getPropertyId()
    {
        return $this->_scopeConfig->getValue(
            'charla/general/property', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ); 
        
    }
}
