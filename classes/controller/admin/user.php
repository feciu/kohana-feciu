<?php defined('SYSPATH') OR die('No direct access allowed.');


class Controller_Admin_User extends Controller_Admin {

	protected $model	 		= 'user';
	protected $model_friendly_name          = 'User';
// colums in database 'id' => 'Id', 'username' => 'Username', 'password' => 'Password', 'email' => 'Email', 'logins' => 'Logins', 'last_login' => 'Last_login', 
	protected $listing_display_keys         = array('id' => 'Id', 
							'username' => 'Username', 
							'password' => 'Password', 
							'email' => 'Email', 
							);
        protected $listing_ordering_keys        = array();
        protected $action_key                   = array('Delete' => array('alt'=>'delete','onclick'=>'return confirm_delete("")'),
                                                        'Edit'  =>  array('alt'=>'edit'),
                                                  );

}