<?php

/**
 * Copyright © Agile Codex Ltd. All rights reserved.
 * License:    https://www.agilecodex.com/license-agreement
 * Brand Repository
 * @module   BrandSlider
 * @author   dev@agilecodex.com
 */

namespace Acx\BrandSlider\Model;

use Acx\BrandSlider\Model\BrandInterface;
use Acx\BrandSlider\Model\BrandFactory;
use Acx\BrandSlider\Model\ResourceModel\Brand as BrandResourceModel;
use Acx\BrandSlider\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;
use Acx\BrandSlider\Api\Data\BrandSearchResultsInterface as SearchResultsFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;


class BrandRepository
{
    protected BrandResourceModel $resource;

    protected BrandFactory $brandFactory;

    protected BrandCollectionFactory $brandCollectionFactory;

    protected SearchResultsFactory $searchResultsFactory;

    private StoreManagerInterface $storeManager;

    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @param BrandResourceModel $resource
     * @param BrandFactory $brandFactory
     * @param BrandCollectionFactory $brandCollectionFactory
     * @param SearchResultsFactory $searchResultsFactory
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        BrandResourceModel $resource,
        BrandFactory $brandFactory,
        BrandCollectionFactory $brandCollectionFactory,
        SearchResultsFactory $searchResultsFactory,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->resource = $resource;
        $this->brandFactory = $brandFactory;
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Brand data
     *
     * @param BrandInterface $brand
     * @return Brand
     * @throws CouldNotSaveException
     */
    public function save(BrandInterface $brand)
    {
        try {
            $this->resource->save($brand);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $brand;
    }

    /**
     * Load Brand data by given Brand Identity
     *
     * @param int $brandId
     * @return Brand
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $brandId): BrandInterface
    {
        $brand = $this->brandFactory->create();
        $this->resource->load($brand, $brandId);

        if (!$brand->getId()) {
            throw new NoSuchEntityException(__('The Brand with the "%1" ID doesn\'t exist.', $brandId));
        }
        return $brand;
    }

    /**
     * Load Brand data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return \Acx\BrandSlider\Api\Data\BrandSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var \Acx\BrandSlider\Model\ResourceModel\Brand\Collection $collection */
        $collection = $this->brandCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var BrandSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Brand
     *
     * @param BrandInterface $brand
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(BrandInterface $brand)
    {
        try {
            $this->resource->delete($brand);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Brand by given Brand Identity
     *
     * @param string $brandId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $brandId)
    {
        return $this->delete($this->getById($brandId));
    }

    /**
     * get brand collection of brandslider.
     *
     * @return \Acx\BrandSlider\Model\ResourceModel\Brand\Collection
     */
    public function getBrandCollection()
    {
        $storeViewId = $this->storeManager->getStore()->getId();

        /** @var \Acx\BrandSlider\Model\ResourceModel\Brand\Collection $brandCollection */
        $brandCollection = $this->brandCollectionFactory->create()
            ->setStoreViewId($storeViewId)            
            ->addFieldToFilter('status', Status::STATUS_ENABLED)
            ->addFieldToFilter('store_id', ['in' => [0,$storeViewId]])
            ->setOrder('sort_order', 'ASC');
        
        return $brandCollection;
    }
    
    
    /**
     * get categories array.
     *
     * @return array
     */
    public function getCategoriesArray()
    {
        $categoriesArray = $this->_categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToSort('path', 'asc')
            ->load()
            ->toArray();

        $categories = array();
        foreach ($categoriesArray as $categoryId => $category) {
            if (isset($category['name']) && isset($category['level'])) {
                $categories[] = array(
                    'label' => $category['name'],
                    'level' => $category['level'],
                    'value' => $categoryId,
                );
            }
        }

        return $categories;
    }
}