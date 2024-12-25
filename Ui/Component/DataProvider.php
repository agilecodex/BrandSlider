<?php
declare(strict_types=1);
/**
 * Copyright © Agile Codex Ltd. All rights reserved.
 * @website www.agilecodex.com
 */
namespace Acx\BrandSlider\Ui\Component;

use Acx\BrandSlider\Service\ImageService;
use Acx\BrandSlider\Api\Data\BrandInterface;
use Acx\BrandSlider\Model\ResourceModel\Brand\Grid\CollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /** @var ImageService  */
    private $imageService;

    /** @var AuthorizationInterface */
    private $authorization;

    /** @var CollectionFactory */
    protected $collection;

    /**
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param ImageService $imageService
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param AuthorizationInterface $authorization
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        ImageService $imageService,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        AuthorizationInterface $authorization,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->imageService = $imageService;
        $this->authorization = $authorization;
        $this->meta = array_replace_recursive($meta, $this->prepareMetadata());
    }

    public function getData()
    {
        return $this->collection->toArray();
    }

    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];

        $arrItems['items'] = [];
        /** @var BrandInterface|\Magento\Framework\DataObject $item */
        foreach ($searchResult->getItems() as $item) {
            $itemData = $item->getData();

            if ($item->getData(BrandInterface::LOGO)) {
                $itemData[BrandInterface::LOGO . '_src'] =
                    $this->imageService->getImageUrl($item->getLogo(), 'logo');
            }

            $arrItems['items'][] = $itemData;
        }

        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        return $arrItems;
    }

    /**
     * Prepares Meta
     *
     * @return array
     */
    public function prepareMetadata()
    {
        $metadata = [];

        if (!$this->authorization->isAllowed('Acx_BrandSlider::brandslider')) {
            $metadata = [
                'brand_brand_columns' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'editorConfig' => [
                                    'enabled' => false
                                ],
                                'componentType' => \Magento\Ui\Component\Container::NAME
                            ]
                        ]
                    ]
                ]
            ];
        }

        return $metadata;
    }
}
