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
    const BASE_MEDIA_PATH = 'acx/brandslider/images';

    const BRAND_TARGET_SELF = 0;
    const BRAND_TARGET_PARENT = 1;
    const BRAND_TARGET_BLANK = 2;

    /**
     * @param $id
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string|null
     */
    public function getName():string;

    /**
     * @param $brandName
     * @return BrandInterface
     */
    public function setName($brandName): BrandInterface;

    /**
     * @return int
     */
    public function getSortOrder():int;

    /**
     * @param $sortOrder
     * @return BrandInterface|null
     */
    public function setSortOrder($sortOrder): ?BrandInterface;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @param $status
     * @return BrandInterface
     */
    public function setStatus($status): BrandInterface;

    /**
     * @return string|null
     */
    public function getImage(): string;

    /**
     * @param $image
     * @return BrandInterface
     */
    public function setImage($image): ?BrandInterface;

    /**
     * @return string|null
     */
    public function getImageAlt(): string;

    /**
     * @param $imageAlt
     * @return BrandInterface
     */
    public function setImageAlt($imageAlt): BrandInterface;

    /**
     * @return array
     */
    public function getStoreId(): array;

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
     * @param \DateTime $value
     * @return BrandInterface
     */
    public function setUpdatedAt(\DateTime $value): BrandInterface;

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime;

}
