<?php
/**
 *
 * @author Wagento
 */
class Wage_Codebaseclient_Block_Adminhtml_Tickets_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_removeButton('back');
        $this->_removeButton('reset');

        $this->_objectId = 'id';
        $this->_blockGroup = 'codebaseclient';
        $this->_controller = 'adminhtml_tickets';
        
        $this->_updateButton('save', 'label', Mage::helper('codebaseclient')->__('Create Ticket'));
        $this->_updateButton('delete', 'label', Mage::helper('codebaseclient')->__('Delete Ticket'));
        //$this->_removeButton('delete');

        /*$this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        */

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('projects_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'tickets_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'tickets_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {

            return Mage::helper('codebaseclient')->__('Create Ticket');

    }
}
