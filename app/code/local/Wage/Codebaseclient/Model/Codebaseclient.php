<?php
class Wage_Codebaseclient_Model_Codebaseclient extends Wage_Codebaseclient_Model_Abstract{
	
	public function getTickets()
    {
        $secure = 's'; // or leave null to use HTTP
        $projects = $this->getProjects();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('codebaseclient/refreshtime');
        $time = $readConnection->fetchCol('SELECT update_time FROM ' . $table . ' WHERE code = "ticket_refresh" ');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->beginTransaction();
        $new_since = Mage::getModel('core/date')->date('Y-m-d H:i:s');

        if(count($projects) > 0)
        {
            foreach ($projects as $permalink)
            {

                $openTickets = array();


                    $project_permalink = $permalink;
                    $query = 'sort:priority resolution:open';
                    $updateDb = false;
                    $limit = 1000;
                    for($i=1;$i<=100;$i++)
                    {
                        //echo 'for -'.$i.'<br/>';
                        unset($tickets);
                        $tickets = $this->tickets($project_permalink,$query,$i); //product_shortcode,query

                        if(!is_array($tickets[0]) && count($tickets) > 0 )
                        {
                            $ticket = array();
                            $ticket = $tickets;
                            unset($tickets);
                            $tickets[0] = $ticket;
                        }
                        if(count($tickets))
                        {
                            $updateDb = true;
                            //echo $project_permalink.' '.$i.' '.count($tickets).'<br/>';
                            foreach ($tickets as $ticket)
                            {
                                unset($data);
                                unset($avail_ticket);

                                $avail_ticket = $this->loadTicketByNumber($ticket['ticket-id'],$ticket['project-id']);
                                if($avail_ticket->getId())
                                {

                                    $model = Mage::getModel('codebaseclient/tickets')->load($avail_ticket->getId());
                                    $data = $model->getData();

                                } else {
                                    $model = Mage::getModel('codebaseclient/tickets');
                                }
                                $openTickets[]              = $ticket['ticket-id'];
                                $data['ticket_id']          = $ticket['ticket-id'];
                                $data['summary']            = $ticket['summary'];
                                $data['ticket_type']        = $ticket['ticket-type'];
                                $data['project_name']       = $permalink;
                                $data['permalink']          = $project_permalink;
                                $data['assignee']           = $ticket['assignee'];
                                $data['reporter']           = $ticket['reporter'];
                                $data['category_name']      = $ticket['category']['name'];
                                $data['priority_name']      = $ticket['priority']['name'];
                                $data['status_name']        = $ticket['status']['name'];
                                $data['type_name']          = $ticket['type']['name'];
                                $data['resolution']         = 'open';
                                if(is_array($ticket['tags'])){
                                    $data['tags']               = implode(',',$ticket['tags']);
                                } else {
                                    $data['tags']               = $ticket['tags'];
                                }
                                $data['created_at']         = $ticket['created-at'];
                                $data['updated_at']         = $ticket['updated-at'];
                                $data['project_id']         = $ticket['project-id'];
                                if(!is_array($ticket['milestone-id'])) {
                                    $data['milestone_id']       = $ticket['milestone-id'];
                                }
                                if(is_array($ticket['milestone'])) {
                                    $data['milestone_name']         = $ticket['milestone']['name'];
                                }
                                if(is_array($ticket['estimated-time'])){
                                    $data['estimated_time']     = 0;
                                } else {
                                    $data['estimated_time']     = $ticket['estimated-time'];
                                }

                                $data['total_time_spent']   = $ticket['total-time-spent'];
                                if($data['estimated_time']  && $data['total_time_spent'])
                                {
                                    $data['time_left']   = $data['estimated_time'] - $data['total_time_spent'];
                                }



                                if($avail_ticket->getId()) {

                                    $model->setId($avail_ticket->getId());
                                    $model->setData($data);

                                } else {
                                    $model->setData($data);
                                }

                                try {
                                    $model->save();
                                } catch(Exception $e) {
                                    if(Mage::getStoreConfigFlag("codebase/general/codebaselog")) {
                                        Mage::log($e->getMessage(),null,"wage_codebase.log");
                                    }
                                }
                            }
                        }else {
                            break;
                        }
                    }

                if($updateDb)
                {
                    $ticketsCollection = Mage::getModel('codebaseclient/tickets')->getCollection()
                    ->addFieldToFilter('resolution','open')
                    ->addFieldToFilter('permalink',$project_permalink);

                    foreach($ticketsCollection as $tkt){
                        if (in_array($tkt->getTicketId(), $openTickets)) {
                            // Do nothing
                        } else {
                            $ticket = $this->loadTicketByNumber($tkt->getTicketId(),$tkt->getProjectId());
                            $ticket->setResolution('close');
                            $ticket->save();
                        }
                    }
                }
            }
        }
        if(count($projects) > 0)
        {
            if($time[0]) {
                // Update Entry
                $__fields = array();
                $__fields['update_time'] = $new_since;
                $__where = $connection->quoteInto('code =?', 'ticket_refresh');
                $connection->update($table, $__fields, $__where);

            } else {
                // Insert Entry
                $fields = array();
                $fields['update_time'] = $new_since;
                $fields['code'] = 'ticket_refresh';
                $connection->insert($table, $fields);
            }
            $connection->commit();
        }
        //return $tickets;
    }

    function loadTicketByNumber($number,$projectId)
    {

        $obj = Mage::getModel('codebaseclient/tickets')->getCollection()
            ->addFieldToFilter('ticket_id',$number)
            ->addFieldToFilter('project_id',$projectId)
            ->getFirstItem();


        return $obj;
    }

    public function getProjects()
    {
        $projects = explode(',',Mage::getStoreConfig('codebaseclient/general/projects'));
        return $projects;
    }


}
