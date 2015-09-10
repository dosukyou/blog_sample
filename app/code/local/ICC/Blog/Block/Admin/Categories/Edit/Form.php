<?php 

class ICC_Blog_Block_Admin_Categories_Edit_Form extends Mage_Adminhtml_Block_Widget_Form { 


    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('category_id');
        $this->setTitle(Mage::helper('blog')->__('Category'));
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

    	$model = Mage::registry('admin_categories');

        $form = new Varien_Data_Form(array(
         	'id' => 'edit_form',
        	'action' => $this->getData('action'),
        	'method' => 'post',
        	'enctype' => 'multipart/form-data'
   		));

        $form->setHtmlIdPrefix('blog_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('blog')->__('Category'), 'class' => 'fieldset-wide'));

        if ($model->getCategoryId()) {
            $fieldset->addField('category_id', 'hidden', array(
                'name' => 'category_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('blog')->__('Name'),
            'title'     => Mage::helper('blog')->__('Name'),
            'required'  => true,
        ));


        $fieldset->addField('url_key', 'text', array(
            'name'      => 'url_key',
            'label'     => Mage::helper('blog')->__('URL Key'),
            'title'     => Mage::helper('blog')->__('URL Key'),
            'required'  => true,
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();        

    }



}