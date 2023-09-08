<?php
/**
 * Copyright © Agile Codex Ltd. All rights reserved.
 * License:    https://www.agilecodex.com/license-agreement
 * @author   agilecodex.com
 */
namespace Acx\BrandSlider\Model\Brand;

use Acx\BrandSlider\Model\Brand as BrandModel;
use Acx\BrandSlider\Model\ResourceModel\Brand\Collection as BrandCollection;
use Acx\BrandSlider\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Category\FileInfo;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

/**
 * Class DataProvider
 */
class DataProvider extends ModifierPoolDataProvider
{
    /** @var BrandCollection */
    protected $collection;

    /** @var DataPersistorInterface */
    protected $dataPersistor;

    /** @var array */
    protected $loadedData;

    /** @var FileInfo */
    private $fileInfo;

    /** @var StoreManager */
    private $storeManager;

    /** @var ImageHelper */
    private $imageHelper;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $blockCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $blockCollectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManager $storeManager,
        ImageHelper $imageHelper,
        array $meta = [],
        array $data = [],
        FileInfo $fileInfo = null,
        PoolInterface $pool = null
    ) {
        $this->collection = $blockCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->fileInfo = $fileInfo ?: ObjectManager::getInstance()->get(FileInfo::class);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var BrandModel $brand */
        foreach ($items as $brand) {
            $this->loadedData[$brand->getId()] = $brand->getData();
        }

        $this->loadedData = $this->convertValues($this->loadedData);

        /*$data = $this->dataPersistor->get('brandslider_brand');
        if (!empty($data)) {
            $brand = $this->collection->getNewEmptyItem();
            $brand->setData($data);
            $this->loadedData[$brand->getId()] = $brand->getData();
            $this->dataPersistor->clear('brandslider_brand');
        }*/

        return $this->loadedData;
    }

    /**
     * Converts brand image data to acceptable for rendering format
     *
     * @param array $dataSet
     * @return array
     */
    private function convertValues($dataSet): array
    {
        foreach ($dataSet as $i => $data) {
            foreach ($data as $key => $value) {
                if ($key == 'image') {
                    $fileName = $value;

                    if ($this->fileInfo->isExist($fileName)) {
                        $stat = $this->fileInfo->getStat($fileName);
                        $mime = $this->fileInfo->getMimeType($fileName);

                        $data[$key] = array();
                        $data[$key][0]['name'] = basename($fileName);

                        $url = '';
                        if ($value != '') {
                            $url = $this->storeManager->getStore()->getBaseUrl() . $value;
                        } else {
                            $url = $this->imageHelper->getDefaultPlaceholderUrl('thumbnail');
                        }
                        $data[$key][0]['url'] = $url;

                        $data[$key][0]['size'] = $stat['size'];
                        $data[$key][0]['type'] = $mime;
                    }
                }
            }
            $dataSet[$i] = $data;
        }

        return $dataSet;
    }
}
