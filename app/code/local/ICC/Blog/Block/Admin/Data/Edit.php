<?php 

class ICC_Blog_Block_Admin_Data_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct()
    {
        $this->_objectId = 'entry_id';
        $this->_blockGroup = 'blog';
        $this->_controller = 'admin_data';

        parent::__construct();





        $this->_addButton('save_draft', array(
            'label'     => 'Save as Draft',
            'onclick'   => 'editForm.submit(\''.$this->_getSaveDraftUrl().'\')',
        ), 1, 1, 'header');

        $this->_updateButton('save', 'label', Mage::helper('cms')->__('Publish Entry'));
        $this->_updateButton('delete', 'label', Mage::helper('cms')->__('Delete Entry'));
    }   


    protected function _getSaveDraftUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'save_as_draft' => 1
        ));
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('admin_data')->getId()) {
            return Mage::helper('blog')->__("Edit Entry '%s'", $this->escapeHtml(Mage::registry('admin_data')->getTitle()));
        }
        else {
            return Mage::helper('blog')->__('New Entry');
        }
    }

}