<?php

/**
 *  Copyright © Agile Codex Ltd. All rights reserved.
 *  License: https://www.agilecodex.com/license-agreement
 */
namespace Acx\BrandSlider\Controller\Adminhtml\Brand;

/**
 * Brand Index action.
 * @author Agile Codex
 */
use Magento\Backend\App\Action as AppAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends AppAction implements HttpGetActionInterface
{
    /** @var PageFactory */
    private $pageFactory;

    public function __construct(Context $context, PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend(__('Brand List'));

        return $page;
    }
}
