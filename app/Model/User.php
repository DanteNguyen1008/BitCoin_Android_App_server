<?php
class User extends AppModel {
	public $name = 'users';

	public function getAllUser() {
		$userObj = $this -> find('all');
		return $userObj;
	}

	public function getUserByLogin($email = '', $password = '') {
		if ($email != '' && $password != '') {
			$user = $this -> find('list', array('conditions' => array('User.email' => $email, 'User.password' => $password)));
			return $user;
		}
		return null;
	}

	public function getUserBalance($email = '') {
		if ($email != '') {
			$balance = $this -> find('first', array('conditions' => array('User.email' => $email), 'fields' => array('balance')));
			return $balance['User']['balance'];
		}
		
		return null;
	}
	
	public function updateUserBalance($email = '', $balance = -1)
	{
		if($email != '' && $balance > -1)
		{
			$result = $this->updateAll(array('User.balance' => $balance),array('User.email = ' => $email));
			return $result;
		}
		
		return 0;
	}

	public function insertUser($userData) {
		$this -> create($userData);
		$this -> save($userData);

		return $this -> getLastInsertID();
	}

	public function loginSelect($data) {
		return $this -> find('first', array('conditions' => array('User.email' => $data['User']['email'], 'User.password' => $data['User']['password'])));
	}

	public function isExist($data) {
		return $this -> find('first', array('conditions' => array('User.email' => $data['User']['email'])));
	}

}
