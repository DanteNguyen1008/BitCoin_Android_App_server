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
	
	public function insertUser($userData)
	{
		
	}

}
