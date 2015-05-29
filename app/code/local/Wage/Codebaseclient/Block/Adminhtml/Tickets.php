<?php
/**
 *
 * @author Wagento
 */
class Wage_Codebaseclient_Block_Adminhtml_Tickets extends Mage_Adminhtml_Block_Widget_Grid_Container {


    public function __construct()
  {
    $this->_controller = 'adminhtml_tickets';
    $this->_blockGroup = 'codebaseclient';
      $resource = Mage::getSingleton('core/resource');

      $readConnection = $resource->getConnection('core_read');
      $table = $resource->getTableName('codebaseclient/refreshtime');
      $time = $readConnection->fetchCol('SELECT update_time FROM ' . $table . ' WHERE code = "ticket_refresh" ');
    if($time[0]){
        $this->_headerText = Mage::helper('codebaseclient')->__('Tickets (Last refreshed on %s)',$time[0]);
    } else {
        $this->_headerText = Mage::helper('codebaseclient')->__('Tickets');
    }
    $this->_addButtonLabel = Mage::helper('codebaseclient')->__('Create Ticket');
    parent::__construct();
      $this->_addButton('adminhtml_tickets', array(
          'label' => $this->__('Refresh Tickets'),
          'onclick' => "setLocation('{$this->getUrl('*/*/refresh')}')",
      ));
  }

	
}
