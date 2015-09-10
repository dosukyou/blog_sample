<?php 

class IDG_Blog_Block_Admin_Ads_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct()
    {
        $this->_objectId = 'entry_id';
        $this->_blockGroup = 'blog';
        $this->_controller = 'admin_ads';

        parent::__construct();

        
        $this->_removeButton('delete');
        $this->_updateButton('save', 'label', Mage::helper('cms')->__('Save Entry'));
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('admin_ads')->getEntryId()) {
            return Mage::helper('blog')->__("Edit Blog Ads", $this->escapeHtml(Mage::registry('admin_ads')->getEntryId()));
        }
        else {
            return Mage::helper('blog')->__('New Ads');
        }
    }

}