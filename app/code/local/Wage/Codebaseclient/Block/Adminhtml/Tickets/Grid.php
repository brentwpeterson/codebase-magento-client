<?php
class Wage_Codebaseclient_Block_Adminhtml_Tickets_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('ticketsGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('codebaseclient/tickets')->getCollection()
            ->addFieldToFilter('resolution','open')
            ->setOrder('priority_name','ASC');


        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('project_name', array(
            'header' => Mage::helper('codebaseclient')->__('Project'),
            'index' => 'project_name',

        ));
        $this->addColumn('assignee', array(
            'header'    =>Mage::helper('codebaseclient')->__('Assignee'),
            'index'     =>'assignee',
        ));

        $this->addColumn('summary', array(
            'header' => Mage::helper('codebaseclient')->__('Summary'),
            'index' => 'summary',

        ));
        $this->addColumn('ticket_type', array(
            'header' => Mage::helper('codebaseclient')->__('Type'),
            'index' => 'ticket_type',

        ));
        $this->addColumn('priority_name', array(
            'header' => Mage::helper('codebaseclient')->__('Priority'),
            'index' => 'priority_name',

        ));
        $this->addColumn('status_name', array(
            'header' => Mage::helper('codebaseclient')->__('Status'),
            'index' => 'status_name',

        ));        
        $this->addColumn('Codebase', array(
            'header' => Mage::helper('codebaseclient')->__('Codebase Link'),
            'align' => 'left',
            'index' => 'ticket_id',
            'width'     => '70',
            'renderer' => 'Wage_Codebaseclient_Block_Adminhtml_Tickets_Grid_Renderer_Link',
            'permalink' => 'permalink'
        ));


        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('codebaseclient')->__('Updated At'),
            'index'     => 'updated_at',
            'type'      => 'datetime',
            'width'     => '70',
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('codebaseclient')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('codebaseclient')->__('XML'));
        return parent::_prepareColumns();
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function _filterConditionCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value == NULL) {
            return $this;
        }
        else {
            $this->getCollection()
                ->addFieldToFilter('estimated_time',$value);
        }

        return $this;
    }


}
