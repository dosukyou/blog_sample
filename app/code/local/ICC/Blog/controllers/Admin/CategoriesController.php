<?php 

class ICC_Blog_Admin_CategoriesController extends Mage_Adminhtml_Controller_Action { 


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
            ->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('Blog Categories'))
            ->_addBreadcrumb(Mage::helper('cms')->__('Manage Blog'), Mage::helper('cms')->__('Manage Blog Categories'))
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
             ->_title($this->__('Manage Blog Categories'));

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
             ->_title($this->__('Categories'))
             ->_title($this->__('Manage Blog Categories'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('category_id');
        $model = Mage::getModel('blog/categories');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getData('category_id')) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blog')->__('This category no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Category'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('admin_categories', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('blog')->__('Edit Category')
                    : Mage::helper('blog')->__('New Category'),
                $id ? Mage::helper('blog')->__('Edit Category')
                    : Mage::helper('blog')->__('New Category'));

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
            $model = Mage::getModel('blog/categories');

            if ($id = $this->getRequest()->getParam('category_id')) {

                $model->load($id);
            }

            $model->setData($data);

            Mage::dispatchEvent('blog_category_prepare_save', array('blog' => $model, 'request' => $this->getRequest()));


            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('This store has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('category_id' => $model->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('blog')->__('An error occurred while saving the category.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('category_id' => $this->getRequest()->getParam('category_id')));
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
        if ($id = $this->getRequest()->getParam('category_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('blog/categories');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('The category has been deleted.'));
                // go to grid
                Mage::dispatchEvent('blog_category_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('blog_category_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('category_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Unable to find a category to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }





}