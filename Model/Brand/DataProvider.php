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
use Acx\BrandSlider\Service\ImageService;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Category\FileInfo;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Io\File as FileSystemIO;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

/**
 * @inheritdoc
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

    /** @var FileSystemIO */
    private $fileSystemIo;

    /** @var ReadInterface */
    private $mediaDirectory;

    /** @var ImageService */
    private $imageService;

    /** @var ImageUploader */
    private $imageUploader;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $blockCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManager $storeManager
     * @param ImageHelper $imageHelper
     * @param FileSystemIO $fileSystemIo
     * @param array $meta
     * @param array $data
     * @param FileInfo|null $fileInfo
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
        FileSystemIO $fileSystemIo,
        Filesystem $filesystem,
        ImageService $imageService,
        ImageUploader $imageUploader,
        Mime $mime,
        array $meta = [],
        array $data = [],
        FileInfo $fileInfo = null,
        PoolInterface $pool = null
    ) {
        $this->collection = $blockCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->fileSystemIo = $fileSystemIo;
        $this->fileInfo = $fileInfo ?: ObjectManager::getInstance()->get(FileInfo::class);
        $this->imageService = $imageService;
        $this->imageUploader = $imageUploader;
        $this->mime = $mime;
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
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
            $data = $brand->getData();
            $data = $this->prepareImageData($data, 'image');
            $this->loadedData[$brand->getId()] = $data;
        }

        return $this->loadedData;
    }

    /**
     * @param array  $data
     * @param string $imageKey
     *
     * @return array
     */
    private function prepareImageData($data, $imageKey)
    {
        if (isset($data[$imageKey])) {
            $imageName = $data[$imageKey];
            unset($data[$imageKey]);
            if ($this->mediaDirectory->isExist($this->getFilePath($imageName))) {
                $data[$imageKey] = [
                    [
                        'name' => $imageName,
                        'url'  => $this->imageService->getImageUrl($imageName),
                        'size' => $this->mediaDirectory->stat($this->getFilePath($imageName))['size'],
                        'type' => $this->getMimeType($imageName),
                    ],
                ];
            }
        }

        return $data;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function getMimeType($fileName)
    {
        $absoluteFilePath = $this->mediaDirectory->getAbsolutePath($this->getFilePath($fileName));

        return $this->mime->getMimeType($absoluteFilePath);
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function getFilePath($fileName)
    {
        return $this->imageUploader->getFilePath($this->imageUploader->getBasePath(), $fileName);
    }

    /**
     * Converts brand image data to acceptable for rendering format
     *
     * @param array $dataSet
     * @return array
     */
    private function convertValues($dataSet): array
    {
        if (!is_array($dataSet)) {
            $dataSet = [];
        }
        foreach ($dataSet as $i => $data) {
            foreach ($data as $key => $value) {
                if ($key == 'image') {
                    $fileName = $value;

                    if ($this->fileInfo->isExist($fileName)) {
                        $stat = $this->fileInfo->getStat($fileName);
                        $mime = $this->fileInfo->getMimeType($fileName);

                        $data[$key] = [];
                        /** @var FileSystemIO $fileSystemIo **/
                        $fileInfo = $this->fileSystemIo->getPathInfo($fileName);
                        $basename = $fileInfo['basename'];
                        $data[$key][0]['name'] = $basename;

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
