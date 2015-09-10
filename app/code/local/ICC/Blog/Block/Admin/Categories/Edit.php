<?php 

class ICC_Blog_Block_Admin_Categories_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct()
    {
        $this->_objectId = 'category_id';
        $this->_blockGroup = 'blog';
        $this->_controller = 'admin_categories';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('cms')->__('Save Category'));
        $this->_updateButton('delete', 'label', Mage::helper('cms')->__('Delete Category'));

    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('admin_categories')->getId()) {
            return Mage::helper('blog')->__("Edit Category '%s'", $this->escapeHtml(Mage::registry('admin_categories')->getName()));
        }
        else {
            return Mage::helper('blog')->__('New Category');
        }
    }

}