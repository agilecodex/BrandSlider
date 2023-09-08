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
    /** Brand slider cache tag */
    public const CACHE_TAG = 'acx_bs_b';

    /** @var string */
    protected $_eventPrefix = 'brand_slider';

    /** Brand's statuses */
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;

    protected function _construct()
    {
        $this->_init(ResourceModel\Brand::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_'
            . str_replace(' ', '_', $this->getName())];
    }

    /**
     * @param $id
     * @return BrandInterface
     */
    public function setBrandId($id) {
        return $this->setData(self::BRAND_ID, $id);
    }

    /**
     * @return mixed
     */
    public function getBrandId(){
        return $this->getData(self::BRAND_ID);
    }

    /** @return string|null */
    public function getName(): ?string {
        return $this->getData(self::NAME);
    }

    /**
     * @param $brandName
     * @return BrandInterface
     */
    public function setName($brandName): BrandInterface {
        return $this->setData(self::NAME, $brandName);
    }

    /**
     * @return int|null
     */
    public function getSortOrder(): ?int {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @param $sortOrder
     * @return BrandInterface
     */
    public function setSortOrder($sortOrder): BrandInterface{
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int {
        return $this->getData(self::STATUS);
    }

    /**
     * @param $status
     * @return BrandInterface
     */
    public function setStatus($status): BrandInterface{
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string {
        return $this->getData(self::IMAGE);
    }

    /**
     * @param $image
     * @return BrandInterface
     */
    public function setImage($image): BrandInterface{
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @return string|null
     */
    public function getImageAlt(): ?string {
        return $this->getData(self::IMAGE_ALT);
    }

    /**
     * @param $imageAlt
     * @return BrandInterface
     */
    public function setImageAlt($imageAlt): BrandInterface{
        return $this->setData(self::IMAGE_ALT, $imageAlt);
    }

    /**
     * @return array|null
     */
    public function getStoreId(): ?array {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @param $storeIds
     * @return BrandInterface
     */
    public function setStoreIds($storeIds): BrandInterface {
        return $this->setData(self::STORE_ID, $storeIds);
    }

    /**
     * @return BrandInterface
     */
    public function unsetStoreIds(): BrandInterface {
        return $this->unsetData('store_id');
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * @param string $value
     * @return BrandInterface
     */
    public function setUpdatedAt($value): BrandInterface{
        return $this->setData(self::UPDATE_TIME, $value);
    }
}
