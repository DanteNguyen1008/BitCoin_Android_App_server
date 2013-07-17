<?php
class UsersController extends AppController{
	
	var  $helpers = array('Session');
	
	public function index()
	{
		$userData = $this->User->getUserByLogin('a1provip002@gmail.com','123456');
		var_dump($userData);
	}
	
	public function register() {
		$task_title = 'res_sign_up';
		$message = 'fail';
		$status = false;
		
		// Get request data
		$email = isset($_GET['email']) ? $_GET['email'] : '';
		$password = isset($_GET['password']) ? $_GET['password'] : '';
		$fullname = isset($_GET['fullname']) ? $_GET['fullname'] : '';
		$phonenumber = isset($_GET['phonenumber']) ? $_GET['phonenumber'] : '';
		$adress = isset($_GET['address']) ? $_GET['address'] : '';
		
		// Check valid input data
		if ($email == '' || $password == '' || $fullname == '') {
			$message = 'Email, Password, Fullname are mandatory';
		}
		else {
			$data = array ();
			$data['User']['email'] = $email;
			$data['User']['password'] = md5($password);
			$data['User']['fullname'] = $fullname;
			$data['User']['phonenumber'] = $phonenumber;
			$data['User']['address'] = $adress;
			
			$this->User->insertUser($data);
			
			$message = 'Your change is success';
			$status = true;
		}
		
		
		
		// Process Json result 
		$response = array();
		$response['task_title'] = $task_title;
		$response['data']['message'] = $message;
		$response['status'] = $status;
		echo json_encode ( array('response' => $response) );
	}
	
	public function login() {
		
		$task_title = 'res_log_in';
		$message = 'fail';
		$status = false;
		
		// Get request data
		$email = isset($_GET['email']) ? $_GET['email'] : '';
		$password = isset($_GET['password']) ? $_GET['password'] : '';
		
		// Check valid input data
		if ($email == '' || $password == '') {
			$message = 'Missing email or password';
		}
		else {
			$data = array ();
			$data['User']['email'] = $email;
			$data['User']['password'] = md5($password);
				
			if ($this->User->loginSelect($data)) {
				// Store session
				$this->Session->write('login', $email);
				
				$message = 'Login success';
				$status = true;
			}
		}
		
		// Process Json result 
		$response = array();
		$response['task_title'] = $task_title;
		$response['data']['message'] = $message;
		$response['status'] = $status;
		echo json_encode ( array('response' => $response) );
	}
	
	public function changepassword() {
		$task_title = 'res_change_pass';
		$message = 'fail';
		$status = false;
		
		// Get request data
		$old_password = isset($_GET['old_password']) ? $_GET['old_password'] : '';
		$new_password = isset($_GET['new_password']) ? $_GET['new_password'] : '';
		
		if (!$this->Session->check('login')) {
			$message = 'Please login first';
		}
		else {
			// Check valid input data
			if ($old_password == '' || $new_password == '') {
				$message = 'Missing old password or new password';
			}
			else {
				$data = array ();
				$data['User']['email'] = $this->Session->read('login');
				$data['User']['password'] = md5($old_password);
			
				$currentUser = $this->User->loginSelect($data);
				if ($currentUser) {
					// Update new password
					$newdata = array();
					$newdata['User']['password'] = md5($new_password);
					$newdata['User']['id'] = $currentUser['User']['id'];
					if ($this->User->save($newdata)) {
						$message = 'Change password success';
						$status = true;
					}
					else {
						$message = 'Change fail';
					}
				}
				else {
					$message = 'Old password wrong';
				}
			}
		}
		
		// Process Json result 
		$response = array();
		$response['task_title'] = $task_title;
		$response['data']['message'] = $message;
		$response['status'] = $status;
		echo json_encode ( array('response' => $response) );
	}
}
