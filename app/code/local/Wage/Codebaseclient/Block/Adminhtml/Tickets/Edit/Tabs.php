<?php
/**
 *
 * @author Wagento
 */
class Wage_Codebaseclient_Block_Adminhtml_Tickets_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('tickets_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('codebaseclient')->__('Ticket Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('codebaseclient')->__('Ticket Information'),
          'title'     => Mage::helper('codebaseclient')->__('Ticket Information'),
          'content'   => $this->getLayout()->createBlock('codebaseclient/adminhtml_tickets_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
