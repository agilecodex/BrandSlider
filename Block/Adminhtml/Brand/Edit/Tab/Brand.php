<?php
/**
 *  Copyright © Agile Codex Ltd. All rights reserved.
 *  License: https://www.agilecodex.com/license-agreement
 */
namespace Acx\BrandSlider\Block\Adminhtml\Brand\Edit\Tab;

use Acx\BrandSlider\Model\Brand as BrandModel;
use Acx\BrandSlider\Model\Status;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Brand Edit tab.
 * * @author Agile Codex
 */
class Brand extends GenericForm implements TabInterface
{
    /** @var DataObjectFactory */
    protected $_objectFactory;

    /** @var BrandModel */
    protected $_brand;

    /** @var Config */
    protected $_wysiwygConfig;

    /** @var Store  */
    protected $_systemStore;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param DataObjectFactory $objectFactory
     * @param BrandModel $brand
     * @param Config $wysiwygConfig
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context             $context,
        Registry            $registry,
        FormFactory         $formFactory,
        DataObjectFactory   $objectFactory,
        BrandModel          $brand,
        Config          $wysiwygConfig,
        Store           $systemStore,
        array           $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_brand = $brand;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * prepare layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());

        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Acx\BrandSlider\Block\Adminhtml\Form\Renderer\Fieldset\Element',
                $this->getNameInLayout().'_fieldset_element'
            )
        );

        return $this;

    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('brand');

        $dataObj->addData($model->getData());

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Brand Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $elements = [];
        $elements['name'] = $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
            ]
        );

        $fieldset->addType('image', '\Acx\BrandSlider\Block\Adminhtml\Brand\Helper\Image');

        $image_path = null;
        if(preg_match('~\.(png|gif|jpe?g|bmp)~i', $model->getImage()))
              $image_path =  $model->getImage();

        $elements['image'] = $fieldset->addField(
            'image',
            'image',
            [
                'title' => __('Brand Image'),
                'label' => __('Brand Image'),
                'name' => 'image',
                'path' => $image_path,
                'note' => 'Allow image type: jpg, jpeg, gif, png',
                'required' => true,
                'value' => $image_path,
                'renderer' => 'Acx\BrandSlider\Block\Adminhtml\Brand\Helper\Renderer\Image'
            ]
        )->setAfterElementHtml('
        <script>

            require([
                 "jquery",
            ], function($){
                $(document).ready(function () {
                    if($("#page_image").attr("value")){
                        $("#page_image").removeClass("required-file");
                    }else{
                        $("#page_image").addClass("required-file");
                    }
                    $( "#page_image" ).attr( "accept", "image/x-png,image/gif,image/jpeg,image/jpg,image/png" );

                });
              });
       </script>
    ');

        $elements['image_alt'] = $fieldset->addField(
            'image_alt',
            'text',
            [
                'title' => __('Alt Text'),
                'label' => __('Alt Text'),
                'name' => 'image_alt',
                'note' => 'Used for SEO',
                'required' => true
            ]
        );

        $elements['Sort Oder'] = $fieldset->addField(
            'sort_order',
            'text',
            [
                'label' => __('Sort Oder'),
                'title' => __('Sort Oder'),
                'name' => 'sort_order'
            ]
        );

        $elements['status'] = $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Brand Status'),
                'name' => 'status',
                'options' => Status::getAvailableStatuses(),
            ]
        );

        $form->addValues($dataObj->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->_coreRegistry->registry('brand');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getBrand()->getId()
            ? __("Edit Brand '%1'", $this->escapeHtml($this->getBrand()->getName())) : __('New Brand');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Brand Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Brand Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
