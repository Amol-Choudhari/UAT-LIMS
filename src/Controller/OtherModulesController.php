<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Network\Session\DatabaseSession;
use App\Network\Email\Email;
use App\Network\Request\Request;
use App\Network\Response\Response;
use Cake\Datasource\ConnectionManager;

class OtherModulesController extends AppController{

       var $name = 'OtherModules';

       public function initialize(): void {

        parent::initialize();
        $this->viewBuilder()->setLayout('admin_dashboard');
        $this->viewBuilder()->setHelpers(['Form','Html']);
        $this->loadComponent('Customfunctions');
        $this->loadComponent('Paymentdetails');
        $this->loadModel('DmiPaoDetails');
        $this->loadModel('DmiRoOffices');
        $this->loadModel('DmiUsers');
        $this->loadModel('DmiDistricts');
      }

      // sampleDetails
      // Author : Shankhpal Shende
      // Description : This function is created to show the list of sample details
      // Date : 13-02-2023
       public function sampleDetails(){
             
             //load model
              $this->loadModel('LimsJunkSamplesLogs');
              $added_sample_details = $this->LimsJunkSamplesLogs->sampleCodeDetails(); // fetched data 
              $this->Set('added_sample_details',$added_sample_details);
       }

       // add junk SampleCode
       // Author : Shankhpal Shende
       // Description : This function is created to add sample details
       // Date : 13-02-2023
       public function addSampleCode(){
        
         $this->autoRender = false;
         if(!empty($this->request->is('post'))){
          
              $this->autoRender = false;
              $this->loadModel('LimsJunkSamplesLogs');
              $this->loadModel('Workflow');

              $sample_code = htmlentities($_POST['sample_code'], ENT_QUOTES);
              $sample_type = htmlentities($_POST['sample_type'], ENT_QUOTES);
              $commodity = htmlentities($_POST['commodity'], ENT_QUOTES);
              $location = htmlentities($_POST['location'], ENT_QUOTES);
              $last_action = htmlentities($_POST['last_action'], ENT_QUOTES);
              $remark = htmlentities($_POST['remark'], ENT_QUOTES);
             
              // get original sample code from workflow model
              $get_org_sample_code = $this->Workflow->find('all',array('fields'=>array('org_sample_code'),'conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
              
              $org_sample_code = $get_org_sample_code['org_sample_code'];

               //check if the sample code belongs to the user
              $mapUserSample = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('stage_smpl_cd'=>$sample_code)))->first();

              // check the sample code are final grant or not 
              $getlastFlag = $this->Workflow->find('all',array('fields'=>'stage_smpl_flag','conditions'=>array('org_sample_code'=>$org_sample_code),'order'=>'id desc'))->first();
              
              $lastAction = $getlastFlag['stage_smpl_flag'];
              $stage_smpl_flag = str_replace(' ', '', $lastAction);
  
              if ($stage_smpl_flag == 'FG'){
                 
                      echo 'error_fg';
                      exit;
              }
              else{
            
                 $conn = ConnectionManager::get('default');
                 $q = $conn->execute("SELECT *
                    FROM lims_junk_samples_logs 
                    WHERE org_sample_code='$org_sample_code' 
                    AND id IN ( SELECT MAX(id) 
                    FROM lims_junk_samples_logs 
                    GROUP BY sample_code) ");
                 
                 $junk_sample = $q->fetchAll('assoc');
               
                 $i = 0;
                 if(!empty($junk_sample)){
                    foreach ($junk_sample as $result) {
                    $status = $result['status'];
                    $i++;
                  }
                  if($status == 'junked'){
                    echo 'error_exist';
                    exit;
                  }else{
                  $save_details_result = $this->LimsJunkSamplesLogs->saveSampleCodeDetails($org_sample_code,$sample_code,$sample_type,$commodity,$location,$last_action,$remark);// call custome method from 
                 }

                }else{
                  $save_details_result = $this->LimsJunkSamplesLogs->saveSampleCodeDetails($org_sample_code,$sample_code,$sample_type,$commodity,$location,$last_action,$remark);// call custome method from 
                 }

          }
              
        }

        $this->render('/element/sample_details/sample_code_view');
  }

       public function deleteSampleCode(){
        
        $this->autoRender = false;
         if(!empty($this->request->is('post'))){
       
          
              $this->autoRender = false;
              $this->loadModel('LimsJunkSamplesLogs');
              $this->loadModel('Workflow');

              $sample_code = htmlentities($_POST['sample_code'], ENT_QUOTES);
              $sample_type = htmlentities($_POST['sample_type'], ENT_QUOTES);
              $commodity = htmlentities($_POST['commodity'], ENT_QUOTES);
              $location = htmlentities($_POST['location'], ENT_QUOTES);
              $last_action = htmlentities($_POST['last_action'], ENT_QUOTES);
              $remark = htmlentities($_POST['remark'], ENT_QUOTES);
             
              // get original sample code from workflow model
              $get_org_sample_code = $this->Workflow->find('all',array('fields'=>array('org_sample_code'),'conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
           
               //check if the sample code belongs to the user
              $mapUserSample = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('stage_smpl_cd'=>$sample_code)))->first();

              $org_sample_code = $get_org_sample_code['org_sample_code'];
              
              $unjunk_record = $this->LimsJunkSamplesLogs->unjunkedSampleCodeDetails($org_sample_code,$sample_code,$sample_type,$commodity,$location,$last_action,$remark);// call custome method from model

             
        }
      
        $this->render('/element/sample_details/sample_code_view');

       }
     
         // SEARCH SAMPLE CODE
         // @AUTHOR : SHANKHPAL SHENDE
         // DATE : 15/02/2023
       public function searchSample(){

        $username = $this->Session->read('username');
        $user_code = $this->Session->read('user_code');
        $posted_ro_office_id = $_SESSION['posted_ro_office'];
        $sample_code = $_POST['sample_code'];
        $this->loadModel('DmiUserRoles');
        $this->loadModel('Workflow');
        $check_user_role = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$username)))->first();

        $show_details ='no';
        //check if the user is super admin
        if($check_user_role['super_admin']=='yes'){			
          $show_details ='yes';
        
          //check if the sample code belongs to the user
          $mapUserSample = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('stage_smpl_cd'=>$sample_code)))->first();
        }else{
          //check if the sample code belongs to the current login user
          $mapUserSample = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('src_loc_id'=>$posted_ro_office_id,'dst_loc_id'=>$posted_ro_office_id,'stage_smpl_cd'=>$sample_code)))->first();
         
           if(!empty($mapUserSample)){
            $show_details ='yes';
          }
        }
        
           $sampleInward = array();
           $location = '';
           $commodity = '';
           $SampleType = '';
           $lastAction = '';
        
           //check sample details
           if($show_details=='yes'){
              
           $org_smpl_cd = $mapUserSample['org_sample_code'];
           $this->loadModel('SampleInward');
           $sampleInward = $this->SampleInward->find('all',array('fields'=>array('loc_id','commodity_code','sample_type_code'),'conditions'=>array('org_sample_code IS'=>$org_smpl_cd)))->first();
          
          if(!empty($sampleInward)){
            //get location from loc id
            $this->loadModel('DmiRoOffices');
            $office = $this->DmiRoOffices->find('all',array('fields'=>'ro_office','conditions'=>array('id'=>$sampleInward['loc_id'])))->first();
            $location = $office['ro_office'];
            
            //get commodity name
            $this->loadModel('MCommodity');
            $getCommodity = $this->MCommodity->find('all',array('fields'=>'commodity_name','conditions'=>array('commodity_code'=>$sampleInward['commodity_code'])))->first();
            $commodity = $getCommodity['commodity_name'];
            
            //get Sample Type
            $this->loadModel('MSampleType');
            $getSampleType = $this->MSampleType->find('all',array('fields'=>'sample_type_desc','conditions'=>array('sample_type_code'=>$sampleInward['sample_type_code'])))->first();
            $SampleType = $getSampleType['sample_type_desc'];
            
            //get last action of the sample
            $stageFlagArray = array(
                        'SI'=>'Saved Sample Inward',
                        'SD'=>'Saved Sample Details',
                        'OF'=>'Forwarded to Inward Officer',
                        'AS'=>'Accepted by Inward Officer',
                        'IF'=>'Forwarded to Inward Officer',
                        'HF'=>'Head office to Inward Officer',
                        'HS'=>'Accepted by HO',
                        'LI'=>'Allocated by Lab_Incharge',
                        'RIF'=>'Forwarded for Retest',
                        'R'=>'Marked for Retest',
                        'FR'=>'Forwarded to RAL',
                        'AR'=>'Approved Results by Inward Officer',
                        'FO'=>'Forwarded to OIC',
                        'FG'=>'Final Graded by OIC',
                        'TA'=>'Allocated to Chemist',
                        'FS'=>'Forward back to RAL',
                        'FC'=>'Forward back to CAL',
                        'FGIO'=>'Final Graded by Inward Officer',
                        'VC'=>'Sample Verified',
                        'VS'=>'Sample Verified',
                        'PS'=>'Payment Saved and Pending with DDO',
                        'PC'=>'Payment Confirmed & Available to Forward',
                        'PR'=>'Payment Referred Back',
                        'FT'=>'Test Finalized',
                        'NABC'=>'Not Accepted By Chemist',
                        'TABC'=>'Test Accepted By Chemist');
                      
            $getlastFlag = $this->Workflow->find('all',array('fields'=>'stage_smpl_flag','conditions'=>array('org_sample_code'=>$org_smpl_cd),'order'=>'id desc'))->first();
            $lastAction = $stageFlagArray[trim($getlastFlag['stage_smpl_flag'])];
          }
          
        }
        if(!empty($sampleInward)){

          echo "<table class='table table-bordered table-hover table-striped' border='1' id='sample_code_details_btl'>
            <thead class='tablehead'>
               <tr>
                   <th>Sample Code</th>
                   <th>Sample Type</th>
                   <th>Commodity</th>
                   <th>Location</th>
                   <th>Last Action</th>
                   <th>Remark</th>
                   <th>Action</th>
               </tr>
            </thead>
            <tbody>
                <tr>
                    <td id='sample_code' >".$sample_code."</td>
                    <td id='sample_type'>".$SampleType."</td>
                    <td id='commodity'>".$commodity."</td>
                    <td id='location'>".$location."</td>
                    <td id='last_action'>".$lastAction."</td>
                    <td id='last_action'>".' <textarea class="form-control" id="remark" placeholder="Enter Remark" aria-label="With textarea"></textarea>'."</td>
                    <span id='error_remark' class='error'></span>
                    <td><div class='form-buttons'><a href='#' id='markjunk' class='table_record_add_btn btn btn-info btn-sm'> Mark Junk</a></div></td>
                </tr>
            </tbody>
          </table>";

        }else{
          echo "<p class='alert alert-danger'>The Sample code does not exist OR The Sample does not belongs to you</p>";
        }
        
        exit;
       }
}

?>