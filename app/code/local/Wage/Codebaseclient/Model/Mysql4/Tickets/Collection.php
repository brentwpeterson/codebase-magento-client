<?php
class Wage_Codebaseclient_Model_Mysql4_Tickets_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('codebaseclient/tickets');
    }
}
