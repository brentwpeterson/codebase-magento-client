<?php
/**
 *
 * @author Wagento
 */
class Wage_Codebaseclient_Block_Adminhtml_Tickets_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $projects = explode(',',Mage::getStoreConfig('codebaseclient/general/projects'));
      $projectsArray = array();
      foreach($projects as $project){
          $item = array();
          $item['value'] = $project;
          $item['label'] = ' '.ucfirst($project);
          $projectsArray[] = $item;
      }
      $fieldset = $form->addFieldset('tickets_form', array('legend'=>Mage::helper('codebaseclient')->__('Ticket Information')));


      $fieldset->addField('project', 'select', array(
          'label'     => Mage::helper('codebaseclient')->__('Project'),
          'name'      => 'project',
          'value'     => this.value,
          'values' => $projectsArray,
          'required'  => true,
      ));

      $fieldset->addField('summary', 'text', array(
          'label'     => Mage::helper('codebaseclient')->__('Ticket Title'),
          'required'  => true,
          'name'      => 'summary',
      ));
     
      $fieldset->addField('description', 'textarea', array(
          'label'     => Mage::helper('codebaseclient')->__('Description'),
          'required'  => true,
          'name'      => 'description',
      ));



      if ( Mage::getSingleton('adminhtml/session')->getTicketsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTicketsData());
          Mage::getSingleton('adminhtml/session')->setTicketsData(null);
      } elseif ( Mage::registry('tickets_data') ) {
          $form->setValues(Mage::registry('tickets_data')->getData());
      }
      return parent::_prepareForm();
  }
}
