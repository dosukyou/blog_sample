<?php 

class ICC_Blog_Block_Admin_Data extends Mage_Adminhtml_Block_Widget_Grid_Container { 


    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'blog';
        $this->_controller = 'admin_data';
        $this->_headerText = Mage::helper('blog')->__('Manage Blog Entries');

        parent::__construct();

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('add', 'label', Mage::helper('cms')->__('Add New Blog Entry'));
        } else {
            $this->_removeButton('add');
        }

    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('blog/admin_data/' . $action);
    }



}