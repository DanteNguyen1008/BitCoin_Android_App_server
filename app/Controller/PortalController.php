<?php
App::import('Controller', 'Users');

class PortalController extends AppController{
	
	public function index()
	{
		
	}
	
	public function signup() {
		
		// Get request data
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
		
		$user = new UsersController();
		echo $user->register($email, $password, $fullname, $phonenumber, $address);
		
	}
	
	public function login() {
	    
	    // Get request data
	    if (IS_DEBUG == true) {
	        $email = isset($_GET['email']) ? $_GET['email'] : '';
		    $password = isset($_GET['password']) ? $_GET['password'] : '';
	    } else {
	        $email = isset($_POST['email']) ? $_POST['email'] : '';
		    $password = isset($_POST['password']) ? $_POST['password'] : '';
	    }
		
	    $user = new UsersController();
	    echo $user->login($email, $password);
	}
	
	public function changepassword() {
	    
	    // Get request data
	    if (IS_DEBUG == true) {
	        $old_password = isset($_GET['old_password']) ? $_GET['old_password'] : '';
		    $new_password = isset($_GET['new_password']) ? $_GET['new_password'] : '';
	    } else {
	        $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
		    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
	    }
	    
	    $user = new UsersController();
		echo $user->changepassword($old_password, $new_password);
	}
}
