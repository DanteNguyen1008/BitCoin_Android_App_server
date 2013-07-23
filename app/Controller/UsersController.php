<?php
class UsersController extends AppController {
	public $components = array('Session', 'GamePlay', 'RandomNumberGenerator');
	public function index() {

	}

	public function register() {
		if (IS_DEBUG == true) {
			$email = isset($_GET['email']) ? $_GET['email'] : '';
			$password = isset($_GET['password']) ? $_GET['password'] : '';
			$fullname = isset($_GET['fullname']) ? $_GET['fullname'] : '';
			$phonenumber = isset($_GET['phonenumber']) ? $_GET['phonenumber'] : '';
			$address = isset($_GET['address']) ? $_GET['address'] : '';
		} else {
			$email = isset($_POST['email']) ? $_POST['email'] : '';
			$password = isset($_POST['password']) ? $_POST['password'] : '';
			$fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
			$phonenumber = isset($_POST['phonenumber']) ? $_POST['phonenumber'] : '';
			$address = isset($_POST['address']) ? $_POST['address'] : '';
		}

		$task_title = 'res_sign_up';
		$message = 'fail';
		$status = false;

		// Check valid input data
		if ($email == '' || $password == '' || $fullname == '') {
			$message = 'Email, Password, Fullname are mandatory';
		} else {
			$data = array();
			$data['User']['email'] = $email;
			$data['User']['password'] = md5($password);
			$data['User']['fullname'] = $fullname;
			$data['User']['phonenumber'] = $phonenumber;
			$data['User']['address'] = $address;
			$data['User']['bitcoinaddress'] = '';
			if ($this -> User -> isExist($data)) {
				$message = 'Email exist';
			} else {
				$data['User']['id'] = $this -> User -> insertUser($data);
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
		echo json_encode(array('response' => $response));
	}

	public function login() {

		if (IS_DEBUG == true) {
			$email = isset($_GET['email']) ? $_GET['email'] : '';
			$password = isset($_GET['password']) ? $_GET['password'] : '';
		} else {
			$email = isset($_POST['email']) ? $_POST['email'] : '';
			$password = isset($_POST['password']) ? $_POST['password'] : '';
		}
		$task_title = 'res_log_in';
		$message = 'fail';
		$status = false;

		// Check valid input data
		if ($email == '' || $password == '') {
			$message = 'Missing email or password';
		} else {
			$data = array();
			$data['User']['email'] = $email;
			$data['User']['password'] = md5($password);
			$loginSelect = $this -> User -> loginSelect($data);
			if ($loginSelect) {
				// Store session
				//CakeSession::write('login', $email);
				$this -> Session -> write('login', $email);
				$message = 'Login success';
				$status = true;
			} else {
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
		echo json_encode(array('response' => $response));
	}

	public function changepassword() {
		if (IS_DEBUG == true) {
			$old_password = isset($_GET['old_password']) ? $_GET['old_password'] : '';
			$new_password = isset($_GET['new_password']) ? $_GET['new_password'] : '';
		} else {
			$old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
			$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
		}
		$task_title = 'res_change_pass';
		$message = 'fail';
		$status = false;
		/*
		 if (!CakeSession::check('login')) {
		 $message = 'Please login first';
		 }*/
		if (!$this -> Session -> check('login')) {
			$message = 'Please login first';
		} else {
			// Check valid input data
			if ($old_password == '' || $new_password == '') {
				$message = 'Missing old password or new password';
			} else {
				$data = array();
				$data['User']['email'] = $this -> Session -> read('login');
				$data['User']['password'] = md5($old_password);

				$currentUser = $this -> User -> loginSelect($data);
				if ($currentUser) {
					// Update new password
					$newdata = array();
					$newdata['User']['password'] = md5($new_password);
					$newdata['User']['id'] = $currentUser['User']['id'];
					if ($this -> User -> save($newdata)) {
						$message = 'Change password success';
						$status = true;
					} else {
						$message = 'Change fail';
					}
				} else {
					$message = 'Old password wrong';
				}
			}
		}

		// Process Json result
		$response = array();
		$response['task_title'] = $task_title;
		$response['data']['message'] = $message;
		$response['status'] = $status;
		echo json_encode(array('response' => $response));
	}

	public function bet() {
		$bets = $_GET['bet'];
		$betAmounts = $_GET['point'];
		$betResult = $this -> GamePlay -> specficyBetResult($this -> RandomNumberGenerator -> getRandomNumber(), $bets, $betAmounts);
		var_dump($betResult);
		foreach ($betResult as $bet => $betAmout) {
			foreach (GamePlayComponent::$arrBetRule as $betName => $ruleValue) {
				if ($betName == $bet) {
					$betAmout = $betAmout * $ruleValue;
					echo "<br/> bet $bet Amout $betAmout ";
				}
			}
		}
		
	}

}
