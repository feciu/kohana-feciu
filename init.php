<?php defined('SYSPATH') or die('No direct script access.');

$path = Kohana::config('feciu.site');
foreach($path as $item => $url){
    $url = strtolower($item);
    Route::set('admin/'.$url , 'admin/'.$url.'(/<action>((/<column>)(/<id>)))')
	->defaults(array(
		'directory'  => 'admin',
		'controller' => $url,
		'action'     => '',
	));
}

Route::set('admin/panel', 'admin/panel(/<action>((/<column>)(/<id>)))')
	->defaults(array(
//                'directory'  => 'admin',
		'controller' => 'panel',
		'action'     => 'index',
	));


Route::set('admin', 'admin(/<action>((/<column>)(/<id>)))')
	->defaults(array(
		'controller' => 'admin',
		'action'     => 'index',
	));



