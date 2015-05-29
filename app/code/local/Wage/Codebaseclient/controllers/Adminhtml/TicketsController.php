<?php
/**
 *
 * @author Wagento
 */
class Wage_Codebaseclient_Adminhtml_TicketsController extends Mage_Adminhtml_Controller_Action {
    protected function _initAction()

    {
        $this->loadLayout()
            ->_setActiveMenu('codebaseclient/open_tickets');
        return $this;
    }
    public function indexAction() {
        $this->loadLayout();
        $this->_initAction();
        $this->renderLayout();
    }
    public function exportCsvAction()
    {
        $fileName   = 'tickets.csv';
        $content    = $this->getLayout()->createBlock('codebaseclient/adminhtml_tickets_grid')
            ->getCsv();
 
        $this->_sendUploadResponse($fileName, $content);
    }
 
    public function exportXmlAction()
    {
        $fileName   = 'tickets.xml';
        $content    = $this->getLayout()->createBlock('codebaseclient/adminhtml_tickets_grid')
            ->getXml();
 
        $this->_sendUploadResponse($fileName, $content);
    }

    public function refreshAction()
    {
        $tickets = Mage::getModel('codebaseclient/codebaseclient')->getTickets();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('codebaseclient')->__('Ticket has been refreshed successfully'));
        $this->_redirect('*/*/');
    }

    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('codebaseclient/tickets')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('codebaseclient_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('codebaseclient/tickets');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Tickets Manager'), Mage::helper('adminhtml')->__('Tickets Manager'));


            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('codebaseclient/adminhtml_tickets_edit'))
                ->_addLeft($this->getLayout()->createBlock('codebaseclient/adminhtml_tickets_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('codebaseclient')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            $project = $data['project'];
            $params['summary'] = $data['summary'];
            $params['description'] = $data['description'];
            $model = Mage::getModel('codebaseclient/codebaseclient')->addTicket($project,$params);
            try {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('codebaseclient')->__('Ticket was created'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }

        }
        $this->_redirect('*/adminhtml_tickets/');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('codebaseclient/adminhtml_tickets_grid')->toHtml()
        );
    }


    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

}
