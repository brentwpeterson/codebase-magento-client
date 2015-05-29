<?php
/**
 *
 * @author Wagento
 */
class Wage_Codebaseclient_Block_Adminhtml_Tickets_Grid_Renderer_Link extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    protected function _getValue(Varien_Object $row)
    {      
        $val = $row->getData($this->getColumn()->getIndex());
	$permalink = $row->getData($this->getColumn()->getPermalink());
        $url = Mage::getStoreConfig('codebaseclient/general/host').'/projects/'.$permalink.'/tickets/'.$val;
        //$out = $url; 
        $out = "<div><a href=$url target='_blank'>Ticket-".$val."</a></div>"; 
        return $out;
    }
}
