<?php

/**
 *  Copyright © Agile Codex Ltd. All rights reserved.
 *  License: https://www.agilecodex.com/license-agreement
 */

namespace Acx\BrandSlider\Controller\Adminhtml\Brand;

/**
 * NewAction
 * @author Agile Codex
 */
class NewAction extends \Acx\BrandSlider\Controller\Adminhtml\Brand
{
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();
         $this->_getSession()->unsBrandName();
         $this->_getSession()->unsImageAlt();
        return $resultForward->forward('edit');
    }
}
