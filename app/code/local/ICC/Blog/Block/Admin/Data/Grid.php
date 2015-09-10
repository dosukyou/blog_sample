<?php 

class ICC_Blog_Block_Admin_Data_Grid extends Mage_Adminhtml_Block_Widget_Grid {


	public function __construct() {
		parent::__construct();
		$this->setId('blogDataGrid');
		$this->setDefaultStort('entry_id');
		$this->setDefaultDir('ASC');
	}


	protected function _prepareCollection() {

		$collection = Mage::getModel('blog/data')->getCollection();
		$this->setCollection($collection);

		return parent::_prepareCollection();

	}


	protected function _prepareColumns() {


		$this->addColumn('entry_id', array(
			'header'	=> Mage::helper('blog')->__('Entry ID'),
			'align'		=> 'center',
			'index'		=> 'entry_id',
			'width'		=> '50px'
		));

      	$this->addColumn('thumbnail', array(
            'header'        => Mage::helper('blog')->__('Thumbnail'),
        	'index'   	=> 'thumbnail',
        	'renderer' 	=> 'blog/admin_data_grid_renderer_image',
        	'width'   	=> '100px'
      	));

		$this->addColumn('title', array(
			'header'	=> Mage::helper('blog')->__('Title'),
			'align'		=> 'left',
			'index'		=> 'title',
			'width'		=> '650px'
		));

		$this->addColumn('created_at', array(
			'header'	=> Mage::helper('blog')->__('Created At'),
			'type'		=> 'datetime',
			'index'		=> 'created_at'
		));


		$cats = Mage::getModel('blog/categories')->getCollection();
		$categories = array();
		foreach ($cats as $kitten) {
			$categories[$kitten->getData('category_id')] = $kitten->getData('name'); 
		}


		$this->addColumn('category_id', array(
			'header'    => Mage::helper('blog')->__('Category'),
			'align'     => 'left',
			'index'     => 'category_id',
			'type'		=> 'options',
			'renderer' 	=> 'blog/admin_data_grid_renderer_multiselect',
			'options'   => $categories
		));

		return parent::_prepareColumns();

	}

	protected function _afterLoadCollection() {

		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();

	}

	public function getRowUrl($row) {

		return $this->getUrl('*/*/edit', array('entry_id' => $row->getId()));

	}


}