<?php
/**
 *  Copyright © Agile Codex Ltd. All rights reserved.
 *  License:    https://www.agilecodex.com/license-agreement
 */

namespace Acx\BrandSlider\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Brand Resource Model
 * @module   BrandSlider
 * @author   Agile Codex
 */
class Brand extends AbstractDb
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('acx_brandslider_brand', 'id');
    }
}
