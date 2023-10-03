<?php
/**
 *  Copyright © Agile Codex Ltd. All rights reserved.
 *  License: https://www.agilecodex.com/license-agreement
 */
namespace Acx\BrandSlider\Block\Widget;

use Acx\BrandSlider\Model\Brand as BrandModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Widget Block
 */
class BrandSlider extends Template implements BlockInterface, IdentityInterface
{
    protected function _construct()
    {
        parent::_construct();
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
