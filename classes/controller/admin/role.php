<?php defined('SYSPATH') OR die('No direct access allowed.');


class Controller_Admin_Role extends Controller_Admin {

	protected $model	 		= 'role';
	protected $model_friendly_name          = 'Role';
// colums in database 'id' => 'Id', 'name' => 'Name', 'description' => 'Description', 
	protected $listing_display_keys         = array('id' => 'Id',
							'name' => 'Name', 
							'description' => 'Description', 
							);
        protected $listing_ordering_keys        = array();
        protected $action_key                   = array('Delete' => array('alt'=>'delete','onclick'=>'return confirm_delete("")'),
                                                        'Edit'  =>  array('alt'=>'edit'),
                                                  );

}