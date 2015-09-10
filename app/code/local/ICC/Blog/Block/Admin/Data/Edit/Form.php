<?php 

class ICC_Blog_Block_Admin_Data_Edit_Form extends Mage_Adminhtml_Block_Widget_Form { 


    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('entry_id');
        $this->setTitle(Mage::helper('blog')->__('Entry'));
    }


    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _prepareForm() {

    	$model = Mage::registry('admin_data');
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array(
                // 'add_variables' => false, 
                // 'add_widgets' => false,
                'files_browser_window_url' => $this->getBaseUrl().'incadmin/cms_wysiwyg_images/index/'
            ));




        $form = new Varien_Data_Form(array(
         	'id' => 'edit_form',
        	'action' => $this->getData('action'),
        	'method' => 'post',
        	'enctype' => 'multipart/form-data'
   		));

        $form->setHtmlIdPrefix('blog_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('blog')->__('Entry'), 'class' => 'fieldset-wide'));

        if ($model->getEntryId()) {
            $fieldset->addField('entry_id', 'hidden', array(
                'name' => 'entry_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('blog')->__('Title'),
            'title'     => Mage::helper('blog')->__('Title'),
            'required'  => true,
        ));


        $fieldset->addField('url_key', 'text', array(
            'name'      => 'url_key',
            'label'     => Mage::helper('blog')->__('URL Key'),
            'title'     => Mage::helper('blog')->__('URL Key'),
            'required'  => true,
        ));

        $fieldset->addField('thumbnail', 'image', array(
            'name'      => 'thumbnail',
            'label'     => Mage::helper('blog')->__('Thumbnail'),
            'title'     => Mage::helper('blog')->__('Thumbnail'),
            'required'  => true,
            'value'     => 'image/url',
            'after_element_html'     => Mage::helper('blog')->__('<small>Please submit a thumbnail 140px by 140px.</small>'),
        ));

        $fieldset->addField('image', 'image', array(
            'name'      => 'image',
            'label'     => Mage::helper('blog')->__('Image'),
            'title'     => Mage::helper('blog')->__('Image'),
            'required'  => true,
            'value'     => 'image/url',
            'after_element_html'     => Mage::helper('blog')->__('<small>Please submit an image  615px wide, height can vary.</small>'),
        ));


        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('blog')->__('Content'),
            'title'     => Mage::helper('blog')->__('Content'),
            'required'  => false,
            'config' => $wysiwygConfig, 
            'wysiwyg' => true
        ));

        $cats = Mage::getModel('blog/categories')->getCollection();
        $categories = array();
        foreach ($cats as $kitten) {
            $categories[] = array(
                'label' => $kitten->getData('name'),
                'value' => $kitten->getData('category_id')
            );
        }

        $fieldset->addField('category_id', 'multiselect', array(
            'name'      => 'category_id',
            'label'     => Mage::helper('blog')->__('Category'),
            'title'     => Mage::helper('blog')->__('Category'),
            'required'  => true,
            'onclick'   => 'return false;',
            'onchange'  => 'return false;',
            'values'    => $categories
        ));


        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();        

    }



}