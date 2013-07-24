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

		if (IS_DEBUG == true) {
			$bets = isset($_GET['bet']) ? $_GET['bet'] : '';
			$betAmounts = isset($_GET['point']) ? $_GET['point'] : '';
		} else {
			$bets = isset($_POST['bet']) ? $_POST['bet'] : '';
			$betAmounts = isset($_POST['point']) ? $_POST['point'] : '';
		}

		$task_title = 'res_bet';
		$message = 'bet error';
		$status = false;

		$response = array();
		if (!$this -> Session -> check('login')) {
			$message = 'Please login first';
			$response['data']['message'] = $message;
		} else {

			if ($bets != null && count($bets) > 0 && $betAmounts != null && count($betAmounts) > 0) {
				//Get balance
				$balance = $this -> User -> getUserBalance($this -> Session -> read('login'));
				//Get Dices
				$dices = $this -> RandomNumberGenerator -> getRandomNumber();
				//Specify bet result
				$betResult = $this -> GamePlay -> specficyBetResult($dices, $bets, $betAmounts);
				//Specify win pattern and
				$winPattern = array();

				$totalBet = 0;
				//Degree balance with bet amounts first
				foreach ($betAmounts as $betAmout) {
					$balance -= $betAmout;
				}

				$totalGot = 0;
				//calculate win amount
				$newBalance = $balance;
				foreach ($betResult as $bet => $betAmout) {
					foreach (GamePlayComponent::$arrBetRule as $betName => $ruleValue) {
						if ($betName == $bet) {
							if ($betName == 'single-1' || $betName == 'single-2' || $betName == 'single-3' || $betName == 'single-4' || $betName == 'single-5' || $betName == 'single-6') {
								$newBalance += $betAmout;
								$winPattern = array_merge($winPattern, array($bet));
							} else {
								$newBalance += $betAmout + ($betAmout * $ruleValue);
								$winPattern = array_merge($winPattern, array($bet));
							}

						}
					}
				}
				$totalWin = $newBalance - $balance;
				$balance = $newBalance;
				//Update balance to db
				$result = $this -> User -> updateUserBalance($this -> Session -> read('login'), $balance);
				//var_dump($betResult);
				$response['data']['success'] = true;
				$result = array();
				$result['dice'] = $dices;
				$result['win'] = $winPattern;
				$result['point']['win'] = $totalWin;
				$result['point']['current'] = $balance;
				$response['data']['result'] = $result;
				/*
				$response['data']['result'] = $dices;
				$response['data']['win'] = $winPattern;
				$response['data']['point']['win'] = count($winPattern);
				$response['data']['point']['current'] = $balance;
				 * */
				 
				$status = true;
			} else {
				$message = 'bet error';
				$response['data']['message'] = $message;
			}
		}

		// Process Json result
		$response['task_title'] = $task_title;
		$response['status'] = $status;
		echo json_encode(array('response' => $response));
	}

	public function logout()
	{
		$this->Session->delete('login');
		
		$response = array();
		$response['task_title'] = 'res_log_out';
		$response['data']['message'] = 'Logout success';
		$response['status'] = true;
		echo json_encode(array('response' => $response));
	}
}
