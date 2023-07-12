<?php
        namespace App\Controller;
        use Cake\Network\Session\DatabaseSession;
        use App\Network\Email\Email;
        use App\Network\Request\Request;
        use Cake\Event\Event;
        use App\Network\Response\Response;
        use Cake\ORM\TableRegistry;
        use App\Network\Http\HttpSocket;
        use Cake\Utility\Xml;
        use FR3D;
        use Applicationformspdfs;//importing another controller class here
        use TCPDF;	
        use Cake\Datasource\ConnectionManager;  
           
        class ChemistController extends AppController {

        var $name = 'Chemist';
        public function beforeFilter($event) {
        parent::beforeFilter($event);    
        $this->viewBuilder()->setHelpers(['Form','Html']);
		    $username = $this->getRequest()->getSession()->read('username');
        $username = $this->getRequest()->getSession()->read('username');
        
        
        
        if ($username == null) {
          $this->customAlertPage('Sorry You are not authorized to view this page..');
          exit;
        } else {

          $this->loadModel('DmiUsers');
          //check if user entry in Dmi_users table for valid user
          $check_user = $this->DmiUsers->find('all',array('conditions'=>array('email'=>$this->Session->read('username'))))->first();
          
          if (empty($check_user)) {
            $this->customAlertPage('Sorry You are not authorized to view this page..');
            exit;
          }
        }

        $this->loadComponent('Customfunctions');
        $this->loadModel('DmiChemistRoToRalLogs');
        $ro_office_id = $_SESSION['posted_ro_office'];
        }

        //for list of chemist application forwarded by RO  added by laxmi B. on 28-12-2022
        public function listOfChemistApplRoToRal(){ 
        $this->viewBuilder()->setLayout('admin_dashboard');
        $this->loadModel('DmiChemistRoToRalLogs');
        $this->loadModel('DmiChemistRalToRoLogs');
        $this->loadModel('DmiApplicationTypes');
        $ro_office_id = $_SESSION['posted_ro_office'];
        $ral_office_id = $_SESSION['posted_ro_office'];	


        $this->loadModel('DmiRoOffices');
        $this->loadModel('DmiRejectedApplLogs');
      
        $query = "SELECT * FROM dmi_chemist_ro_to_ral_logs  WHERE chemist_id NOT IN (SELECT customer_id FROM dmi_rejected_appl_logs) AND is_forwordedtoral != '' AND ral_office_id ='".$ral_office_id."' ORDER BY id DESC";
        $conn = ConnectionManager::get('default');
        $listofAppl = $conn->execute($query);
        $listofApp =  $listofAppl->fetchAll('assoc');
        
        $i=0;
        $ro_offices = array();
        $is_training_completed = array();
        $reshedule_status = array();
        $reschedule_pdf  = array();
        $appl_type = array();
        if(!empty($listofApp)){
        foreach($listofApp as $list){ 
        $ro_offices[$i] = $this->DmiRoOffices->find('list',array('valueField'=>'ro_office', 'conditions'=>array('id IS'=>$list['ro_office_id'])))->first();
        
        $is_training_complete = $this->DmiChemistRalToRoLogs->find('all')->where(array('chemist_id IS'=>$list['chemist_id'], 'training_completed IS'=>1))->last();

        if(!empty( $is_training_complete)){
        $is_training_completed[$i] = $is_training_complete['training_completed'];
          }
       
       // to get reshedule status
        $is_confirm = $this->DmiChemistRalToRoLogs->find('all')->where(array('chemist_id IS'=>$list['chemist_id'], 'training_completed IS'=>NULL))->last();
     
         if(!empty($is_confirm)){
         $reshedule_status[$i] = $is_confirm['reshedule_status'];
         $reschedule_pdf [$i]  = $is_confirm['reshedule_pdf'];
         }

         // to get application type by application type id
         $appli_type = $this->DmiApplicationTypes->find('all',['conitions'=>array('id IS'=> $list['appliaction_type'])])->first();
         $appl_type[$i] = $appli_type['application_type'];
        $i= $i+1;	
        }
        
       
        $this->set('appl_type', $appl_type);
        $this->set('reschedule_pdf', $reschedule_pdf);
        $this->set('is_training_completed', $is_training_completed );
        $this->set('listOfChemistApp',$listofApp);
        $this->set('ro_office', $ro_offices);      
        $ral_result = $this->DmiRoOffices->find('all',array('fields'=>'ro_office', 'conditions'=>array('id'=>$ral_office_id)))->first();


        $this->set('ral_offices',$ral_result['ro_office']);
        //to set resudule status
        $this->set('reshedule_status', $reshedule_status);
        }


        }
                        

            //training completed at RAL side mark as completed after submit form 
            //added by Laxmi on 28-12-2022
            public function forwardApplicationtoRo($id){
            $this->viewBuilder()->setLayout('admin_dashboard');
            $this->loadModel('DmiChemistRoToRalLogs');
            $this->loadModel('DmiRoOffices');
            $message = "";
            $message_theme = "";
            $redirect_to = "";
            $this->viewBuilder()->setLayout('admin_dashboard');
            $ralId = $this->Session->read('posted_ro_office');
            //for information
            $this->set('ral_id', $ralId);
            $chemistRoForwardData = $this->DmiChemistRoToRalLogs->find('all')->where(array('id'=>$id))->toArray();
            
            $ral_office = $_SESSION['ro_office'];
            $this->set('ral_office', $ral_office);
            $ro_office_name = $this->DmiRoOffices->find('list', array('valueField'=>'ro_office', 'conditions'=>array('id IS'=>$chemistRoForwardData[0]['ro_office_id'])))->first(); 
            $this->set('ro_office', $ro_office_name);
            $chemist_fname = $chemistRoForwardData[0]['chemist_first_name'];
            $chemist_lname = $chemistRoForwardData[0]['chemist_last_name'];
            $chemist_id    = $chemistRoForwardData[0]['chemist_id'];
            $scheduleFrom  = $chemistRoForwardData[0]['shedule_from'];

            $fromDate = str_replace("/","-",$scheduleFrom);
            $date = date('d/m/Y',strtotime($fromDate));
           
            $scheduleTo    =   $chemistRoForwardData[0]['shedule_to'];

            $toDate = str_replace("/","-",$scheduleTo);
            $Todate = date('d/m/Y',strtotime($toDate));

            $this->set('scheduleFrom', $date);
            $this->set('scheduleTo', $Todate);
            //set customer id to chemist id in session
            $this->Session->write('customer_id',$chemist_id);

            $this->set('chemist_fname', $chemist_fname);
            $this->set('chemist_lname', $chemist_lname);
            $this->set('chemist_id', $chemist_id);
            $ral_fname = $this->Session->read('f_name');
            $ral_lname = $this->Session->read('l_name');
            $this->set('ral_fname', $ral_fname);
            $this->set('ral_lname', $ral_lname);
            
            $ro_office_id = $chemistRoForwardData[0]['ro_office_id'];
            // to open reshedule application
             $this->loadModel('DmiChemistRalToRoLogs');
            $fetchData = $this->DmiChemistRalToRoLogs->find('all', array('conditions'=>['chemist_id IS'=>$chemist_id]))->last();

             if(!empty($fetchData)){
               $this->set('training_completed',$fetchData['fetchData']);
               $this->set('reshedule_status',$fetchData['reshedule_status']);
             }

            if($this->request->is('post') != '' ){
             
             if(!empty($fetchData['reshedule_status']) && $fetchData['reshedule_status'] == 'confirm'){  
            $document= $this->request->getData('document');
            if (!empty($this->request->getData('document')->getClientFilename()))
            {
            $attchment = $document; 
            $file_name = $attchment->getClientFilename();
            $file_size = $attchment->getSize();
            $file_type = $attchment->getClientMediaType();
            $file_local_path = $attchment->getStream()->getMetadata('uri');
            // calling file uploading function
            $document = $this->Customfunctions->fileUploadLib($file_name,$file_size,$file_type,$file_local_path);
             }else{
              $document = "";  
             }
             $postdata = $this->request->getData();

             if(!empty($postdata['training_completed'])){
             $this->loadModel('DmiChemistRalToRoLogs');
             $chemistId = $postdata['chemist_id'];
             $ral_office_id = $this->Session->read('posted_ro_office');
             $data = $this->DmiChemistRalToRoLogs->newEntity(array(
             'chemist_id' =>$postdata['chemist_id'],
             'ro_first_name' =>$postdata['ro_first_name'],
             'ro_last_name' => $postdata['ro_last_name'],
             'chemist_first_name' => $postdata['chemist_first_name'],
             'chemist_last_name' => $postdata['chemist_last_name'],
             'ro_office_id' => $ro_office_id, 
             'remark' => $postdata['remark'], 
             'document' => $document,
             'training_completed' => $postdata['training_completed'],
             'created' => date('Y-m-d H:i:s'),
             'appliaction_type'=> 4,
             'ral_office_id' =>$ral_office_id,
            'reshedule_to_date' => date('Y-m-d H:i:s', strtotime(str_replace('/','-',$fetchData['reshedule_to_date']))),
             'reshedule_from_date' => date('Y-m-d H:i:s', strtotime(str_replace('/','-',$fetchData['reshedule_from_date']))),
             'reshedule_status' => 'confirm',
             ));
              
             $result = $this->DmiChemistRalToRoLogs->save($data);
            
             if($result){   
             $lastInsertedId = $result['id'];

             //to enter RAL Email id in allocation and current position table added by laxmi on 10-01-2023
                 $this->loadModel('DmiRoOffices');
                 
                 $find_office_email_id = $this->DmiRoOffices->find('all',array('conditions'=>array('id'=>$chemistRoForwardData[0]['ro_office_id'])))->first(); 
                 $office_incharge_id = $find_office_email_id['ro_email_id'];
                 $office_name = $find_office_email_id['ro_office']; 
                 //Entry in allocation table for level_3 Ro
                 $this->loadModel('DmiChemistAllocations');
                 $allocationEntity = $this->DmiChemistAllocations->newEntity(array(
                       'customer_id'=>$chemist_id,
                       'level_3'=>$office_incharge_id,
                       'current_level'=>$office_incharge_id,
                       'created'=>date('Y-m-d H:i:s'),
                       'modified'=>date('Y-m-d H:i:s')
                     ));

               if($this->DmiChemistAllocations->save($allocationEntity)){
               
                   $this->loadModel('DmiChemistAllCurrentPositions');
                  //Entry in all applications current position table
                  $customer_id =  $chemist_id;
                  $user_email_id = $office_incharge_id;
                  $current_level = 'level_3';
                 
                  $this->DmiChemistAllCurrentPositions->currentUserUpdate($customer_id,$user_email_id,$current_level);
                }

             $message ="Chemist Application Forwarded to RO Office ".$office_name. " And the email id is " .base64_decode($office_incharge_id)."";
             $message_theme = "success";
             $redirect_to = '../chemistApplRalToRo/'.$lastInsertedId;
             }else{
             $message ="Something went wrong, Please Try Again!";
             $message_theme = "warning";
             $redirect_to = '../listOfChemistApplRoToRal';
             }
             }else{
             $message ="Please Enter all Field data";
             $message_theme = "warning";
             $redirect_to = '';
             }  
             }else{

            $resheduleData = $this->request->getData(); 
            $chemist_id = $resheduleData['chemist_id'];
            if(!empty($resheduleData['reshedule_from_date']) && !empty($resheduleData['reshedule_to_date'])){

            $from = date('d-m-Y',strtotime(str_replace('/','-',$resheduleData['reshedule_from_date'])));
			      $to   = date('d-m-Y',strtotime(str_replace('/','-',$resheduleData['reshedule_to_date'])));
	
			      if($from < $to){
                $this->loadModel('DmiChemistRalToRoLogs');
                $ral_office_id = $this->Session->read('posted_ro_office');
                $data = $this->DmiChemistRalToRoLogs->newEntity(array(
                'chemist_id' => $chemist_id,
                'chemist_first_name' => $resheduleData['chemist_first_name'],
                'chemist_last_name'  => $resheduleData['chemist_last_name'],
                'ro_first_name'      =>$resheduleData['ro_first_name'],
                'ro_last_name'      =>$resheduleData['ro_last_name'],
                'reshedule_from_date' => date('Y-m-d H:i:s', strtotime(str_replace('/','-', $resheduleData['reshedule_from_date']))),
                'reshedule_to_date' => date('Y-m-d H:i:s', strtotime(str_replace('/','-',$resheduleData['reshedule_to_date']))),
                'reshedule_remark'=> $resheduleData['remark'],
                'reshedule_status'=>'confirm',
                'ro_office_id'    =>$ro_office_id,
                'ral_office_id'   =>$this->Session->read('posted_ro_office'),
                'application_type'=> 4,

                ));
                
               $result = $this->DmiChemistRalToRoLogs->save($data);
                if($result){
                $lastInsertedId = $result['id'];
                $message ="Chemist Training Schedule Successfully.";
                $message_theme = "success";
                $redirect_to = '../chemistApplTrainingScheduleAtRal/'.$lastInsertedId;
                
                }else{
                $message ="Something went wrong, Please Try Again!";
                $message_theme = "warning";
                $redirect_to = '../listOfChemistApplRoToRal';
                }
              }else{
                $message ="Always select From date gretter than To Dates!";
                $message_theme = "warning";
                $redirect_to = '../forwardApplicationtoRo/'.$id.'';
              } 
            }else{
            $message ="Please Enter all Field data";
            $message_theme = "warning";
            $redirect_to = '';
            }
            }
            }

             // set variables to show popup messages from view file
             $this->set('message',$message);
             $this->set('message_theme',$message_theme);
             $this->set('redirect_to',$redirect_to);
             }

                 //list of application forwarded back Ral to RO added by laxmi on 29/12/2022
                 public function  listOfChemistApplRalToRo(){
                 	$this->viewBuilder()->setLayout('admin_dashboard');
               	    $this->loadModel('DmiChemistRalToRoLogs');
               	    $this->loadModel('DmiRoOffices');

	      	        $ral_office_id = $_SESSION['posted_ro_office'];	

                    $listofApp = $this->DmiChemistRalToRoLogs->find('all')->where(array('training_completed IS  '=>1, 'ral_office_id IS'=>$ral_office_id))->order('created desc')->toArray();
                         $i=0;
                         $ro_offices = array();
                         
                        if(!empty($listofApp)){
                         foreach($listofApp as $list){
                         $ro_offices[$i] = $this->DmiRoOffices->find('list',array('valueField'=>'ro_office', 'conditions'=>array('id IS'=>$list['ro_office_id'])))->first();
                         $i= $i+1;	
                         }
                        
        	             
        		             $this->set('listOfChemistApp',$listofApp);
        		             $this->set('ro_office', $ro_offices);      
                         $ral_result = $this->DmiRoOffices->find('all',array('fields'=>'ro_office', 'conditions'=>array('id'=>$ral_office_id)))->first();
                            
                         
                         $this->set('ral_offices',$ral_result['ro_office']);
                                
	      	       }

               }

               //forwarded training completed pdf RAL to RO added by laxmi B. on 30-12-2022
               public function chemistApplRalToRo($id = null){
               	$this->viewBuilder()->setLayout('pdf_layout');
                $ral_office_id = $this->Session->read('posted_ro_office');
                $ral_office    = $this->Session->read('ro_office');
                $ral_role    = $this->Session->read('role');
                $this->set('ral_office',$ral_office);
                $this->set('ral_role',$ral_role);

                $this->loadModel('DmiRoOffices');
                $this->loadModel('DmiChemistRalToRoLogs');
                $this->loadModel('DmiFirms');
                $this->loadModel('DmiChemistRegistrations');
                $this->loadModel('DmiChemistRoToRalLogs');
                $this->loadModel('MCommodityCategory');
                $this->loadModel('MCommodity');
                
                $ralforwardedRo = $this->DmiChemistRalToRoLogs->find('all')->where(array('ral_office_id IS'=>$ral_office_id, 'id IS'=>$id))->first();
                if(!empty($ralforwardedRo)){
                $this->set('chemist_id', $ralforwardedRo['chemist_id']);
                $this->set('chemist_fname', $ralforwardedRo['chemist_first_name']);
                $this->set('chemist_lname', $ralforwardedRo['chemist_last_name']);
                $this->set('ral_fname', $ralforwardedRo['ro_first_name']);
                $this->set('ral_lname', $ralforwardedRo['ro_last_name']);
                $dateReliving = date('d-m-Y',strtotime(str_replace('/', '.',$ralforwardedRo['created'])));
                $this->set('reliving_date', $dateReliving);
                }

                //ro-office
                $ro_officeData = $this->DmiRoOffices->find('all', array('fields'=>'ro_office'))->where(array('id IS'=>$ralforwardedRo['ro_office_id']))->first();
                if(!empty($ro_officeData)){
                   $this->set('ro_office',$ro_officeData['ro_office']);
                }

                //get packer id
                $packerId = $this->DmiChemistRegistrations->find('all', array('fields'=>'created_by'))->where(array('chemist_id IS'=>$ralforwardedRo['chemist_id']))->first();

                if(!empty($packerId['created_by'])){
                  $firmData = $this->DmiFirms->find('all')->where(array('customer_id IS'=>$packerId['created_by']))->first();
                  if(!empty($firmData)){
                  	$this->set('firm_name', $firmData['firm_name']);
                  	$this->set('firm_address', $firmData['street_address']);
                     

                    // for multiple commodities select at export added by laxmi On 10-1-23
                   $sub_commodity_array = explode(',',$firmData['sub_commodity']);
                   $i=0;
                   foreach ($sub_commodity_array as $key => $sub_commodity) {
                        
                    $fetch_commodity_id = $this->MCommodity->find('all',array('conditions'=>array('commodity_code IS'=>$sub_commodity)))->first(); 
                    $commodity_id[$i] = $fetch_commodity_id['category_code'];
                    $sub_commodity_data[$i] =  $fetch_commodity_id;     
                    $i=$i+1;
                   }
                    $unique_commodity_id = array_unique($commodity_id); 
                    $commodity_name_list = $this->MCommodityCategory->find('all',array('conditions'=>array('category_code IN'=>$unique_commodity_id, 'display'=>'Y')))->toArray();

                    $this->set('commodity_name_list',$commodity_name_list);        
                     $this->set('sub_commodity_data',$sub_commodity_data);
                  	
                  }
                  
                  $scheduleDates = $this->DmiChemistRalToRoLogs->find('all')->where(array('chemist_id IS'=>$ralforwardedRo['chemist_id']))->last(); 
                 
             
                  $from = date('d-m-Y',strtotime(str_replace('/', '-',$scheduleDates['reshedule_from_date'])));
                  $to = date('d-m-Y',strtotime(str_replace('/', '-',$scheduleDates['reshedule_to_date'])));

                 $this->set('schedule_from',$from);
                 $this->set('schedule_to',$to);
                 $ral_office = $this->DmiRoOffices->find('all', ['conditions'=>array('id IS'=>$scheduleDates['ral_office_id'])])->first();
                  if(!empty($ral_office)){
                   $this->set('ral_office', $ral_office['ro_office']);
                  }
                }
                  
                  $customer_id = $ralforwardedRo['chemist_id'];  
                  $all_data_pdf = $this->render('/Chemist/chemist_appl_ral_to_ro');
		
		              $split_customer_id = explode('/',(string) $customer_id); #For Deprecations
		
		              $pdfPrefix = 'forward_letter_to_ro';
		              $rearranged_id = $pdfPrefix.'('.$split_customer_id[0].'-'.$split_customer_id[1].'-'.$split_customer_id[2].')';
		              $application_type = 4;
		              
		              //check applicant last record version to increment		
		              $list_id = $this->DmiChemistRalToRoLogs->find('list', array('valueField'=>'id', 'conditions'=>array('chemist_id IS'=>$customer_id)))->toArray(); 
		
		               if(!empty($list_id))
		               {
			              $max_id = $this->DmiChemistRalToRoLogs->find('all', array('fields'=>'pdf_version', 'conditions'=>array('id'=>max($list_id))))->first();
			              $last_pdf_version 	=	$max_id['pdf_version'];
            
		                 }
		                 else{	$last_pdf_version = 0;	}				
                     
		                $current_pdf_version = $last_pdf_version+1; //increment last version by 1//taking complete file name in session, which will be use in esign controller to esign the file.
		                $this->Session->write('pdf_file_name',$rearranged_id.'('.$current_pdf_version.')'.'.pdf');
	    
                      //creating filename and file path to save				
		                 $file_path = '/writereaddata/LIMS/chemist_training/ral_to_ro_letter/'.$rearranged_id.'('.$current_pdf_version.')'.'.pdf';
				               
		                 $filename = $_SERVER["DOCUMENT_ROOT"].$file_path;
                     //creating filename and file path to save				
				
		                 $file_name = $rearranged_id.'('.$current_pdf_version.')'.'.pdf';
		
		                $this->DmiChemistRalToRoLogs->updateAll(
			               array('pdf_file' => $file_path, 'pdf_version'=>$current_pdf_version),
			              array('chemist_id'=>$customer_id));
        
		                $file_path = $_SERVER["DOCUMENT_ROOT"].$file_path;
                          
		                //to preview application
                    //$this->callTcpdf($all_data_pdf,'I',$customer_id,'chemist',$file_path);//on with preview mode
		                $this->callTcpdf($all_data_pdf,'F',$customer_id,'chemist',$file_path);//with save mode
		               
		
		                $this->redirect('/Chemist/listOfChemistApplRalToRo');
               
                 
              }



                //training schedule letter at ral generate after reshedule training at ral 
                //  added by laxmi B. 09-05-2023
                // Chemist Training module
              
                public function chemistApplTrainingScheduleAtRal($id){ 
                $this->viewBuilder()->setLayout('pdf_layout');    
                $this->loadModel('DmiFirms');
                //$this->loadModel('DmiCustomers');
                $this->loadModel('DmiDistricts');
                $this->loadModel('DmiStates');
                $this->loadModel('MCommodity');
                $this->loadModel('MCommodityCategory');
                $this->loadModel('DmiRoOffices');
                $this->loadModel('DmiChemistPaymentDetails');
                $this->loadModel('DmiUserRoles');
                $this->loadModel('DmiChemistRegistrations');
                $this->loadModel('DmiChemistRoToRalLogs');
                $this->loadModel('DmiChemistRalToRoLogs');
                $this->loadModel('DmiChemistProfileDetails');
               

                $customer_id = $this->Session->read('customer_id'); 
                $application_type = $this->Session->read('application_type');
                // $ro_fname = $this->Session->read('f_name');
                // $ro_lname = $this->Session->read('l_name');
                // $role = $this->Session->read('role');
                $this->set('customer_id', $customer_id);
                // $this->set('ro_fname', $ro_fname);
                // $this->set('ro_lname', $ro_lname);
                // $this->set('role', $role);
                

                $pdf_date = date('d-m-Y');  
                $this->set('pdf_date',$pdf_date);
                
                //set profile picture in letter by laxmi on 11-07-2023
                $chemist_profile_picture = $this->DmiChemistProfileDetails->find('all', array('conditions'=>['customer_id IS'=>$customer_id]))->first();
                if(!empty($chemist_profile_picture)){
                  $profile_photo = $chemist_profile_picture['profile_photo'];
                  $this->set('profile_photo', $profile_photo);
                }


                $chemistdetails = $this->DmiChemistRegistrations->find('all')->where(array('chemist_id IS'=>$customer_id))->first();
               if(!empty($chemistdetails['is_training_completed']) && $chemistdetails['is_training_completed'] == 'no' ){
                
                $charge = $this->DmiChemistPaymentDetails->find('list', array('valueField'=>'amount_paid'))->where(array('customer_id'=>$customer_id))->first();
                if(!empty($charge)){
                $this->set('charges',$charge);

                }
                }

                if(!empty($chemistdetails)){


                $this->set('chemist_fname', $chemistdetails['chemist_fname']);
                $this->set('chemist_lname', $chemistdetails['chemist_lname']);


                $firmDetails = $this->DmiFirms->find('all')->where(array('customer_id IS'=>$chemistdetails['created_by']))->first();
                
                if(!empty($firmDetails)){
                $this->set('firmName',$firmDetails['firm_name']);
                $this->set('firm_address',$firmDetails['street_address']);
                $this->set('pin_code', $firmDetails['postal_code']);

                $district = $this->DmiDistricts->find('all')->where(array('id IS'=>$firmDetails['district']))->first();
                if(!empty($district)){

                $this->set('district', $district['district_name']);
                }
                $state = $this->DmiStates->find('all')->where(array('id IS'=>$firmDetails['state']))->first();
                if(!empty($state)){
                $this->set('state', $state['state_name']);
                }
                // for multiple commodities select at export added by laxmi On 10-1-23
                $sub_commodity_array = explode(',',$firmDetails['sub_commodity']);
                $i=0;
                foreach ($sub_commodity_array as $key => $sub_commodity) {

                $fetch_commodity_id = $this->MCommodity->find('all',array('conditions'=>array('commodity_code IS'=>$sub_commodity)))->first(); 
                $commodity_id[$i] = $fetch_commodity_id['category_code'];
                $sub_commodity_data[$i] =  $fetch_commodity_id;     
                $i=$i+1;
                }
                $unique_commodity_id = array_unique($commodity_id); 
                $commodity_name_list = $this->MCommodityCategory->find('all',array('conditions'=>array('category_code IN'=>$unique_commodity_id, 'display'=>'Y')))->toArray();
                 
                $this->set('commodity_name_list',$commodity_name_list);     
                $this->set('sub_commodity_data',$sub_commodity_data);


                }

                $ral_officeData = $this->DmiChemistRoToRalLogs->find('all')->where(array('chemist_id IS'=>$customer_id))->first();
                
               

                if(!empty($ral_officeData)){
                  
                $ral_id = $ral_officeData['ral_office_id'];
                $ral_office = $this->DmiRoOffices->find('all')->where(array('id IS'=>$ral_id))->first();
                $this->set('ral_office', $ral_office['ro_office']);
                $this->set('ral_office_address', $ral_office['ro_office_address']);
                $this->set('ro_fname',$ral_officeData['ro_first_name']);
                $this->set('ro_lname', $ral_officeData['ro_last_name']);
                

                //ro office
                $ro_office = $this->DmiRoOffices->find('all', ['conditions'=>['id IS'=>$ral_officeData['ro_office_id']]])->first();
              
                if(!empty($ro_office)){
                    $this->set('ro_office',$ro_office['ro_office']);
                    $this->set('role','RO/SO');
                    //added office type by laxmi on 12-07-2023
                    $this->set('office_type', $ro_office['office_type']);
                }
               
                // get reschedule from and to date
                 $rescheduleDate = $this->DmiChemistRalToRoLogs->find('all')->where(['chemist_id IS'=>$customer_id])->last();

                $dateF = date('d-m-Y',strtotime(str_replace('/', '-',$rescheduleDate['reshedule_from_date'])));
                $dateTo = date('d-m-Y',strtotime(str_replace('/', '-',$rescheduleDate['reshedule_to_date'])));

                $this->set('schedule_from',$dateF);
                $this->set('schedule_to',$dateTo);
                }

                $all_data_pdf = $this->render('/Chemist/chemist_appl_training_schedule_at_ral');

                $split_customer_id = explode('/',(string) $customer_id); #For Deprecations

                $pdfPrefix = 'forward_letter_to_ral';
                $rearranged_id = $pdfPrefix.'('.$split_customer_id[0].'-'.$split_customer_id[1].'-'.$split_customer_id[2].')';

                $application_type = $this->Session->read('application_type');

                //check applicant last record version to increment      
                $list_id = $this->DmiChemistRalToRoLogs->find('list', array('valueField'=>'id', 'conditions'=>array('chemist_id IS'=>$customer_id)))->toArray();

                if(!empty($list_id))
                {
                $max_id = $this->DmiChemistRalToRoLogs->find('all', array('fields'=>'reshedule_version', 'conditions'=>array('id'=>max($list_id))))->first();                                                                 
                $last_pdf_version   =   $max_id['reshedule_version'];

                }
                else{   $last_pdf_version = 0;  }               

                $current_pdf_version = $last_pdf_version+1; //increment last version by 1//taking complete file name in session, which will be use in esign controller to esign the file.
                $this->Session->write('pdf_file_name',$rearranged_id.'('.$current_pdf_version.')'.'.pdf');
               
                //creating filename and file path to save               
                $file_path = '/testdocs/DMI/chemist_training/ro_to_ral_letter/'.$rearranged_id.'('.$current_pdf_version.')'.'.pdf';

                $filename = $_SERVER["DOCUMENT_ROOT"].$file_path;
                //creating filename and file path to save               

                $file_name = $rearranged_id.'('.$current_pdf_version.')'.'.pdf';

                $this->DmiChemistRalToRoLogs->updateAll(
                array('reshedule_pdf' => $file_path, 'reshedule_version'=>$current_pdf_version),
                array('chemist_id'=>$customer_id));

                $file_path = $_SERVER["DOCUMENT_ROOT"].$file_path;
                //to preview application
                $this->callTcpdf($all_data_pdf,'F',$customer_id,'chemist',$file_path);//with save mode
                //$this->callTcpdf($all_data_pdf,'I',$customer_id,'chemist',$file_path);//on with preview mode

                $this->redirect('/Chemist/listOfChemistApplRoToRal');

                }
                }

                  //for reject chemist application by RAL officer forwarded from Ro to RAL 
                  //added by Laxmi Bhadade
                  //dated 22-05-2023 
                  //for chemist Training Module
                public function chemistApplicationReject(){
                  $this->setLayout= false;
                  $this->autoRender = false;
                  
                  
                  if($this->request->is('post')){
                    $reqData = $this->request->getData();
                   
                    $app_type = $reqData['appl_type'];
                    $chemistId = $reqData['chemist_id'];
                    $reason    = $reqData['remark'];
                    $byuser    = $this->Session->read('username');

                    $this->loadModel('DmiRejectedApplLogs');
                    $this->loadModel('DmiApplicationTypes');

                    $appl_type = $this->DmiApplicationTypes->find('all',array('fields'=>['id'], 'conditions'=>['application_type'=>$app_type]))->first();
                    
                    if($appl_type['id'] == 4) {

                      $form_type='CHM';
                    }
                                
                    if(!empty($reason)){
                                $DmiRejectedApplLogsEntity = $this->DmiRejectedApplLogs->newEntity(
                      array(
                        'appl_type'   => $appl_type['id'],
                        'form_type'   => $form_type,
                        'customer_id' => $chemistId,
                        'by_user'     => $byuser,
                        'remark'      => $reason,
                        'created'     => date('Y-m-d H:i:s'),
                      ));
                    }else{
                      $message = "Please enter all fields data.";
                    }

                                
                    if($this->DmiRejectedApplLogs->save($DmiRejectedApplLogsEntity) ) {
                                      $message ="Application Rejected successfully.";
                    }else{
                      $message ="Something went wrong, Please try Again.";
                    }
                                  
                  }

                }

	}
?>