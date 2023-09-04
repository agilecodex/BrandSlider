<?php
/**
 * Copyright © Agile Codex Ltd. All rights reserved.
 * License:    https://www.agilecodex.com/license-agreement
 * @author   agilecodex.com
 */
namespace Acx\BrandSlider\Model;

use Acx\BrandSlider\Api\BrandRepositoryInterface;
use Acx\BrandSlider\Api\Data\BrandSearchResultsInterface;
use Acx\BrandSlider\Api\Data\BrandInterface;
use Acx\BrandSlider\Model\BrandFactory;
use Acx\BrandSlider\Model\ResourceModel\Brand as BrandResourceModel;
use Acx\BrandSlider\Model\ResourceModel\Brand\Collection;
use Acx\BrandSlider\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;
use Acx\BrandSlider\Api\Data\BrandSearchResultsInterfaceFactory as ResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;


class BrandRepository implements BrandRepositoryInterface
{
    protected BrandResourceModel $resource;

    protected BrandFactory $brandFactory;

    protected BrandCollectionFactory $brandCollectionFactory;

    protected ResultsInterfaceFactory $searchResultsFactory;

    protected StoreManagerInterface $storeManager;

    protected CollectionProcessorInterface $collectionProcessor;

    /**
     * @param BrandResourceModel $resource
     * @param BrandFactory $brandFactory
     * @param BrandCollectionFactory $brandCollectionFactory
     * @param ResultsInterfaceFactory $searchResultsFactory
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        BrandResourceModel $resource,
        BrandFactory $brandFactory,
        BrandCollectionFactory $brandCollectionFactory,
        ResultsInterfaceFactory $searchResultsFactory,
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
     * @param string $brandId
     * @return BrandInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(string $brandId): BrandInterface
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
    public function getList(SearchCriteriaInterface $criteria): BrandSearchResultsInterface
    {
        /** @var \Acx\BrandSlider\Model\ResourceModel\Brand\Collection $collection */
        $collection = $this->brandCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var \Acx\BrandSlider\Api\Data\BrandSearchResultsInterface $searchResults */
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
    public function delete(BrandInterface $brand): bool
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
    public function deleteById(int $brandId): bool
    {
        return $this->delete($this->getById($brandId));
    }

    /**
     * get brand collection of brandslider.
     *
     * @return \Acx\BrandSlider\Model\ResourceModel\Brand\Collection
     */
    public function getBrandCollection(): Collection
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

}
