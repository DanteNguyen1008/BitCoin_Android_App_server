<?php
class UsersController extends AppController{
	
	public function index()
	{
		$userData = $this->User->getUserByLogin('a1provip002@gmail.com','123456');
		var_dump($userData);
	}
	
	public function register($email, $password, $fullname, $phonenumber, $address) {
		$task_title = 'res_sign_up';
		$message = 'fail';
		$status = false;
		
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
			$data['User']['address'] = $address;
			
			if ($this->User->isExist($data)) {
			    $message = 'Email exist';
			}
			else {
			    $this->User->insertUser($data);
			    $message = 'Sign up success';
			    $status = true;
			}
			
		}
		
		
		// Process Json result 
		$response = array();
		$response['task_title'] = $task_title;
		$response['data']['message'] = $message;
		if ($status == true) {
		    $response['data']['register']['user'] = $data['User'];
		    unset($response['data']['register']['user']['password']);
		    $response['data']['register']['credit']['balance'] = BALANCE_INIT;
		}
		$response['status'] = $status;
		echo json_encode ( array('response' => $response) );
	}
	
	public function login($email, $password) {
		$task_title = 'res_log_in';
		$message = 'fail';
		$status = false;
		
		// Check valid input data
		if ($email == '' || $password == '') {
			$message = 'Missing email or password';
		}
		else {
			$data = array ();
			$data['User']['email'] = $email;
			$data['User']['password'] = md5($password);

			$loginSelect = $this->User->loginSelect($data);
			if ($loginSelect) {
				// Store session
				CakeSession::write('login', $email);
				
				$message = 'Login success';
				$status = true;
			}
			else {
				$message = 'Email or password wrong';
			}
		}
		
		// Process Json result 
		$response = array();
		$response['task_title'] = $task_title;
		$response['data']['message'] = $message;
		if ($status == true) {
		    $response['data']['login']['user'] = $loginSelect['User'];
		    unset($response['data']['login']['user']['password']);
		    $response['data']['login']['credit']['balance'] = $loginSelect['User']['balance'];
		}
		$response['status'] = $status;
		return json_encode ( array('response' => $response) );
	}
	
	public function changepassword($old_password, $new_password) {
		$task_title = 'res_change_pass';
		$message = 'fail';
		$status = false;
		
		if (!CakeSession::check('login')) {
			$message = 'Please login first';
		}
		else {
			// Check valid input data
			if ($old_password == '' || $new_password == '') {
				$message = 'Missing old password or new password';
			}
			else {
				$data = array ();
				$data['User']['email'] = CakeSession::read('login');
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
		return json_encode ( array('response' => $response) );
	}
}
