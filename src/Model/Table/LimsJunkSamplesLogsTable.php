<?php

namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class LimsJunkSamplesLogsTable extends Table{
	
	var $name = "LimsJunkSamplesLogs";
  
    public function sampleCodeDetails(){

            if(strpos(base64_decode($_SESSION['username']), '@') !== false){//for email encoding
              $username = $_SESSION['username'];
            }

            $conn = ConnectionManager::get('default');
            $Workflow = TableRegistry::getTableLocator()->get('Workflow');
          
            $posted_ro_office_id = $_SESSION['posted_ro_office'];

            // $q = $conn->execute("SELECT *
            // FROM lims_junk_samples_logs 
            // WHERE id IN ( SELECT MAX(id) FROM lims_junk_samples_logs 
            // GROUP BY sample_code) AND status='junked'");

    
            /* this query are used for to select last inseted record 
               with status = junked from lims_junk_sample_code and check posted_ro_office_id = src_loc_id OR posted_ro_office_id = dst_loc_id
            by shankhpal shende on 16/03/2023   
            */

            $q = $conn->execute("SELECT MAX(j.id) as id, 
              MAX(j.sample_code) as sample_code, 
              MAX(j.sample_type) as sample_type, 
              MAX(j.commodity) as commodity, 
              MAX(j.locations) as locations, 
              MAX(j.last_action) as last_action, 
              MAX(j.status) as status,
              MAX(j.remark) as remark 
              FROM lims_junk_samples_logs as j 
              INNER JOIN workflow as wf 
              ON wf.org_sample_code = j.org_sample_code 
              WHERE j.id IN ( SELECT MAX(id) FROM lims_junk_samples_logs GROUP BY sample_code) 
              AND j.status = 'junked' AND (wf.src_loc_id = '$posted_ro_office_id' OR wf.dst_loc_id = '$posted_ro_office_id') 
              GROUP BY wf.org_sample_code
              ");

		          $added_sample_code_details = $q->fetchAll('assoc');
             // pr($added_sample_code_details);die;
              return $added_sample_code_details;
              /*
            $q = $conn->execute("SELECT MAX(j.id) as id, 
              MAX(j.sample_code) as sample_code, 
              MAX(j.sample_type) as sample_type, 
              MAX(j.commodity) as commodity, 
              MAX(j.locations) as locations, 
              MAX(j.last_action) as last_action, 
              MAX(j.status) as status,
              MAX(j.remark) as remark 
              FROM lims_junk_samples_logs as j 
              INNER JOIN workflow as wf 
              ON wf.org_sample_code = j.org_sample_code 
              WHERE j.id IN ( SELECT MAX(id) FROM lims_junk_samples_logs GROUP BY sample_code) 
              AND j.status = 'junked' AND (wf.src_loc_id = '$posted_ro_office_id' OR wf.dst_loc_id = '$posted_ro_office_id') 
              GROUP BY wf.org_sample_code
              ");*/

		          $added_sample_code_details = $q->fetchAll('assoc');
             // pr($added_sample_code_details);die;
              return $added_sample_code_details;
    }
    public function saveSampleCodeDetails($org_sample_code,$sample_code,$sample_type,$commodity,$location,$last_action,$remark){
        
      if(strpos(base64_decode($_SESSION['username']), '@') !== false){//for email encoding
				$username = $_SESSION['username'];
			}

      $SampleInward = TableRegistry::getTableLocator()->get('SampleInward');

      //fech result from sampleinward when org_sample_code matched
      $sample_inward_data = $SampleInward->find('all', array('conditions'=>array('org_sample_code IS'=>$org_sample_code)))->first();
     
      // asign variables
      $status_flag = $sample_inward_data['status_flag'];
      $inward_id = $sample_inward_data['inward_id'];
      $fin_year = $sample_inward_data['fin_year'];
      $loc_id = $sample_inward_data['loc_id'];
      $stage_sample_code = $sample_inward_data['stage_sample_code'];
      $sample_type_code = $sample_inward_data['sample_type_code'];


      $Sample_Inward_detail = $SampleInward->newEntity(array(		
        'inward_id'=>$inward_id,
        'fin_year'=>$fin_year,
        'loc_id'=>$loc_id,
        'stage_sample_code'=>$stage_sample_code,
        'sample_type_code'=>$sample_type_code,
        'status_flag'=>'junked',
        'modified'=>date('Y-m-d H:i:s'),			
      ));

    if($SampleInward->save($Sample_Inward_detail)){

          //Similarly, add a new entry in the limsjunksamplelogs table with a status code.
          $lims_junk_sample_entity = $this->newEntity(array(

          'org_sample_code'=>$org_sample_code,
          'sample_code'=>$sample_code,
          'sample_type'=>$sample_type,
          'commodity'=>$commodity,
          'locations'=>$location,
          'last_action'=>$last_action,
          'last_status'=>$status_flag,
          'by_user'=>$username,
          'status'=>'junked',
          'remark'=>$remark,
          'created'=>date('Y-m-d H:i:s')
        
        )); 
        if($this->save($lims_junk_sample_entity)){						
						return true;
					}
        }
      
    

     
    }

    public function unjunkedSampleCodeDetails($org_sample_code,$sample_code,$sample_type,$commodity,$location,$last_action,$remark)
    {
      
        if(strpos(base64_decode($_SESSION['username']), '@') !== false){//for email encoding
          $username = $_SESSION['username'];
        }

        //fech result from sampleinward when org_sample_code matched
         $SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
         $sample_inward_data = $SampleInward->find('all', array('conditions'=>array('org_sample_code IS'=>$org_sample_code)))->first();

        //  $added_sample_code_details = $this->find('all', array('conditions'=>array('org_sample_code IS'=>$org_sample_code)))->first(); // fetch data from table
        //  pr($added_sample_code_details);die;
        //  $last_status = $added_sample_code_details['last_status'];

         $conn = ConnectionManager::get('default');

          $q = $conn->execute("SELECT *
          FROM lims_junk_samples_logs 
          WHERE org_sample_code='$org_sample_code' AND id IN ( SELECT MAX(id) FROM lims_junk_samples_logs 
          GROUP BY sample_code) ");
           
          $junk_sample = $q->fetchAll('assoc');
       
          $i = 0;
          foreach ($junk_sample as $result) {
           
            $status = $result['status'];
            $last_status = $result['last_status'];
            $i++;
          }

          
    
         // asign variables
          $status_flag = $sample_inward_data['status_flag'];
          $inward_id = $sample_inward_data['inward_id'];
          $fin_year = $sample_inward_data['fin_year'];
          $loc_id = $sample_inward_data['loc_id'];
          $stage_sample_code = $sample_inward_data['stage_sample_code'];
          $sample_type_code = $sample_inward_data['sample_type_code'];


          $Sample_Inward_detail = $SampleInward->newEntity(array(		
            'inward_id'=>$inward_id,
            'fin_year'=>$fin_year,
            'loc_id'=>$loc_id,
            'stage_sample_code'=>$stage_sample_code,
            'sample_type_code'=>$sample_type_code,
            'status_flag'=>$last_status,
            'modified'=>date('Y-m-d H:i:s'),			
          ));

        if($SampleInward->save($Sample_Inward_detail)){

              //Similarly, add a new entry in the limsjunksamplelogs table with a status code.
              $lims_junk_sample_entity = $this->newEntity(array(

              'org_sample_code'=>$org_sample_code,
              'sample_code'=>$sample_code,
              'sample_type'=>$sample_type,
              'commodity'=>$commodity,
              'locations'=>$location,
              'last_action'=>$last_action,
              'last_status'=>$status_flag,
              'status'=>'unjunked',
              'by_user'=>$username,
              'remark'=>$remark,
              'created'=>date('Y-m-d H:i:s')
            
            )); 
            if($this->save($lims_junk_sample_entity)){						
                return true;
              }
            }

    }

}

?>