<?php 

class ICC_Blog_Block_Admin_Categories_Grid extends Mage_Adminhtml_Block_Widget_Grid {


	public function __construct() {
		parent::__construct();
		$this->setId('blogCategoriesGrid');
		$this->setDefaultStort('category_id');
		$this->setDefaultDir('ASC');
	}


	protected function _prepareCollection() {

		$collection = Mage::getModel('blog/categories')->getCollection();
		$this->setCollection($collection);

		return parent::_prepareCollection();

	}


	protected function _prepareColumns() {


		$this->addColumn('category_id', array(
			'header'	=> Mage::helper('blog')->__('Category ID'),
			'align'		=> 'center',
			'index'		=> 'category_id',
			'width'		=> '50px'
		));

		$this->addColumn('name', array(
			'header'	=> Mage::helper('blog')->__('Name'),
			'align'		=> 'left',
			'index'		=> 'name'
		));

		return parent::_prepareColumns();

	}

	protected function _afterLoadCollection() {

		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();

	}

	public function getRowUrl($row) {

		return $this->getUrl('*/*/edit', array('category_id' => $row->getId()));

	}


}