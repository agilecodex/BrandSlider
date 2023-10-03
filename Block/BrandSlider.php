<?php
/**
 *  Copyright © Agile Codex Ltd. All rights reserved.
 *  License: https://www.agilecodex.com/license-agreement
 */
namespace Acx\BrandSlider\Block;

use Acx\BrandSlider\Api\BrandRepositoryInterface;
use Acx\BrandSlider\Model\Brand as BrandModel;
use Acx\BrandSlider\Model\BrandRepository;
use Acx\BrandSlider\Model\Status;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * BrandSlider Widget Block
 *
 * @author Agile Codex
 */
class BrandSlider extends Template implements IdentityInterface
{
    /** template for brand slider */
    public const TEMPLATE = 'Acx_BrandSlider::brandslider/brandslider.phtml';
    public const XML_CONFIG_BRANDSLIDER = 'brandslider/general/enable_frontend';

    /** Prefix for cache key of Brand Slider */
    public const CACHE_KEY_PREFIX = 'BRAND_SLIDER_';

    /** @var ScopeConfigInterface */
    protected $_scopeConfig;

    /** @var BrandRepository */
    protected $_brandRepository;

    /** @var Repository */
    protected $_assetRepo;

    /** @var BrandModel */
    private $brand;

    /**
     * @param Context $context
     * @param Repository $assetRepo
     * @param BrandRepositoryInterface $brandRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Repository $assetRepo,
        BrandRepositoryInterface $brandRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_brandRepository = $brandRepository;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_assetRepo = $assetRepo;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $store = $this->_storeManager->getStore()->getId();
        $configEnable = $this->_scopeConfig->getValue(
            self::XML_CONFIG_BRANDSLIDER,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if ($configEnable && $this->_brandRepository->getBrandCollection()->getSize()) {
            $this->setTemplate(self::TEMPLATE);
        }

        return parent::_toHtml();
    }

    /**
     * Get brand collection.
     *
     * @return \Acx\BrandSlider\Model\ResourceModel\Brand\Collection
     */
    public function getBrandCollection()
    {
        return $this->_brandRepository->getBrandCollection();
    }

    /**
     * Get brand image url.
     *
     * @param \Acx\BrandSlider\Model\Brand $brand
     *
     * @return string
     */
    public function getBrandImageUrl(\Acx\BrandSlider\Model\Brand $brand)
    {
        $srcImage = $this->getBaseUrlMedia($brand->getImage());
        if (!preg_match('~\.(png|gif|jpe?g|bmp)~i', $srcImage)) {
            $srcImage = $this->_assetRepo->getUrl("Acx_BrandSlider::images/brand-logo-blank.png");
        }
        return $srcImage;
    }

    /**
     * Get brand slider html.
     *
     * @return string
     */
    public function getBrandSliderHtmlId()
    {
        return 'acx-brandslider-brandslider';
    }

    /**
     * Get Base Url Media.
     *
     * @param string $path
     * @param bool   $secure
     *
     * @return string
     */
    public function getBaseUrlMedia($path = '', $secure = false)
    {
        return $this->_storeManager->getStore()
                ->getBaseUrl() . $path;
    }


    /**
     * Get Brand Url
     *
     * @return string
     */
    public function getBrandSliderBrandUrl()
    {
        return $this->_backendUrl->getUrl('*/*/brands', ['_current' => true]);
    }

    /**
     * Get Backend Url
     *
     * @param  string $route
     * @param  array  $params
     * @return string
     */
    public function getBackendUrl($route = '', $params = ['_current' => true])
    {
        return $this->_backendUrl->getUrl($route, $params);
    }

    /**
     * Get cache identities of the Brand
     *
     * @return array
     */
    public function getIdentities()
    {
        $brand = $this->getBrand();

        if ($brand) {
            return $brand->getIdentities();
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        $cacheKeyInfo[] = $this->_storeManager->getStore()->getId();
        return $cacheKeyInfo;
    }

    /**
     * Get brand
     *
     * @return BrandModel|null
     * @throws LocalizedException
     */
    private function getBrand(): ?BrandModel
    {
        if ($this->brand) {
            return $this->brand;
        }

        $brandId = $this->getData('brand_id');

        if ($brandId) {
            try {
                $storeId = $this->_storeManager->getStore()->getId();

                /** @var \Acx\BrandSlider\Model\Brand $brand */
                $brand = $this->_brandFactory->create();
                $brand->setStoreId($storeId)->load($brandId);
                $this->brand = $brand;

                return $brand;
            } catch (NoSuchEntityException $e) {
                throw new LocalizedException(__('The specified logo id does not exist.'));
            }
        }

        return null;
    }
}
