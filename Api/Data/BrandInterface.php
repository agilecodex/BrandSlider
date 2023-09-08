<?php

/**
 *  Copyright © Agile Codex Ltd. All rights reserved.
 *  License: https://www.agilecodex.com/license-agreement
 */

namespace Acx\BrandSlider\Api\Data;

/**
 * Brand Service Contract
 * @author Agile Codex
 */
interface BrandInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const BRAND_ID  = 'brand_id';
    const NAME      = 'name';
    const SORT_ORDER = 'sort_order';
    const STATUS    = 'status';
    const IMAGE     = 'image';
    const IMAGE_ALT = 'image_alt';
    const UPDATE_TIME   = 'update_time';
    const STORE_ID     = 'store_id';

    const BASE_MEDIA_PATH = 'acx/brandslider/images';

    const BRAND_TARGET_SELF = 0;
    const BRAND_TARGET_PARENT = 1;
    const BRAND_TARGET_BLANK = 2;

    /**
     * @param $id
     * @return BrandInterface
     */
    public function setBrandId($id);

    /**
     * @return mixed
     */
    public function getBrandId();

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param $brandName
     * @return BrandInterface
     */
    public function setName($brandName): BrandInterface;

    /**
     * @return int|null
     */
    public function getSortOrder(): ?int;

    /**
     * @param $sortOrder
     * @return BrandInterface|null
     */
    public function setSortOrder($sortOrder): ?BrandInterface;

    /**
     * @return int|null
     */
    public function getStatus(): ?int;

    /**
     * @param $status
     * @return BrandInterface
     */
    public function setStatus($status): BrandInterface;

    /**
     * @return string|null
     */
    public function getImage(): ?string;

    /**
     * @param $image
     * @return BrandInterface
     */
    public function setImage($image): ?BrandInterface;

    /**
     * @return string|null
     */
    public function getImageAlt(): ?string;

    /**
     * @param $imageAlt
     * @return BrandInterface
     */
    public function setImageAlt($imageAlt): BrandInterface;

    /**
     * @return array|null
     */
    public function getStoreId(): ?array;

    /**
     * @param array $storeIds
     * @return BrandInterface
     */
    public function setStoreIds(array $storeIds): BrandInterface;

    /**
     * @return BrandInterface
     */
    public function unsetStoreIds(): BrandInterface;

    /**
     * @param string $value
     * @return BrandInterface
     */
    public function setUpdatedAt($value): BrandInterface;

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

}
