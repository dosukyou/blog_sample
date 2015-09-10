<?php 

class ICC_Blog_Block_Admin_Data_Grid_Renderer_Multiselect extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action { 

    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }


	public function _getValue(Varien_Object $row)
    {
        if ($getter = $this->getColumn()->getGetter()) {
            $val = $row->$getter();
        }

        $val = $row->getData($this->getColumn()->getIndex());
        $cat_array = explode(',', $val);
        $out = '';
        foreach ($cat_array as $cat) {
            $kitty = Mage::getModel('blog/categories')->load($cat);
            $out .= $kitty->getName().'<br/>';
        }

        return $out;

    }


}