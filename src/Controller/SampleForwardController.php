<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\View;


class SampleForwardController extends AppController {

	var $name = 'SampleForward';

	public function initialize(): void {

		parent::initialize();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Customfunctions');
		$this->loadModel('DmiSmsEmailTemplates');
		$this->loadModel('LimsUserActionLogs');
		$this->loadComponent('Ilc');

	}

/************************************************************************************************************************************************************************************************************************/

	//TO VALIDATE LOGIN USER
	public function authenticateUser(){

		$this->loadModel('DmiUserRoles');
		$user_access = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$this->Session->read('username'))))->first();

		if (!empty($user_access)) {
			//proceed
		} else {
			$this->customAlertPage("Sorry.. You don't have permission to view this page");
		}
	}


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-|Sample Forward Method Starts|->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//MAIN METHOD FOR SAMPLE FORWARD
	public function sampleForward() {

		$forw_sample_cd = $this->Session->read('forw_sample_cd');

		if (!empty($forw_sample_cd)) {	

			//Load Models, Layouts and Funtions.
			$this->authenticateUser();
			$this->viewBuilder()->setLayout('admin_dashboard');
			$this->loadModel('SampleInward');
			$this->loadModel('MCommodityCategory');
			$this->loadModel('MLab');
			$this->loadModel('DmiRoOffices');
			$this->loadModel('Workflow');
			$this->loadModel('DmiUsers');
			$this->loadModel('DmiUserRoles');
			$conn = ConnectionManager::get('default');

			// Set Variables to Show Pop-up Messages From View File
			$message = '';
			$message_theme = '';
			$redirect_to = '';

			$usr=$_SESSION['user_code'];

			//$st = "SELECT  stage_sample_code FROM sample_inward WHERE stage_sample_code IN(SELECT stage_smpl_cd FROM workflow WHERE stage!='2') and user_code=$usr";
			$username = $this->Session->read('username');

			$current_user_roles = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$username)))->first();

			$this->set('current_user_roles',$current_user_roles);

			/* added new component for ILC Flow done 03/06/2022 by shreeya*/
			//create sample type
			$sampleTypeCode = $this->Customfunctions->createSampleType($forw_sample_cd);
			
			if($sampleTypeCode==9){

				$this->Ilc->getSavedSelectedRALs($sampleTypeCode);
				$this->Ilc->SavedSelectedTestname($sampleTypeCode); /*select save test name on 09-11-22*/

				//to get commodity code & sample available qnt , unit done by shreeya on 11-11-2022
				$query = $conn->execute("SELECT mc.commodity_name,mc.commodity_code,muw.unit_id,muw.unit_weight,si.sample_total_qnt
											FROM sample_inward AS si
											INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
											INNER JOIN m_unit_weight AS muw ON si.parcel_size=muw.unit_id
											WHERE  si.org_sample_code='$forw_sample_cd' AND si.sample_type_code=$sampleTypeCode ");

				$getcommodity = $query->fetch('assoc');

				$this->set('getcommodity',$getcommodity);

				//added for Test name show the list by shreeya on 07-11-2022
				$test_name = $conn->execute("SELECT m.test_code,m.test_name FROM commodity_test AS ct
												INNER JOIN m_test AS m ON m.test_code = ct.test_code
												WHERE ct.commodity_code='".$getcommodity['commodity_code']."' ORDER BY m.test_code"); 

				
				$this->set('test_name',$test_name);
			
				
			}
			
			$this->set('res',array($forw_sample_cd=>$forw_sample_cd));

			$offices = "";
			$this->set('office',$offices);

			if (null !==($this->request->getData('forward_sample'))) {

				if($sampleTypeCode==9){

					$checkexist=$this->Ilc->checkSavedornot($forw_sample_cd);
					if ($checkexist ==false) {

						$this->set('validate_err',"Please save the selected RALs/CALs & Select Test Name then proceed to forward sample");
						return null;
					}

					// added for check save or not for test name done by shreeya on 09-11-2022
					$checkedsave=$this->Ilc->savedornot($forw_sample_cd);
					if ($checkedsave ==false) {

						$this->set('validate_err',"Please save the Selected Test Name then proceed to forward sample");
						return null;
					}
					
					// save record with new generating map code by shreeya 22-06-2022
					$this->Ilc->SelectSavedMapping($sampleTypeCode);
					// save to workflow and sample inward table by shreeya 
					$this->Ilc->SavedToSampleInward($sampleTypeCode);
					//forward to ilc sample type done by shreeya on 22-06-2022
					$ilcforwardres=$this->Ilc->ilcsampleForward($sampleTypeCode);
					if($ilcforwardres==true){

						$message = 'The ILC sample with registration code '.$this->request->getData('stage_sample_code').' is forwarded to selected RALs/CALs';
						$message_theme = 'success';
						$redirect_to = 'forwarded_list';

					}
					
				//else all other samples forwarded normaly
				} else {

					$sample_code	= $this->request->getData('stage_sample_code');

					//Checked Empty Conditions on Post Data
					if($this->request->getData('dst_usr_cd') != '' && $this->request->getData('ral_cal') != '' && $this->request->getData('dst_loc_id') != '' ) {

						//Check Post Data Validations
						$validate_err = $this->forwardPostValidations($this->request->getData());

						if ($validate_err != '') {

							$this->set('validate_err',$validate_err);
							return null;
						}

						//HTML Encoding
						$postdata = $this->request->getData();

						foreach ($postdata as $key => $value) {

							$postdata[$key] = htmlentities($postdata[$key], ENT_QUOTES);
						}

						$this->loadModel('Workflow');
						$this->loadModel('DmiUsers');
						$this->loadModel('SampleInward');


						$ogrsample1	= $this->SampleInward->find('all', array('conditions'=> array('stage_sample_code IS' => $sample_code)))->first();
						
						$ogrsample	= $ogrsample1['org_sample_code'];

						$user_code	= $this->request->getData('dst_usr_cd');

						$user_code_pattern	= '/^[0-9]+$/';

						$office_code	= $this->request->getData('ral_cal');

						$dst_user_office = $this->request->getData('dst_loc_id');

						$tran_date		= date('Y-m-d');
						$dispatch_date	= date("Y/m/d");

						//Generate New Stage Sample Code
						$new_sample_code	= $this->Customfunctions->createStageSampleCode();

						if ($office_code == 'HO') {

							$flag = "HF";

						} else {

							$flag = "OF";
						}

						//Checks if the Sample is Already Forwarded.
						$already_forwarded = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag IN'=>array('HF','OF'),'org_sample_code'=>$ogrsample)))->first();

						if (empty($already_forwarded)) {

							$workflow_data	= array("org_sample_code"=>$ogrsample,
														"src_loc_id"=>$_SESSION["posted_ro_office"],
														"src_usr_cd"=>$_SESSION["user_code"],
														"dst_loc_id"=>$dst_user_office,
														"dst_usr_cd"=>$user_code,
														"stage_smpl_cd"=>$new_sample_code,
														"tran_date"=>$tran_date,
														"user_code"=>$_SESSION["user_code"],
														"stage"=>"4",
														"stage_smpl_flag"=>$flag);

							$workflowEntity = $this->Workflow->newEntity($workflow_data);
					
							//Save the Data
							if ($this->Workflow->save($workflowEntity)) {

								if ($office_code=='HO') {

									$str="UPDATE sample_inward SET status_flag='H',dispatch_date='$dispatch_date' WHERE stage_sample_code='$ogrsample'";

								} elseif ($office_code=='CAL') {

									$str="UPDATE sample_inward SET status_flag='F',chlng_smpl_disptch_cal_dt='$tran_date',dispatch_date='$dispatch_date'   WHERE stage_sample_code='$ogrsample'";

								} else {

									$str="UPDATE sample_inward SET status_flag='F',dispatch_date='$dispatch_date' WHERE stage_sample_code='$ogrsample'";
								}

								$query = $conn->execute("SELECT user_flag,ro_office FROM workflow
															INNER JOIN dmi_users ON workflow.dst_usr_cd=dmi_users.id
															INNER JOIN dmi_user_roles ON dmi_users.email=dmi_user_roles.user_email_id
															INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id
															INNER JOIN sample_inward ON workflow.org_sample_code=sample_inward.org_sample_code
															WHERE workflow.org_sample_code='$ogrsample'AND workflow.dst_usr_cd = $user_code");

								$user_flag1 = $query->fetchAll('assoc');
								
								$this->set('user_flag1',$user_flag1);

								$user_flag_new = $user_flag1[0]['user_flag'];
								$ro_office_new = $user_flag1[0]['ro_office'];
								
				
								$conn->execute($str);

								$get_user_codes = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_cd IS'=>$new_sample_code)))->first();
								$ralcaloic = $this->DmiRoOffices->getOfficeIncharge($get_user_codes['dst_loc_id']);

								#SMS: Sample Forward
								$this->DmiSmsEmailTemplates->sendMessage(90,$get_user_codes['src_usr_cd'],$new_sample_code); #Source
								$this->DmiSmsEmailTemplates->sendMessage(91,$get_user_codes['dst_usr_cd'],$new_sample_code); #Destination
								$this->DmiSmsEmailTemplates->sendMessage(141,$ralcaloic,$new_sample_code); #OIC
								
								// For Maintaining Action Log by Akash (26-04-2022)
								$this->LimsUserActionLogs->saveActionLog('Sample Forward','Success');

								$message = 'The sample with registration code '.$this->request->getData('stage_sample_code').' is forwarded to '.$user_flag_new.' '.$ro_office_new.' with code as '.$new_sample_code;
								$message_theme = 'success';
								$redirect_to = 'forwarded_list';

							} else {

								// For Maintaining Action Log by Akash (26-04-2022)
								$this->LimsUserActionLogs->saveActionLog('Sample Forward','Failed');
								$message = 'Sorry... The Sample not forwarded properly. Please check.';
								$message_theme = 'failed';
								$redirect_to = 'available_to_forward_list';
							}

						} else {

							// For Maintaining Action Log by Akash (26-04-2022)
							$this->LimsUserActionLogs->saveActionLog('Sample Forward','Failed');
							$message = 'The selected sample is already forwarded.';
							$message_theme = 'alertinfo';
							$redirect_to = 'available_to_forward_list';
						}
						
					} else {

						$this->LimsUserActionLogs->saveActionLog('Sample Forward','Failed'); #Action
						$message = 'Sorry... Please select proper inputs';
						$message_theme = 'warning';
						$redirect_to = 'available_to_forward_list';
					}
				}
			}

			//Set Variables to Show Pop-up Messages From View File
			$this->set('message',$message);
			$this->set('message_theme',$message_theme);
			$this->set('redirect_to',$redirect_to);

		}
	
	}

/************************************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-|Edit Sample Forward Method Ends|->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

 	//To Edit the Forwarded Sample
	public function editForwardedSample($forwarded_sample_code) {

		$this->Session->write('stage_smpl_cd',$forwarded_sample_code);

		if (!empty($forwarded_sample_code)) {

			//Load Models, Layouts and Funtions.
			$this->authenticateUser();
			$this->viewBuilder()->setLayout('admin_dashboard');
			$this->loadModel('SampleInward');
			$this->loadModel('MCommodityCategory');
			$this->loadModel('MLab');
			$this->loadModel('DmiRoOffices');
			$this->loadModel('Workflow');
			$this->loadModel('DmiUsers');
			$this->loadModel('DmiUserRoles');
			$conn = ConnectionManager::get('default');

			// Set Variables to Show Pop-up Messages From View File
			$message = '';
			$message_theme = '';
			$redirect_to = '';

			$username = $this->Session->read('username');

			$current_user_roles = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$username)))->first();

			$this->set('current_user_roles',$current_user_roles);

			$this->set('res',array($forwarded_sample_code=>$forwarded_sample_code));

			$offices = "";

			$this->set('office',$offices);
			$conn = ConnectionManager::get('default');

			//To Get User's Name & User'a Email Id
			$query = $conn->execute("SELECT si.stage_sample_code, w.stage_smpl_cd, mc.commodity_name, st.sample_type_desc, w.tran_date, du.f_name, du.l_name, du.email
										FROM sample_inward AS si
										INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
										INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
										INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
										INNER JOIN dmi_users AS du ON du.id=w.dst_usr_cd
										WHERE w.stage_smpl_flag='OF'  AND w.stage_smpl_cd='".$forwarded_sample_code."' AND w.src_usr_cd='".$_SESSION['user_code']."' ORDER BY w.tran_date DESC");

			$res3 = $query->fetchAll('assoc');

			if (count($res3)>0) {

				$this->set('res3',$res3);
			}


			if (null !==($this->request->getData('forward_sample'))) {

				$sample_code	= $this->request->getData('stage_sample_code');

				//Checked Empty Conditions on Post Data
				if($this->request->getData('dst_usr_cd') != '' && $this->request->getData('ral_cal') != '' && $this->request->getData('dst_loc_id') != '' ) {

					//Check Post Data Validations
					$validate_err = $this->forwardPostValidations($this->request->getData());

					if ($validate_err != '') {

						$this->set('validate_err',$validate_err);
						return null;
					}

					//HTML Encoding
					$postdata = $this->request->getData();

					foreach ($postdata as $key => $value) {

						$postdata[$key] = htmlentities($postdata[$key], ENT_QUOTES);
					}

					$this->loadModel('Workflow');
					$this->loadModel('DmiUsers');
					$this->loadModel('SampleInward');


					$ogrsample1	= $this->SampleInward->find('all', array('conditions'=> array('stage_sample_code IS' => $sample_code)))->first();

					$ogrsample	= $ogrsample1['org_sample_code'];

					$user_role = $this->Workflow->find('all',array('fields'=>array('src_usr_cd'),'conditions'=>array('org_sample_code IS'=>$ogrsample)))->first();

					$usercode = $user_role['src_usr_cd'];

					$user_role = $this->DmiUsers->find('all',array('fields'=>array('role'),'conditions'=>array('id IS'=>$usercode)))->first();

					$user_code	= $this->request->getData('dst_usr_cd');

					$user_code_pattern	= '/^[0-9]+$/';

					$office_code	= $this->request->getData('ral_cal');

					$dst_user_office = $this->request->getData('dst_loc_id');

					$tran_date		= date('Y-m-d');

					$dispatch_date	= date("Y/m/d");

					//Generate New Stage Sample Code
					$new_sample_code	= $this->Customfunctions->createStageSampleCode();

					if ($office_code == 'HO') {

						$flag = "HF";

					} else {

						$flag = "OF";
					}

					//Checks if the Sample is Already Forwarded.
					$already_forwarded = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag IN'=>array('HF','OF'),'org_sample_code'=>$ogrsample)))->first();

					if (empty($already_forwarded)) {

						$workflow_data	= array("org_sample_code"=>$ogrsample,
													"src_loc_id"=>$_SESSION["posted_ro_office"],
													"src_usr_cd"=>$_SESSION["user_code"],
													"dst_loc_id"=>$dst_user_office,
													"dst_usr_cd"=>$user_code,
													"stage_smpl_cd"=>$new_sample_code,
													"tran_date"=>$tran_date,
													"user_code"=>$_SESSION["user_code"],
													"stage"=>"4",
													"stage_smpl_flag"=>$flag);

						$workflowEntity = $this->Workflow->newEntity($workflow_data);

						//Save the Data
						if ($this->Workflow->save($workflowEntity)) {

							if ($office_code=='HO') {

								$str="UPDATE sample_inward SET status_flag='H',dispatch_date='$dispatch_date' WHERE stage_sample_code='$ogrsample'";

							} elseif ($office_code=='CAL') {

								$str="UPDATE sample_inward SET status_flag='F',chlng_smpl_disptch_cal_dt='$tran_date',dispatch_date='$dispatch_date'   WHERE stage_sample_code='$ogrsample'";

							} else {

								$str="UPDATE sample_inward SET status_flag='F',dispatch_date='$dispatch_date' WHERE stage_sample_code='$ogrsample'";
							}

							$query = $conn->execute("SELECT user_flag,ro_office FROM workflow
													INNER JOIN dmi_users ON workflow.dst_usr_cd=dmi_users.id
													INNER JOIN dmi_user_roles ON dmi_users.email=dmi_user_roles.user_email_id
													INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id
													INNER JOIN sample_inward ON workflow.org_sample_code=sample_inward.org_sample_code
													WHERE workflow.org_sample_code='$ogrsample'AND workflow.dst_usr_cd = $user_code");

							$user_flag1 = $query->fetchAll('assoc');

							$this->set('user_flag1',$user_flag1);

							$user_flag_new = $user_flag1[0]['user_flag'];
							$ro_office_new = $user_flag1[0]['ro_office'];

							$conn->execute($str);

							// For Maintaining Action Log by Akash (26-04-2022)
							$this->LimsUserActionLogs->saveActionLog('Sample Forward','Success');
							$message = 'The sample with registration code '.$this->request->getData('stage_sample_code').' is forwarded to '.$user_flag_new.' '.$ro_office_new.' with code as '.$new_sample_code;
							$message_theme = 'success';
							$redirect_to = 'forwarded_list';

						} else {
							// For Maintaining Action Log by Akash (26-04-2022)
							$this->LimsUserActionLogs->saveActionLog('Sample Forward','Failed');
							$message = 'Sorry... The Sample not forwarded properly. Please check.';
							$message_theme = 'failed';
							$redirect_to = 'available_to_forward_list';
						}

					} else {
						// For Maintaining Action Log by Akash (26-04-2022)
						$this->LimsUserActionLogs->saveActionLog('Sample Forward','Failed');
						$message = 'The selected sample is already forwarded.';
						$message_theme = 'alertinfo';
						$redirect_to = 'available_to_forward_list';
					}

				} else {
					// For Maintaining Action Log by Akash (26-04-2022)
					$this->LimsUserActionLogs->saveActionLog('Sample Forward','Failed');
					$message = 'Sorry... Please select proper inputs';
					$message_theme = 'warning';
					$redirect_to = 'available_to_forward_list';
				}
			}

			//Set Variables to Show Pop-up Messages From View File
			$this->set('message',$message);
			$this->set('message_theme',$message_theme);
			$this->set('redirect_to',$redirect_to);

		}


	}


/************************************************************************************************************************************************************************************************************************/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-|Sample Forward Method Ends|->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/



	//SAMPLE FORWARD LIST METHOD SAMPLES THAT ARE READY TO FORWARD
	public function sampleForwardList(){

		$this->loadModel('SampleInward');
		$this->loadModel('Workflow');

		$getSamplecodes=array();

		//Get Currnt User
		if ($_SESSION['user_flag']=='HO') {

			$workflowData = $this->Workflow->find('list',array('valueField'=>'org_sample_code','conditions'=>array('dst_usr_cd IS'=>$_SESSION['user_code'],'stage_smpl_flag'=>'HF'),'group'=>array('id','org_sample_code')))->toList();

			if (!empty($workflowData)) {

				//Get Samples
				$getSamplecodes = $this->SampleInward->find('list',array('keyfield'=>'org_sample_code','valueField'=>'org_sample_code','conditions'=>array('org_sample_code IN'=>$workflowData,'display'=>'Y','status_flag'=>'H','acc_rej_flg'=>'A')))->toArray();
			}

		} else {

			$workflowData = $this->Workflow->find('list',array('valueField'=>'org_sample_code','conditions'=>array('dst_usr_cd IS'=>$_SESSION['user_code']),'group'=>array('id','org_sample_code')))->toList();

			if (!empty($workflowData)) {

				//Get Samples
				$getSamplecodes = $this->SampleInward->find('list',array('keyField'=>'org_sample_code','valueField'=>'org_sample_code','conditions'=>array('org_sample_code IN'=>$workflowData,'display'=>'Y','status_flag'=>'S','acc_rej_flg'=>'A')))->toArray();
			}
		}

		return($getSamplecodes);
	}


/************************************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-|SAMPLE REJECT METHOD STARTS|->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//Reject Sample at Sample Forward Stage
	public function sampleReject() {

		$rej_sample_cd = $this->Session->read('rej_sample_cd');

		if (!empty($rej_sample_cd)) {

			$this->authenticateUser();
			$this->viewBuilder()->setLayout('admin_dashboard');
		
			$this->loadModel('SampleInward');
			$this->loadModel('Workflow');
			$this->loadModel('SmpRejectAtFwdStage');

			$message='';
			$message_theme = '';
			$redirect_to='';

			$this->set('res',array($rej_sample_cd=>$rej_sample_cd));


			if (null !==($this->request->getData('sample_reject'))) {

				$sample_code = $this->request->getData('stage_sample_code');

				//Check POST Data Validations
				$validate_err = $this->rejectPostValidations($this->request->getData());

				if ($validate_err != '') {

					$this->set('validate_err',$validate_err);
					return null;
				}


				$postdata = $this->request->getData();

				//HTML Encoding
				foreach ($postdata as $key => $value) {

					$postdata[$key] = htmlentities($postdata[$key], ENT_QUOTES);
				}


				if ($this->request->getData('sample_reject_reason') != '') {

					//Get Original Sample Code From Stage Sample Code
					$org_sample_code = $this->Workflow->find('all',array('fields'=>array('org_sample_code'),'conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();

					//Get Inward ID
					$sample_inward_id = $this->SampleInward->find('all',array('fields'=>array('inward_id'),'conditions'=>array('org_sample_code IS'=>$org_sample_code['org_sample_code'])))->first();

					$sample_reject_reason = htmlentities($this->request->getData('sample_reject_reason'), ENT_QUOTES);

					//Update Status Flag to "RJ" For Rejcted Sample
					if ($this->SampleInward->updateAll(array('status_flag'=>'RJ'),array('inward_id'=>$sample_inward_id['inward_id']))) {

						$SmpRejectEntity = $this->SmpRejectAtFwdStage->newEntity(array(
							'sid_id'=>$sample_inward_id['inward_id'],
							'sample_code'=>$sample_code,
							'rejected_by'=>$_SESSION['user_code'],
							'reason'=>$sample_reject_reason,
							'revert_back'=>'no',
							'user_office'=>$_SESSION["posted_ro_office"],
							'created'=>date('Y-m-d H:i:s'),
							'modified'=>date('Y-m-d H:i:s')
						));

						//Save Entry of Reject
						if ($this->SmpRejectAtFwdStage->save($SmpRejectEntity)) {
							
							#SMS: Sample Rejected At Forward
							$this->DmiSmsEmailTemplates->sendMessage(137,$_SESSION['user_code'],$sample_code); #Source
							$this->LimsUserActionLogs->saveActionLog('Sample Rejected at Forward Stage','Success'); #Action
							$message = 'Sample Rejected Successfully';
							$message_theme = 'success';
							$redirect_to = 'rejected_list';
						}
					}

				} else {
					
					$this->LimsUserActionLogs->saveActionLog('Sample Reject at Forward Stage','Failed'); #Action
					$message = 'Please enter sample rejection reason.';
					$message_theme = 'warning';
					$redirect_to = 'available_to_forward_list';
				}
			}

			//Set Variables to Show Pop-up Messages From View File
			$this->set('message',$message);
			$this->set('message_theme',$message_theme);
			$this->set('redirect_to',$redirect_to);
		}

	}

/************************************************************************************************************************************************************************************************************************/


/************************************************************************************************************************************************************************************************************************/

	//GET THROUGH AJAX to FETCH USER FOR SELECED OFFICE TYPE
	public function getUser(){

		$conn = ConnectionManager::get('default');

		if (null !==($_POST['dst_loc_id'])) {

			$user_office_pattern = '/^[0-9]+$/';
			$office_pattern = '/^[A-Z]+$/';

			$user_office_val = preg_match($user_office_pattern,$_POST['dst_loc_id']);
			$office_val = preg_match($office_pattern,$_POST['user_flag']);

			if ($user_office_val==0){ echo "[error]"; exit; }
			if ($office_val==0){ echo "[error]"; exit; }

			$dst_loc_id = $_POST['dst_loc_id'];
			$flag = $_POST['user_flag'];

		} else {

			$dst_loc_id="";
			$flag="";
		}

		$this->loadModel('DmiUsers');

		if ($flag=="HO") {

			$query = $conn->execute("SELECT u.id,u.f_name,u.l_name FROM dmi_users AS u
									INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id
									WHERE posted_ro_office=$dst_loc_id AND ur.user_flag='$flag' AND u.status !='disactive'");
		} else {

			$query = $conn->execute("SELECT u.id,u.f_name,u.l_name FROM dmi_users AS u
									INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id
									WHERE posted_ro_office=$dst_loc_id AND role='Inward Officer' AND ur.user_flag='$flag' AND u.status !='disactive'");
		}

		$user_data = $query->fetchAll('assoc');
		
		if (!empty($user_data)) {
			echo '~'.json_encode($user_data).'~';
		} else {
			echo '~'."0".'~';
		}

		exit;
	
	}

/************************************************************************************************************************************************************************************************************************/

	//GET OFFICE TYPE BY AJAX
	public function getOffice(){

		$this->autoRender=false;
		$conn = ConnectionManager::get('default');

		$user_flag = $_POST['ral'];
		$str="";
		$this->loadModel('DmiUsers');

		$query = $conn->execute("SELECT ur.user_flag,o.id,o.ro_office FROM dmi_users AS u
			                     INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id
			                     INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id AND ur.user_flag='$user_flag'  AND u.status !='disactive'
			                     GROUP BY ur.user_flag,o.id,o.ro_office
			                     ORDER BY o.ro_office ASC");

		$offices = $query->fetchAll('assoc');
		
		$str = "<option value='0' >--Select--</option>";//added this line on 18-05-2022 by Amol
		foreach($offices as $office1) {

			$location=$office1['id'];
			$type=$office1['user_flag'];
			$desc=$office1['ro_office'];
			$str.="<option value='".$location."' >".$type." - ".$desc."</option>";
		}
		echo $str;
		//exit;
	}


/************************************************************************************************************************************************************************************************************************/


	//GET FORWARDED SAMPLES LIST
	public function forwardedSamplesList() {

		$this->loadModel('SampleInward');
		$this->loadModel('Workflow');
		$conn = ConnectionManager::get('default');

		$getSamplecodes=array();

		$query = $conn->execute("SELECT TRIM(w.stage_smpl_cd) AS stage_sample_code
								 FROM sample_inward AS si
								 INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code AND si.display='Y' AND si.status_flag IN('F','H')
								 AND w.stage_smpl_flag IN('OF','HF') AND w.src_usr_cd='".$_SESSION['user_code']."'ORDER BY w.id DESC");

		$result = $query->fetchAll('assoc');

		//Creating Array Format Requird For Listing in View
		foreach ($result as $esch) {

			$getSamplecodes[$esch['stage_sample_code']] = $esch['stage_sample_code'];
		}

		return($getSamplecodes);
	}


/************************************************************************************************************************************************************************************************************************/


	//GENEREATE FORWARDED SAMPLE LETTER WITH PDF
	public function gnrtSmplFrwdLtr(){

	  $ltr_sample_cd = $this->Session->read('ltr_sample_cd');

		if (!empty($ltr_sample_cd)) {

			$this->viewBuilder()->setLayout('admin_dashboard');
			$this->loadModel('SampleInward');
			$this->loadModel('MSampleType');

			//Set Variables to Show Pop-up Messages From View File
			$message = '';
			$redirect_to = '';
			$message_theme = '';

			//$samples_list = $this->forwardedSamplesList();

			$this->set('samples_list',array($ltr_sample_cd=>$ltr_sample_cd));

			$sam_type=$this->MSampleType->find('all',array('conditions' => array('display' => 'Y')))->toArray();
			$this->set('Sample_Type',$sam_type);

		}

	}


/************************************************************************************************************************************************************************************************************************/
	//GENEREATE FORWARDED SAMPLE LETTER WITH PDF For ILC FLOW done 06-07-2022 by shreeya
	public function ilcGnrtSmplFrwdLtr(){

		$ltr_sample_cd = trim($this->Session->read('ltr_sample_cd'));
		$conn = ConnectionManager::get('default');
		if (!empty($ltr_sample_cd)) {

			$this->viewBuilder()->setLayout('admin_dashboard');
			$this->loadModel('SampleInward');
			$this->loadModel('MSampleType');

			//Set Variables to Show Pop-up Messages From View File
			$message = '';
			$redirect_to = '';
			$message_theme = '';

			$this->set('samples_list',array($ltr_sample_cd=>$ltr_sample_cd));

			$sam_type=$this->MSampleType->find('all',array('conditions' => array('display' => 'Y')))->toArray();
			$this->set('Sample_Type',$sam_type);

				$query2 = $conn->execute("SELECT w.stage_smpl_cd,du.email,du.f_name,du.l_name
					FROM ilc_org_smplcd_maps AS sm
					INNER JOIN workflow AS w ON sm.ilc_org_sample_cd=w.org_sample_code
					INNER JOIN dmi_users AS du ON sm.inwd_off_val=du.id
					WHERE  w.stage_smpl_flag='OF' AND sm.org_sample_code='$ltr_sample_cd' AND sm.status = 1 ");
						
					$subsmplres = $query2->fetchAll('assoc');	
					$subsamplelist= array();
					foreach ($subsmplres as $each1) {
						$subsamplelist[$each1['stage_smpl_cd']] = $each1['stage_smpl_cd'];
					}
				
				$this->set('subsamplelist',$subsamplelist);
			}

		
	}

  /*******************************************************************************************************************************************************************************************************/


	//FORWARD LETTER PDF VIEW
	public function frdLetterPdf($stage_sample_code) {

		$this->viewBuilder()->setLayout('pdf_layout');

		$sample_code = $stage_sample_code;
		$this->loadModel('SampleInward');
		$conn = ConnectionManager::get('default');

		#Below Query is Changed - Amol [11-11-2022]
		//Reason: The WHERE clause is changed to generate the forward letter pdf. As it was taking the flags from the SampleInward Table and the 'F' an 'H' flags are changes on next stage
		//So the qyery changed to workflow AS and OF flags to genretae the letter after the forward and accept and lettre on.
		$query = $conn->execute("SELECT si.*,b.sample_type_desc,c.container_desc, a.unit_weight,w.dst_usr_cd,w.dst_loc_id,w.src_usr_cd, w.src_loc_id,w.stage_smpl_cd,m.commodity_name
								 FROM sample_inward AS si
								 INNER JOIN m_sample_type AS b ON b.sample_type_code = si.sample_type_code
								 INNER JOIN m_container_type AS c ON c.container_code = si.container_code
								 INNER JOIN m_unit_weight AS a ON a.unit_id = si.parcel_size
								 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
								 INNER JOIN m_commodity AS m ON m.commodity_code = si.commodity_code
								 WHERE w.stage IN('3','4') AND si.display='Y' AND w.stage_smpl_flag IN('OF','AS') AND w.stage_smpl_cd='$sample_code'");
								 //WHERE w.stage IN('3','4') AND si.display='Y' AND si.status_flag IN('F','H') AND w.stage_smpl_cd='$sample_code'");

		$str_data = $query->fetchAll('assoc');
		$this->set('str_data',$str_data);

		$user_code=$str_data[0]['dst_usr_cd'];
		$location_code=$str_data[0]['dst_loc_id'];

		$this->loadModel('DmiUsers');

		$query=$conn->execute("SELECT u.f_name,u.l_name,ur.role_name,ml.ro_office,r.user_flag
							   FROM dmi_users AS u
							   INNER JOIN user_role AS ur ON ur.role_name=u.role
							   INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
							   INNER JOIN dmi_ro_offices AS ml ON ml.id=u.posted_ro_office
							   AND ml.id='$location_code' AND u.id='$user_code' AND u.status != 'disactive' ");

		$user_data = $query->fetchAll('assoc');
		$this->set('user_data',$user_data);

		//To Show Officer Incharge Name & Designation in 'From' at Bottom Either Sent by Any Officer & Name Should be Display Only of Incharge
		$src_user_code=$str_data[0]['src_usr_cd'];
		$src_location_code=$str_data[0]['src_loc_id'];

		$query=$conn->execute("SELECT u.f_name,u.l_name,u.role,ml.ro_office,r.user_flag
							   FROM dmi_users AS u
			                   INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
			                   INNER JOIN dmi_ro_offices AS ml ON ml.id=u.posted_ro_office
							   AND ml.id='$src_location_code' AND u.email=ml.ro_email_id AND u.status != 'disactive' ");

		$src_user_data = $query->fetchAll('assoc');
		$this->set('src_user_data',$src_user_data);

		//Call to the PDF Creation Common Method
		$this->callTcpdf($this->render(),'I');
	}


/************************************************************************************************************************************************************************************************************************/

	//GENERATE FORWARDED SAMPLE LETTER FOR MULTIPLE SAMPLES IN SINGLE PDF
	public function gnrtMultipleSmplFrwdLtr() {

		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->loadModel('SampleInward');
		$this->loadModel('MSampleType');

		//Set Variables to Show Pop-up Messages From View File
		$message = '';
		$redirect_to = '';

		$samples_list = $this->forwardedSamplesList();

		$this->set('samples_list',$samples_list);

		if (null !==($this->request->getData('generate'))) {

			$selected_samples = $this->request->getData('stage_sample_code_s');

			$this->Session->write('multiple_samples',$selected_samples );
			$this->redirect(array('controller'=>'SampleForward','action'=>'multiple_smpl_frd_letter_pdf'));
		}

	}


/************************************************************************************************************************************************************************************************************************/

	//MULTIPLE SAMPLES FORWARD LETTER IN SINGLE PDF
	public function multipleSmplFrdLetterPdf() {

		$this->viewBuilder()->setLayout('pdf_layout');

		$sample_code_list = $this->Session->read('multiple_samples');
		$this->loadModel('SampleInward');
		$conn = ConnectionManager::get('default');

		$result_array = array();

		$i=0;
		foreach($sample_code_list as $each) {

			$sample_code = $each;

			#Below Query is Changed - Amol [11-11-2022]
			//Reason: The WHERE clause is changed to generate the forward letter pdf. As it was taking the flags from the SampleInward Table and the 'F' an 'H' flags are changes on next stage
			//So the qyery changed to workflow AS and OF flags to genretae the letter after the forward and accept and lettre on.
			$query = $conn->execute("SELECT si.*,b.sample_type_desc,c.container_desc,a.unit_weight,w.dst_usr_cd,w.dst_loc_id,w.src_usr_cd,w.src_loc_id,w.stage_smpl_cd,m.commodity_name
										FROM sample_inward AS si
										INNER JOIN m_sample_type AS b ON b.sample_type_code = si.sample_type_code
										INNER JOIN m_container_type AS c ON c.container_code = si.container_code
										INNER JOIN m_unit_weight AS a ON a.unit_id = si.parcel_size
										INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
										INNER JOIN m_commodity AS m ON m.commodity_code = si.commodity_code
										WHERE w.stage IN('3','4') AND si.display='Y' AND w.stage_smpl_flag IN('OF','AS') AND w.stage_smpl_cd='$sample_code'");
										//WHERE w.stage IN('3','4') AND si.display='Y' AND si.status_flag IN('F','H') AND w.stage_smpl_cd='$sample_code'");

			$str_data = $query->fetchAll('assoc');

			$result_array[$i]['sample_code'] = $sample_code;
			$result_array[$i]['commodity'] = $str_data[0]['commodity_name'];
			$result_array[$i]['sample_type'] = $str_data[0]['sample_type_desc'];
			$result_array[$i]['quantity'] = $str_data[0]['sample_total_qnt'].' '.$str_data[0]['unit_weight'];
			$result_array[$i]['container_type'] = $str_data[0]['container_desc'];

			$i=$i+1;
		}

		$this->set('result_array',$result_array);

		$user_code=$str_data[0]['dst_usr_cd'];

		$location_code=$str_data[0]['dst_loc_id'];

		$this->loadModel('DmiUsers');

		$query=$conn->execute("SELECT u.f_name,u.l_name,ur.role_name,ml.ro_office,r.user_flag FROM dmi_users AS u
			                   INNER JOIN user_role AS ur ON ur.role_name=u.role
			                   INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
			                   INNER JOIN dmi_ro_offices AS ml ON ml.id=u.posted_ro_office AND ml.id='$location_code' AND u.id='$user_code'
			                   AND u.status != 'disactive' ");

		$user_data = $query->fetchAll('assoc');
		$this->set('user_data',$user_data);

		//To Show Officer Incharge Name & Designation in 'From' at Bottom Either Sent by Any Officer & Name Should be Display Only of Incharge
		$src_user_code=$str_data[0]['src_usr_cd'];
		$src_location_code=$str_data[0]['src_loc_id'];

		$query=$conn->execute("SELECT u.f_name,u.l_name,u.role,ml.ro_office,r.user_flag FROM dmi_users AS u
							   INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
			                   INNER JOIN dmi_ro_offices AS ml ON ml.id=u.posted_ro_office AND ml.id='$src_location_code'
			                   AND u.email=ml.ro_email_id
			                   AND u.status != 'disactive' ");

		$src_user_data = $query->fetchAll('assoc');
		$this->set('src_user_data',$src_user_data);

		//Call to the PDF Creation Common Method
		$this->callTcpdf($this->render(),'I');
	}

/************************************************************************************************************************************************************************************************************************/

	//VALIDATION FOR INPUT FIELDS
	public function forwardPostValidations($postData) {

		$validation_status = '';

		if (!is_numeric($postData["stage_sample_code"])) {

			$validation_status = 'Select proper sample code';
		}

		if (!empty($postData["inward_id"]) && !is_numeric($postData["inward_id"])) {

			$validation_status = 'Invalid Inward Id';
		}

		$arr = array("RAL","CAL","HO");

		if (!in_array($postData['ral_cal'],$arr)) {

			$validation_status = 'Invalid Office Type';
		}

		if (!is_numeric($postData["sample_type"])) {

			$validation_status = 'Invalid Sample Type';
		}

		if (!is_numeric($postData["commodity_code"])) {

			$validation_status = 'Select Proper Commodity';
		}

		if (!is_numeric($postData["dst_usr_cd"])) {

			$validation_status = 'Invalid Destination Code';
		}


		return $validation_status;

	}

/************************************************************************************************************************************************************************************************************************/

	//REJECT POST VALIDATIONS
	public function rejectPostValidations($postData) {

		$validation_status = '';

		if (!is_numeric($postData["stage_sample_code"])) {

			$validation_status = 'Select proper sample code';
		}

		if (!empty($postData["inward_id"]) && !is_numeric($postData["inward_id"])) {

			$validation_status = 'Invalid Inward Id';
		}

		if (!is_numeric($postData["sample_type"])) {

			$validation_status = 'Invalid Sample Type';
		}

		if (!is_numeric($postData["commodity_code"])) {

			$validation_status = 'Select Proper Commodity';
		}

		if (empty($postData["sample_reject_reason"])) {

			$validation_status = 'Enter Proper Reason';
		}


		return $validation_status;

	}


/************************************************************************************************************************************************************************************************************************/

	//AVAILABLE SAMPLES FOR FORWARD
	public function availableToForwardList() {

		$res = $this->getSampleListToForward();
		$this->set('res',$res);
	}

/************************************************************************************************************************************************************************************************************************/

	//COMMON METHOD TO FETCH LIST FOR DASHBOARD COUNTS on 28-04-2021 by Amol
	public function getSampleListToForward() {

		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');

		$sample_list = $this->sampleForwardList();
		
		//set array format
		$cus_string= '';
		foreach ($sample_list as $each) {

			$cus_string .= $each."','";
		}

		#=> Query Updated :: /* New status Flag is added i,e status_flah != 'junked' - Shankhpal*/
		$query = $conn->execute("SELECT si.inward_id,si.org_sample_code,si.received_date,st.sample_type_desc,mcc.category_name,mc.commodity_name,ml.ro_office
									FROM sample_inward AS si
									INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
									INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
									INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
									INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
									INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code 
									WHERE si.status_flag !='junked' AND w.stage_smpl_cd NOT IN ('','blank') AND si.stage_sample_code IN ('$cus_string')
									ORDER BY si.received_date DESC");
									// stage_smpl_cd != '' as per change req on 19/04/2023
									//status_flag added by shankhpal shende  

		$res = $query ->fetchAll('assoc');

		return $res;
	
	}

/************************************************************************************************************************************************************************************************************************/

	//TO RE-DIRECT ON SAMPLE FORWARD
	public function redirectToForward($forw_sample_cd){

		$this->Session->write('forw_sample_cd',$forw_sample_cd);
		$this->redirect(array('controller'=>'SampleForward','action'=>'sample_forward'));

	}

/************************************************************************************************************************************************************************************************************************/

	//TO RE-DIRECT ON SAMPLE REJECT
	public function redirectToReject($rej_sample_cd){

		$this->Session->write('rej_sample_cd',$rej_sample_cd);
		$this->redirect(array('controller'=>'SampleForward','action'=>'sample_reject'));

	}

/************************************************************************************************************************************************************************************************************************/

	//TO RE-DIRECT ON GENERATE SAMPLE FORWARD LETTER
	public function redirectToGnrtLtr($ltr_sample_cd){

		$this->Session->write('ltr_sample_cd',$ltr_sample_cd);
		$this->redirect(array('controller'=>'SampleForward','action'=>'gnrt_smpl_frwd_ltr'));

	}

	//TO RE-DIRECT ON GENERATE SAMPLE FORWARD LETTER ILC FLOW by shreeya on 11-07-2022
	public function redirectToGnrtLtrIlc($ltr_sample_cd){

		$this->Session->write('ltr_sample_cd',$ltr_sample_cd);
		$this->redirect(array('controller'=>'SampleForward','action'=>'ilc_gnrt_smpl_frwd_ltr'));

	}
	

/************************************************************************************************************************************************************************************************************************/

	//GET FORWARDED SAMPLE LIST
	public function forwardedList(){

		$conn = ConnectionManager::get('default');
		$user_code = $_SESSION['user_code'];


		//To Get User's Name & User'a Email Id
		#=> Query Updated :: /* New status Flag is added i,e status_flah != 'junked' - Shankhpal*/
		$query = $conn->execute("SELECT si.stage_sample_code, w.stage_smpl_cd, mc.commodity_name, st.sample_type_desc, w.tran_date, du.f_name, du.l_name, du.email
									FROM sample_inward AS si
									INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
									INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
									INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
									INNER JOIN dmi_users AS du ON du.id=w.dst_usr_cd
									WHERE w.stage_smpl_flag='OF' 
										AND si.status_flag !='junked' 
										AND w.stage_smpl_cd NOT IN ('','blank')
										AND si.sample_type_code != 9 
										AND si.entry_type IS NULL 
										AND w.src_usr_cd='$user_code' 
									ORDER BY w.tran_date DESC");//added sample type and entery flag contn by shreeya

		$res3 = $query->fetchAll('assoc');
	
		if (count($res3)>0) {

			$this->set('res3',$res3);
		}
		
			//below query is added by shreeya on 04-07-2022
			// for ILC flow show the list of ilc samples
			//To Get User's Name & User'a Email Id
			$query1 = $conn->execute("SELECT si.stage_sample_code, w.stage_smpl_cd,mc.commodity_name, st.sample_type_desc, w.tran_date, du.f_name, du.l_name, du.email
			FROM sample_inward AS si
			INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
			INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
			INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
			INNER JOIN dmi_users AS du ON du.id=w.dst_usr_cd
			WHERE  w.stage_smpl_flag='OF'  AND si.status_flag !='junked' AND w.stage_smpl_cd NOT IN ('','blank')  AND si.sample_type_code = 9 AND si.entry_type IS NULL AND w.src_usr_cd='$user_code'  ORDER BY w.tran_date DESC");
			//above query changed by shreeya for ILC flow on 04-07-2022
			// status_flag != 'junked' condition added by shankhpal shende 
			// stage_smpl_cd != '' as per change req on 19/04/2023 
			$result = $query1->fetchAll('assoc');
			if (count($result)>0) {

				$this->set('result',$result);
				
				//added for fetch min 5 RAL's records in ilc table list using dropdown 
				//done 05-07-2022 by shreeya
				$i=1;

				$subsamplelist= array();
				$userdetailslist= array();
				
				foreach ($result as $each) {

					$query2 = $conn->execute("SELECT w.stage_smpl_cd,du.email,du.f_name,du.l_name
								FROM ilc_org_smplcd_maps AS sm
					 			INNER JOIN workflow AS w ON sm.ilc_org_sample_cd=w.org_sample_code
					 			INNER JOIN dmi_users AS du ON sm.inwd_off_val=du.id
					 			WHERE  w.stage_smpl_flag='OF' AND sm.org_sample_code='".$each['stage_sample_code']."' AND sm.status = 1 ");
					
					$subsmplres = $query2->fetchAll('assoc');

					foreach ($subsmplres as $each1) {
						
						$subsamplelist[$i][] = $each1['stage_smpl_cd'];
						$userdetailslist[$i][] = $each1['f_name'].' '.$each1['l_name'].' ('. base64_decode($each1['email']).')';
						
					}

					$i++;
				}

				$this->set('subsamplelist',$subsamplelist);
				$this->set('userdetailslist',$userdetailslist);
			}

	}

/************************************************************************************************************************************************************************************************************************/

	//SHOW REJECTED SAMPLES LIST
	public function rejectedList() {

		$rejected_sample_list = array();
		$this->loadModel('SmpRejectAtFwdStage');
		$conn = ConnectionManager::get('default');
		$user_code = $_SESSION['user_code'];

		$rejected_sample_codes = $this->SmpRejectAtFwdStage->find('list',array('keyField'=>'sample_code','valueField'=>'sample_code','group'=>array('sample_code')))->toArray();

		foreach ($rejected_sample_codes as $each_code) {

			$max_id = $this->SmpRejectAtFwdStage->find('all',array('fields'=>array('id'),'conditions'=>array('sample_code IS'=>$each_code),'order'=>array('id desc')))->first();
			$max_id = $max_id['id'];

			#=> Query Updated :: /* New status Flag is added i,e status_flah != 'junked' - Shankhpal*/
			$query = $conn->execute("SELECT sr.sample_code, mc.commodity_name, mst.sample_type_desc, sr.created, sr.sid_id
									FROM smp_reject_at_fwd_stage AS sr
									INNER JOIN sample_inward AS si ON si.org_sample_code = sr.sample_code
									INNER JOIN m_commodity AS mc ON mc.commodity_code = si.commodity_code
									INNER JOIN m_sample_type AS mst ON mst.sample_type_code = si.sample_type_code
									INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code 
									WHERE sr.rejected_by='$user_code' AND w.stage_smpl_cd NOT IN ('','blank') AND si.status_flag !='junked' AND sr.id='$max_id' AND sr.revert_back='no'");

									// status_flag != junked added by shankhpal shende 
									// stage_smpl_cd != '' as per change req on 19/04/2023
			$rejected_sample = $query->fetchAll('assoc');

			if (!empty($rejected_sample)) {

				$rejected_sample_list[] = $rejected_sample;
			}
		}

		$this->set('rejected_sample_list',$rejected_sample_list);

		if (!empty($_POST['sample_reject_undo'])) {

			if (null !==($_POST['sample_reject_undo'])) {

				$this->loadModel('SampleInward');

				//update status flag to RJ for rejcted sample
				if ($this->SampleInward->updateAll(array('status_flag'=>'S'),array('inward_id'=>$_POST['sid_id']))) {

					//save entry to reject log table
					$SmpRejectEntity = $this->SmpRejectAtFwdStage->newEntity(array(
						'sid_id'=>$_POST['sid_id'],
						'sample_code'=>$_POST['sample_code'],
						'revert_back_by'=>$_SESSION['user_code'],
						'revert_back'=>'yes',
						'user_office'=>$_SESSION["posted_ro_office"],
						'created'=>date('Y-m-d H:i:s'),
						'modified'=>date('Y-m-d H:i:s')
					));

					if ($this->SmpRejectAtFwdStage->save($SmpRejectEntity)) {
						
						#SMS: Sample Reverted [YTR]
						$this->DmiSmsEmailTemplates->sendMessage(149,$_SESSION['user_code'],$_POST['sample_code']); #Source
						$this->LimsUserActionLogs->saveActionLog('Sample Reverted','Success'); #Action

						echo '~1~';
						exit;

					} else {
						echo '~0~';
						exit;
					}

				} else {

					echo '~0~';
					exit;
				}
			}
		}
	}


}
?>
