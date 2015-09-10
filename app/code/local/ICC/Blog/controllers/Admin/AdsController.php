<?php 

class ICC_Blog_Admin_AdsController extends Mage_Adminhtml_Controller_Action { 


    /**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_PageController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('cms/blog')
            ->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('Blog'))
            ->_addBreadcrumb(Mage::helper('cms')->__('Manage Blog'), Mage::helper('cms')->__('Manage Blog Ads'))
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
             ->_title($this->__('Manage Blog Ads'));

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
             ->_title($this->__('Ads'))
             ->_title($this->__('Manage Ads'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('entry_id');
        $model = Mage::getModel('blog/ads');

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

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Ads'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('admin_ads', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('blog')->__('Edit Ads')
                    : Mage::helper('blog')->__('New Ads'),
                $id ? Mage::helper('blog')->__('Edit Ads')
                    : Mage::helper('blog')->__('New Ads'));

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
            $model = Mage::getModel('blog/ads');

            if ($id = $this->getRequest()->getParam('entry_id')) {
                $model->load($id);
            }

            /* Thumbnails */ 
            if (isset($_FILES['side_ad_1']['name']) && (file_exists($_FILES['side_ad_1']['tmp_name']))) {
                try {

                    $uploader = new Varien_File_Uploader('side_ad_1');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                 
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                   
                    $path = Mage::getBaseDir('media') . DS . 'blog' . DS;
                               
                    $uploader->save($path, $_FILES['side_ad_1']['name']);
                    
                    $data['side_ad_1'] = '/blog/'.$_FILES['side_ad_1']['name'];

                } catch (Exception $e) { }

            } else { 
                if (isset($data['side_ad_1']['delete']) && $data['side_ad_1']['delete'] == 1)  {
                    $data['side_ad_1'] = '';
                } else { 
                    $data['side_ad_1'] = $model->getData('side_ad_1');
                }

            }


            /* Thumbnails */ 
            if (isset($_FILES['side_ad_2']['name']) && (file_exists($_FILES['side_ad_2']['tmp_name']))) {
                try {

                    $uploader = new Varien_File_Uploader('side_ad_2');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                 
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                   
                    $path = Mage::getBaseDir('media') . DS . 'blog' . DS;
                               
                    $uploader->save($path, $_FILES['side_ad_2']['name']);
                    
                    $data['side_ad_2'] = '/blog/'.$_FILES['side_ad_2']['name'];

                } catch (Exception $e) { }

            } else { 
                if (isset($data['side_ad_2']['delete']) && $data['side_ad_2']['delete'] == 1)  {
                    $data['side_ad_2'] = '';
                } else { 
                    $data['side_ad_2'] = $model->getData('side_ad_2');
                }
            }


            /* Thumbnails */ 
            if (isset($_FILES['side_ad_3']['name']) && (file_exists($_FILES['side_ad_3']['tmp_name']))) {
                try {

                    $uploader = new Varien_File_Uploader('side_ad_3');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                 
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                   
                    $path = Mage::getBaseDir('media') . DS . 'blog' . DS;
                               
                    $uploader->save($path, $_FILES['side_ad_3']['name']);
                    
                    $data['side_ad_3'] = '/blog/'.$_FILES['side_ad_3']['name'];

                } catch (Exception $e) { }

            } else { 
                if (isset($data['side_ad_3']['delete']) && $data['side_ad_3']['delete'] == 1)  {
                    $data['side_ad_3'] = '';
                } else { 
                    $data['side_ad_3'] = $model->getData('side_ad_3');
                }

            }


            /* Image */ 
            if (isset($_FILES['instagram_banner']['name']) && (file_exists($_FILES['instagram_banner']['tmp_name']))) {
                try {

                    $uploader = new Varien_File_Uploader('instagram_banner');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                 
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                   
                    $path = Mage::getBaseDir('media') . DS . 'blog' . DS;
                               
                    $uploader->save($path, $_FILES['instagram_banner']['name']);
                    
                    $data['instagram_banner'] = '/blog/'.$_FILES['instagram_banner']['name'];

                } catch (Exception $e) { }

            } else { 
                if (isset($data['instagram_banner']['delete']) && $data['instagram_banner']['delete'] == 1)  {
                    $data['instagram_banner'] = '';
                } else { 
                    $data['instagram_banner'] = $model->getData('instagram_banner');
                }

            }

            Mage::dispatchEvent('blog_ads_prepare_save', array('blog' => $model, 'request' => $this->getRequest()));


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

                $this->_redirect('*/*/edit', array('entry_id' => $this->getRequest()->getParam('entry_id')));
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('blog')->__('An error occurred while saving the ad.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('entry_id' => $this->getRequest()->getParam('entry_id')));
            return;
        }
       $this->_redirect('*/*/edit', array('entry_id' => $this->getRequest()->getParam('entry_id')));
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
                $model = Mage::getModel('blog/ads');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('These ads have been deleted.'));
                // go to grid
                Mage::dispatchEvent('blog_ads_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('blog_ads_on_delete', array('title' => $title, 'status' => 'fail'));
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