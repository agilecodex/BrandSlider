<?php
/**
 *  Copyright © Agile Codex Ltd. All rights reserved.
 *  License: https://www.agilecodex.com/license-agreement
 */
namespace Acx\BrandSlider\Block\Adminhtml;

/**
 * Brand grid container.
 * @module   BrandSlider
 * @author Agile Codex
 */
class Brand extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_brand';
        $this->_blockGroup = 'Acx_BrandSlider';
        $this->_headerText = __('Brands');
        $this->_addButtonLabel = __('Add New Brand');
        parent::_construct();
    }
}
