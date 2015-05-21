<?php
class Wage_Codebaseclient_Model_Tickets extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('codebaseclient/tickets');
    }
}
