<?php 

class ICC_Blog_Admin_DataController extends Mage_Adminhtml_Controller_Action { 
    /**
     * Init actions
     * @return Mage_Adminhtml_Cms_PageController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('cms/blog')
            ->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('Blog'))
            ->_addBreadcrumb(Mage::helper('cms')->__('Manage Blog'), Mage::helper('cms')->__('Manage Blog Entries'))
        ;
        return $this;
    }
    /**
     * Index action
     */
    public function indexAction()
    {
        
        $this->_title($this->__('CMS'))
             ->_title($this->__('Blog'))
             ->_title($this->__('Manage Blog Entries'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new CMS page
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }


    /**
     * Edit CMS page
     */
    public function editAction()
    {  
        $this->_title($this->__('Blog'))
             ->_title($this->__('Entries'))
             ->_title($this->__('Manage Entries'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('entry_id');
        $model = Mage::getModel('blog/data');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blog')->__('This entry no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Entry'));

        // 2.5 Detect if it's a Draft 
        if ($model->getSaveAsDraft()) { 

            Mage::getSingleton('adminhtml/session')->addNotice(
                Mage::helper('blog')->__('This entry is a DRAFT. '));

        }


        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('admin_data', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('blog')->__('Edit Entry')
                    : Mage::helper('blog')->__('New Entry'),
                $id ? Mage::helper('blog')->__('Edit Entry')
                    : Mage::helper('blog')->__('New Entry'));

        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            //init model and set data
            $model = Mage::getModel('blog/data');

            if ($id = $this->getRequest()->getParam('entry_id')) {
                $model->load($id);
            }

            $catIds = $data['category_id'];
            $data['category_id'] = implode(',', $catIds);

            /* Thumbnails */ 
            if (isset($_FILES['thumbnail']['name']) && (file_exists($_FILES['thumbnail']['tmp_name']))) {
                try {

                    $uploader = new Varien_File_Uploader('thumbnail');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                 
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                   
                    $path = Mage::getBaseDir('media') . DS . 'blog' . DS;
                               
                    $uploader->save($path, $_FILES['thumbnail']['name']);
                    
                    $data['thumbnail'] = '/blog/'.$_FILES['thumbnail']['name'];

                } catch (Exception $e) { }

            } else { 
                if (isset($data['thumbnail']['delete']) && $data['thumbnail']['delete'] == 1)  {
                    $data['thumbnail'] = '';
                } else { 
                    $data['thumbnail'] = $model->getData('thumbnail');
                }

            }

            /* Image */ 
            if (isset($_FILES['image']['name']) && (file_exists($_FILES['image']['tmp_name']))) {
                try {

                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                 
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                   
                    $path = Mage::getBaseDir('media') . DS . 'blog' . DS;
                               
                    $uploader->save($path, $_FILES['image']['name']);
                    
                    $data['image'] = '/blog/'.$_FILES['image']['name'];

                } catch (Exception $e) { }

            } else { 
                if (isset($data['image']['delete']) && $data['image']['delete'] == 1)  {
                    $data['image'] = '';
                } else { 
                    $data['image'] = $model->getData('image');
                }

            }


            Mage::dispatchEvent('blog_data_prepare_save', array('blog' => $model, 'request' => $this->getRequest()));


            if ($draft = $this->getRequest()->getParam('save_as_draft')) { 
                $data['save_as_draft'] = (int) $draft; 
            } else { 
                $data['save_as_draft'] = 0;
            }


            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('This entry has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('entry_id' => $model->getId(), '_current'=>true));
                    return;
                }


                // stay in entry
               $this->_redirect('*/*/edit', array('entry_id' => $this->getRequest()->getParam('entry_id')));
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('blog')->__('An error occurred while saving the store.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('entry_id' => $this->getRequest()->getParam('entry_id')));
            return;
        }
        $this->_redirect('*/*/');
    }


    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('entry_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('blog/data');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('The store has been deleted.'));
                // go to grid
                Mage::dispatchEvent('blog_data_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('blog_data_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('entry_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Unable to find an entry to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }


}