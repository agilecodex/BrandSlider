<?php

/**
 *  Copyright © Agile Codex Ltd. All rights reserved.
 *  License: https://www.agilecodex.com/license-agreement
 */

namespace Acx\BrandSlider\Controller\Adminhtml\Brand;

use Acx\BrandSlider\Controller\Adminhtml\Brand as AbastractBrand;
use Acx\BrandSlider\Model\Brand\Image;
use Acx\BrandSlider\Model\BrandFactory;
use Acx\BrandSlider\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Backend\App\Action\Context as BackendContext;
use Magento\Backend\Helper\Js;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Save Brand action.
 *
 * @author Agile Codex
 */
class Save extends AbastractBrand
{
    /** @var UploaderFactory  */
    protected $uploaderFactory;

    /** @var Image  */
    protected $imageModel;

    public function __construct(
        BackendContext                                               $context,
        UploaderFactory                                              $uploaderFactory,
        Image                                                        $imageModel,
        BrandFactory                          $brandFactory,
        CollectionFactory $brandCollectionFactory,
        Registry                                  $coreRegistry,
        FileFactory             $fileFactory,
        PageFactory                   $resultPageFactory,
        LayoutFactory                 $resultLayoutFactory,
        ForwardFactory            $resultForwardFactory,
        StoreManagerInterface                   $storeManager,
        Js $jsHelper
    ) {
        parent::__construct($context, $brandFactory, $brandCollectionFactory,
                $coreRegistry, $fileFactory, $resultPageFactory, $resultLayoutFactory,
                $resultForwardFactory, $storeManager, $jsHelper);

        $this->uploaderFactory = $uploaderFactory;
        $this->imageModel = $imageModel;
    }

    /**
     * @var PageFactory
     */
    public function execute() {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            $model = $this->_brandFactory->create();

            if ($id = $this->getRequest()->getParam(static::PARAM_CRUD_ID)) {
                $model->load($id);
            }

            $imageRequest = $this->getRequest()->getFiles('image');

            $fileName = isset($imageRequest['name']) && strlen($imageRequest['name']) > 0
                            ? $imageRequest['name'] : '';

            if (isset($data['store_id'][0])) {
                $data['store_id'] = $data['store_id'][0];
            } elseif (!isset($data['store_id'])) {
                try {
                    $data['store_id'] = $this->storeManager->getStore()->getId();
                } catch (NoSuchEntityException $e) {
                }
            }

            $model->setData($data);

            try {
                $model->save();

                $this->messageManager->addSuccess(__('The brand has been saved.'));
                $this->_getSession()->setFormData(false);

                return $this->_getBackResultRedirect($resultRedirect, $model->getId());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the brand.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [static::PARAM_CRUD_ID => $this->getRequest()->getParam(static::PARAM_CRUD_ID)]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }

}
