<?php

/**
 *  Copyright © Agile Codex Ltd. All rights reserved.
 *  License: https://www.agilecodex.com/license-agreement
 */

namespace Acx\BrandSlider\Model\ResourceModel\Brand;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Brand Collection
 * @module   BrandSlider
 * @author   Agile Codex
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Acx\BrandSlider\Model\Brand', 'Acx\BrandSlider\Model\ResourceModel\Brand');
    }
}
