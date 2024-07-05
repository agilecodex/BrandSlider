<?php
/**
 * @copyright Copyright (c) AgileCodex (https://www.agilecodex.com/)
 * @license https://www.agilecodex.com/license-agreement
 */

declare(strict_types=1);

namespace Acx\BrandSlider\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    protected $storeId;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeId     = $storeManager->getStore()->getStoreId();
    }

    /**
     * {@inheritdoc}
     */
    public function isShowLogoOnProduct()
    {
        return $this->scopeConfig->getValue(
            'brand/logo_for_product/show_logo_on_product',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLogoWidthForProductPage()
    {
        $logoWidth = $this->scopeConfig->getValue(
            'brand/logo_for_product/logo_width_for_product',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        if (!$logoWidth) {
            $logoWidth = 30;
        }

        return $logoWidth;
    }
}
