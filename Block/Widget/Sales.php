<?php

namespace Younify\ProductCollections\Block\Widget;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Sales extends Template implements BlockInterface
{

    protected $_template = "widget/saleWidget.phtml";

    /**
     * Sales constructor.
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Visibility $visibility
     * @param Context $contextImage
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Visibility $visibility,
        Context $contextImage,
        array $data = []
    )
    {
        $this->productCollectionFactory = $collectionFactory;
        $this->_catalogProductVisibility = $visibility;
        $this->imageBuilder = $contextImage->getImageBuilder();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getSaleProducts()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection->addAttributeToSelect(['small_image', 'name', 'short_description']);
        $collection->addStoreFilter();

        $collection->addFinalPrice();
        $collection->addAttributeToSort('created_at', 'desc');

        $collection->getSelect()->where('price_index.final_price < price_index.price');

        $collection->setPageSize(3)
            ->setCurPage($this->getCurrentPage())
            ->setOrder('entity_id', 'DESC');

        return $collection;
    }

    /**
     * @param $product
     * @param $imageId
     * @param array $attributes
     * @return Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }
}