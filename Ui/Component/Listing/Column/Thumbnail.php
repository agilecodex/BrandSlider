<?php
namespace Acx\BrandSlider\Ui\Component\Listing\Column;

use Acx\BrandSlider\Api\Data\BrandInterface;
use Acx\BrandSlider\Service\ImageService;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Ui\Component\Listing\Columns\Thumbnail as CatalogThumbnail;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;

class Thumbnail extends CatalogThumbnail
{
    public const ALT_FIELD = 'name';

    /** @var ImageHelper */
    protected $imageHelper;

    /** @var UrlInterface */
    protected $urlBuilder;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var ReadInterface */
    protected $mediaDirectory;

    /** @var ImageService */
    protected $imageService;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ImageHelper $imageHelper
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface      $context,
        UiComponentFactory    $uiComponentFactory,
        ImageHelper           $imageHelper,
        UrlInterface          $urlBuilder,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        ImageService $imageService,
        array                 $components = [],
        array                 $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $imageHelper, $urlBuilder, $components, $data);
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->imageService = $imageService;
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $imageName = $item[$fieldName];
                if (isset($imageName)) {
                    $url = $this->imageService->getImageUrl($imageName, BrandInterface::LOGO);
                    $item[$fieldName . '_src'] = $url;
                    $item[$fieldName . '_alt'] = $item['name'] ?? null;
                    $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                        'brand/brand/edit',
                        ['brand_id' => $item['brand_id']]
                    );
                }
            }
        }
        return $dataSource;
    }

    /**
     * Get Alt text
     *
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}
