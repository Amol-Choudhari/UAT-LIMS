<?php

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Network\Session\DatabaseSession;
use App\Network\Email\Email;
use App\Network\Request\Request;
use App\Network\Response\Response;
use Cake\Utility\Hash;
use Cake\Datasource\ConnectionManager;

class DevsController extends AppController {

	var $name = 'Devs';

	public function initialize(): void {

		parent::initialize();
		//Load Components
		$this->loadComponent('Customfunctions');
		$this->loadComponent('Authentication');
		//Set Helpers
		$this->viewBuilder()->setHelpers(['Form', 'Html', 'Time']);
	}

	
	
	//Login Customer function start
    public function login() {

        //Set the Layout
        $this->viewBuilder()->setLayout('devs_layout');
    
        // set variables to show popup messages from view file
        $message = '';
        $message_theme = '';
        $redirect_to = '';


		if ($this->request->is('post')) {

			$postData = $this->request->getData();
			$username = $postData['username'];
			$passcode = $postData['passcode'];
		
		
			if($username == null){

				$message = 'Enter the User Email';
				$message_theme = 'failed';
				$redirect_to = 'login';
	
			}else{
				
				$loginpro = $this->proceedLogin(base64_encode($username),$passcode);

				if ($loginpro == 1){

					$message = 'Wrong Passcode Entered.';
					$message_theme = 'failed';
					$redirect_to = 'login';

				}else{
					$this->Session->write('devs','yes');
				}
			}
		}

        // set variables to show popup messages from view file
        $this->set('message', $message);
        $this->set('message_theme', $message_theme);
        $this->set('redirect_to', $redirect_to);
    }



	// Customer Proceed Login
	// Description : this function contains the login logic for Authorized  user & on for multiple logged in check security updates for customers
	// @AUTHOR : Amol Chaudhari (c)
	// #CONTRIBUTER : Akash Thakre (u) (m)
	// DATE : 25-06-2021

	public function proceedLogin($username,$passcode) {

		$this->Session->destroy();
		Session_start();
		$defPasscode = '123';
        $current_ip = $this->request->clientIp();

		if ($passcode == $defPasscode) {

			$this->loadModel('DmiUsers');
			$this->loadModel('DmiUserRoles');
			$this->loadModel('DmiRoOffices');

			$user_data_query = $this->DmiUsers->find('all', array('conditions'=> array('email IS' => $username)))->first();
			$f_name = $user_data_query['f_name'];
			$l_name = $user_data_query['l_name'];
			$once_card_no = '000000000000';
			$division = $user_data_query['division'];
			$user_code = $user_data_query['id'];
			$role=$user_data_query['role'];
			$posted_ro_office=$user_data_query['posted_ro_office'];
	
			$user_flag = $this->DmiUserRoles->find('all', array('fields'=>'user_flag','conditions'=> array('user_email_id IS' => $username)))->first();
	
			$location = $this->DmiRoOffices->find('all', array('conditions'=> array('id IS' => $posted_ro_office)))->first();
	
			//below if-else condition added on 21-05-2019 by Amol
			if(!empty($location)){
				$this->set('location',$location);
				$ro_office=$location['ro_office'];
			}else{
				$ro_office = 'Unknown';
			}
	
			// taking user data in Session variables
			$this->Session->write('userloggedin','yes');
			$this->Session->write('username',$username);
			$this->Session->write('once_card_no',$once_card_no);
			$this->Session->write('last_login_time_value',time()); // Store the "login time" into Session for checking user activity time (Done by pravin 24/4/2018)
			$this->Session->write('division',$division);
			$this->Session->write('f_name',$f_name);
			$this->Session->write('l_name',$l_name);
			$this->Session->write('ip_address',$current_ip);
	
			$this->Session->write('user_flag',$user_flag['user_flag']);
			$this->Session->write('user_code',$user_code);
			$this->Session->write('role',$role);
			$this->Session->write('posted_ro_office',$posted_ro_office);
			$this->Session->write('ro_office',$ro_office);
			$this->Session->write('profile_pic',$user_data_query['profile_pic']); //added on 06-05-2021 for profile pic
			
			if($this->Session->read('division') == 'LMIS' || $this->Session->read('division') == 'BOTH'){
				$this->redirect('/dashboard/home');
			}

		}else{
			return 1;
		}
		

	}



	// Logout 
	// Description : This common logout function is created for the user,chemist and customer customer
	// @Author : Amol Choudhari
	// #Contributer : Akash Thakre
	// Date : 19-04-2022

	public function logout() {
		
		$this->Session->destroy();
		$this->redirect(array('controller'=>'devs', 'action'=>'login'));
	}


	

}
?>
