<?php
/**
 * Copyright © Agile Codex Ltd. All rights reserved.
 * License:  https://www.agilecodex.com/license-agreement
 * @author   Agile Codex
*/

namespace Acx\BrandSlider\Model;

use Acx\BrandSlider\Api\Data\BrandInterface;
use Magento\Framework\Model\AbstractModel;

/** Brand Model */
class Brand extends AbstractModel implements BrandInterface
{
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'brand_slider';

    /**
     * Brand's statuses
     */
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;

    protected function _construct()
    {
        $this->_init(ResourceModel\Brand::class);
    }

    /** @return string|null */
    public function getName(): string {
        return $this->getData('name');
    }

    /**
     * @param $brandName
     * @return BrandInterface
     */
    public function setName($brandName): BrandInterface {
        return $this->setData('name', $brandName);
    }

    /**
     * @return int
     */
    public function getSortOrder(): int {
        return $this->getData('sort_order');
    }

    /**
     * @param $sortOrder
     * @return BrandInterface
     */
    public function setSortOrder($sortOrder): BrandInterface{
        return $this->setData('sort_order', $sortOrder);
    }

    /**
     * @return int
     */
    public function getStatus(): int {
        return $this->getData('status');
    }

    /**
     * @param $status
     * @return BrandInterface
     */
    public function setStatus($status): BrandInterface{
        return $this->setData('status', $status);
    }

    /**
     * @return string|null
     */
    public function getImage(): string {
        return $this->getData('image');
    }

    /**
     * @param $image
     * @return BrandInterface
     */
    public function setImage($image): BrandInterface{
        return $this->setData('image', $image);
    }

    /**
     * @return string|null
     */
    public function getImageAlt(): string {
        return $this->getData('image_alt');
    }

    /**
     * @param $imageAlt
     * @return BrandInterface
     */
    public function setImageAlt($imageAlt): BrandInterface{
        return $this->setData('image_alt', $imageAlt);
    }

    /**
     * @return array
     */
    public function getStoreId(): array {
        return $this->getData('store_ids');
    }

    /**
     * @param $storeIds
     * @return BrandInterface
     */
    public function setStoreIds($storeIds): BrandInterface {
        return $this->setData('store_ids', $storeIds);
    }

    /**
     * @return BrandInterface
     */
    public function unsetStoreIds(): BrandInterface {
        return $this->unsetData('store_ids');
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime {
        return new \DateTime($this->getData('update_time'));
    }

    /**
     * @param \DateTime $value
     * @return BrandInterface
     */
    public function setUpdatedAt(\DateTime $value): BrandInterface{
        return $this->setData('update_time', $value);
    }
}
