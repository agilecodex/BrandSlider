<?php

/**
 *  Copyright © Agile Codex Ltd. All rights reserved.
 *  License: https://www.agilecodex.com/license-agreement
 */

namespace Acx\BrandSlider\Controller\Adminhtml\Brand;

use Acx\BrandSlider\Controller\Adminhtml\Brand as AbastractBrand;
use Acx\BrandSlider\Model\Brand;
use Acx\BrandSlider\Model\Brand\Image;
use Acx\BrandSlider\Model\BrandFactory;
use Acx\BrandSlider\Model\BrandRepository;
use Acx\BrandSlider\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Backend\App\Action\Context as BackendContext;
use Magento\Backend\Helper\Js;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
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

    /** @var BrandRepository */
    protected $brandRepository;

    public function __construct(
        BackendContext $context,
        UploaderFactory $uploaderFactory,
        Image $imageModel,
        BrandFactory $brandFactory,
        CollectionFactory $brandCollectionFactory,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        PageFactory $resultPageFactory,
        LayoutFactory $resultLayoutFactory,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager,
        Js $jsHelper,
        BrandRepository $brandRepository
    ) {
        parent::__construct($context, $brandFactory, $brandCollectionFactory,
                $coreRegistry, $fileFactory, $resultPageFactory, $resultLayoutFactory,
                $resultForwardFactory, $storeManager, $jsHelper);

        $this->uploaderFactory = $uploaderFactory;
        $this->imageModel = $imageModel;
        $this->brandRepository = $brandRepository;
    }

    /**
     * @var PageFactory
     */
    public function execute() {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            if (isset($data['status']) && $data['status'] === 'true') {
                $data['status'] = Brand::STATUS_ENABLED;
            }
            if (empty($data['id'])) {
                $data['id'] = null;
            }
            $model = $this->_brandFactory->create();

            if ($id = $this->getRequest()->getParam(static::PARAM_CRUD_ID)) {
                try {
                    $model = $this->brandRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This brand no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $data = $this->imageModel->beforeSave($data);
            $model->setData($data);

            try {
                $this->brandRepository->save($model);

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
