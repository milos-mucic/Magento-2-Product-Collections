<?php

namespace Younify\ProductCollections\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
//use Magento\Review\Model\ReviewFactory;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
//use Magento\Store\Model\StoreManagerInterface;

/**
 * Class NewProducts
 * @package Mageplaza\Productslider\Block
 */

class Sale extends AbstractProduct
{
    /**
     * @var CollectionFactory
     */
    private $_productCollectionFactory;
    /**
     * @var Visibility
     */
    private $_catalogProductVisibility;
    /**
     * @var BestSellersCollectionFactory
     */
    protected $_bestSellersCollectionFactory;
//    /**
//     * @var ReviewFactory
//     */
//    protected $_reviewFactory;

    /**
     * BestSellerProducts constructor.
     * @param Context $context
     * @param Visibility $visibility
     * @param CollectionFactory $productCollectionFactory
//     * @param ReviewFactory $reviewFactory
     * @param DateTime $dateTime
     * @param BestSellersCollectionFactory $bestSellersCollectionFactory
//     * @param StoreManagerInterface $storeManager
     * @param array $data
     */

    public function __construct(
        Context $context,
        Visibility $visibility,
        CollectionFactory $productCollectionFactory,
//        ReviewFactory $reviewFactory,
        DateTime $dateTime,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
//        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $visibility;
//        $this->_storeManager = $storeManager;
//        $this->_reviewFactory = $reviewFactory;
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getSaleProducts()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection->addAttributeToSelect(['small_image', 'name', 'special_price', 'special_to_date','short_description', 'product_label']);
        $collection->addStoreFilter();
        $collection->addFinalPrice();
        $collection->addAttributeToSort('created_at', 'desc');
        $collection->getSelect()->where('price_index.final_price < price_index.price');
        $collection->setPageSize(6)
            ->setCurPage($this->getCurrentPage())
            ->setOrder('entity_id', 'DESC');
        return $collection;
    }

    /**
     * get featured product collection
     */
    public function getBestsellerProducts()
    {
        $productIds = [];
        $bestSellers = $this->_bestSellersCollectionFactory->create()
            ->setPeriod('year');
        foreach ($bestSellers as $product) {
            $productIds[] = $product->getProductId();
        }
        $collection = $this->_productCollectionFactory->create()->addIdFilter($productIds);
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*');
        return $collection;
    }

    /**
     * get collection of feature products
     * @return mixed
     */
    public function getFeaturedProducts()
    {
        $visibleProducts = $this->_catalogProductVisibility->getVisibleInCatalogIds();
        $collection = $this->_productCollectionFactory->create()->setVisibility($visibleProducts);
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_featured', '1');
        return $collection;
    }

    /**
     * @inheritdoc
     */
    public function getNewProducts()
    {
        $todayDate = date('Y-m-d');
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection->addAttributeToSelect(['small_image', 'name', 'short_description', 'product_label']);
        $collection->addStoreFilter();
        $collection->addFinalPrice();
        $collection->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ]
        )->addAttributeToFilter(
                [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
            );

        return $collection;
    }

    /*TO DO: getDailyDealsProducts() - filter should be improved according to needs*/
    /**
     * @return string
     */
    public function getDailyDealsProducts()
    {
        $todayDate = date('Y-m-d');
        $tomorrow = date("Y-m-d", strtotime('tomorrow'));
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection->addAttributeToSelect(['small_image', 'name', 'special_price', 'special_to_date', 'special_from_date','short_description', 'product_label']);
        $collection->addStoreFilter();
        $collection->addFinalPrice();
        $collection->addAttributeToSort('special_to_date', 'asc');
        $collection->addAttributeToFilter(
            'special_to_date',
            [
                'and' => [
                    0 => ['date' => true, 'from' => $tomorrow]                    
                ]
            ])->addAttributeToFilter(
                'special_from_date',
                [
                    'and' => [
                        0 => ['date' => true, 'to' => $todayDate]                        
                    ]
                ]
        );
        $collection->setPageSize(9)
            ->setCurPage($this->getCurrentPage());

        return $collection;
    }

//    public function getTopRatedProducts(){
//        $collection = $this->_productCollectionFactory->create()
//            ->addAttributeToSelect('*')
//            ->load();
//        $rating = array();
//        foreach ($collection as $product) {
//            $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
//            $ratingSummary = $product->getRatingSummary()->getRatingSummary();
//            if($ratingSummary!=null){
//                $rating[$product->getId()] = $ratingSummary;
//            }
//        }
//
//        return $ratingSummary;
//    }
}
