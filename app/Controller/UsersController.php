<?php
class UsersController extends AppController{
	public function index()
	{
		$userData = $this->User->getUserByLogin('a1provip002@gmail.com','123456');
		var_dump($userData);
	}
}
