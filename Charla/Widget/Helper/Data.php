<?php

namespace Charla\Widget\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_scopeConfig; 
    protected $log;
    protected $productRepositoryFactory;
    protected $storeManager;
    private $httpContext;
    protected $customer_session_factory;
    protected $checkout_session_factory;
    protected $block_cart;

    public function __construct( 
            \Magento\Framework\App\Helper\Context $context, 
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Charla\Widget\Helper\Log $log,
            \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Checkout\Model\SessionFactory $checkout_session_factory,
            \Magento\Customer\Model\SessionFactory $customer_session_factory,
            \Magento\Checkout\Block\Cart $block_cart,
            \Magento\Framework\App\Http\Context $httpContext ) {
            
                            
             parent::__construct($context); 

             $this->log = $log;
             $this->_scopeConfig = $scopeConfig;
             $this->productRepositoryFactory = $productRepositoryFactory;
             $this->storeManager = $storeManager;
             $this->customer_session_factory = $customer_session_factory;
             $this->checkout_session_factory = $checkout_session_factory;
             $this->block_cart = $block_cart;
             $this->httpContext = $httpContext;
    }

    public function getPropertyId()
    {
        return $this->_scopeConfig->getValue(
            'charla/general/property', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ); 
        
    }

    public function getCartTotals(){

        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);        

        // Get the products
        $quote = $this->checkout_session_factory->create()->getQuote();
        $products = $quote->getAllVisibleItems();
        if (is_null($products) || (count($products) == 0) ){
            $products = $this->block_cart->getItems();
        }
        $items = array();
        foreach($products as $product) {

            $p = $this->productRepositoryFactory->create()->getById($product->getProductId());

            $items[] = array(
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'quantity' => $product->getQty(),
                'price' => strval($product->getPrice()),
                'permalink' => $p->getUrlModel()->getUrl($p),
                'image' => array(
                    'source' => $mediaUrl . 'catalog/product' . $p->getData('thumbnail')
                )
            );
        }


        // Get logged in customer details
        $customer_id = 0;
        $customer_email = '';
        $customer = $this->customer_session_factory->create();
        $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        
        if( $isLoggedIn == 1){
            $customer_id = $customer->getCustomer()->getId();
            $customer_email = $customer->getCustomer()->getEmail();
        }

        // Get the totals
        $mtotals = $quote->getTotals();
        if (is_null( $mtotals['subtotal']['value'] )){
            $mtotals = $this->block_cart->getTotals();
        }
        $totals = (array(
            'subtotal' => is_null($mtotals['subtotal']['value']) ? '0' : strval($mtotals['subtotal']['value']),
            'shipping_total' => is_null($mtotals['shipping']['value']) ? '0' : strval($mtotals['shipping']['value']),
            'total' => is_null($mtotals['grand_total']['value']) ? '0' : strval($mtotals['grand_total']['value'])
        ));

        return json_encode(array(
            'shop' => 'magento2',
            'email' => $customer_email,
            'totals' => $totals,
            'items' => $items,
            'currency' => $quote->getQuoteCurrencyCode()
        ));

    }

}
