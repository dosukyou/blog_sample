<?php 

class IDG_Blog_Block_Admin_Ads_Edit_Form extends Mage_Adminhtml_Block_Widget_Form { 


    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('entry_id');
        $this->setTitle(Mage::helper('blog')->__('Blog Ads'));
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

    	$model = Mage::registry('admin_ads');
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array(
                'add_variables' => false, 
                'add_widgets' => false,
                'files_browser_window_url' => $this->getBaseUrl().'starsgento/cms_wysiwyg_images/index/'
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


        $fieldset->addField('side_ad_1', 'image', array(
            'name'      => 'side_ad_1',
            'label'     => Mage::helper('blog')->__('Side Ad 1'),
            'title'     => Mage::helper('blog')->__('Side Ad 1'),
            'required'  => true,
            'value'     => 'image/url',
            'after_element_html'     => Mage::helper('blog')->__('<small>Please submit an image 300px by 655px.</small>'),
        ));

        $fieldset->addField('side_ad_1_link', 'text', array(
            'name'      => 'side_ad_1_link',
            'label'     => Mage::helper('blog')->__('Side Ad 1 Link'),
            'title'     => Mage::helper('blog')->__('Side Ad 1 Link'),
            'required'  => true,
        ));

        $fieldset->addField('side_ad_2', 'image', array(
            'name'      => 'side_ad_2',
            'label'     => Mage::helper('blog')->__('Side Ad 2'),
            'title'     => Mage::helper('blog')->__('Side Ad 2'),
            'required'  => true,
            'value'     => 'image/url',
            'after_element_html'     => Mage::helper('blog')->__('<small>Please submit a image 300px by 300px.</small>'),
        ));

        $fieldset->addField('side_ad_2_link', 'text', array(
            'name'      => 'side_ad_2_link',
            'label'     => Mage::helper('blog')->__('Side Ad 2 Link'),
            'title'     => Mage::helper('blog')->__('Side Ad 2 Link'),
            'required'  => true,
        ));

        $fieldset->addField('side_ad_3', 'image', array(
            'name'      => 'side_ad_3',
            'label'     => Mage::helper('blog')->__('Side Ad 3'),
            'title'     => Mage::helper('blog')->__('Side Ad 3'),
            'required'  => true,
            'value'     => 'image/url',
            'after_element_html'     => Mage::helper('blog')->__('<small>Please submit a thumbnail 300px by 410px.</small>'),
        ));

        $fieldset->addField('side_ad_3_link', 'text', array(
            'name'      => 'side_ad_3_link',
            'label'     => Mage::helper('blog')->__('Side Ad 3 Link'),
            'title'     => Mage::helper('blog')->__('Side Ad 3 Link'),
            'required'  => true,
        ));

        $fieldset->addField('instagram_banner', 'image', array(
            'name'      => 'instagram_banner',
            'label'     => Mage::helper('blog')->__('Instagram Banner'),
            'title'     => Mage::helper('blog')->__('Instagram Banner'),
            'required'  => true,
            'value'     => 'image/url',
            'after_element_html'     => Mage::helper('blog')->__('<small>Please submit an image 464px by 150px.</small>'),
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();        

    }



}