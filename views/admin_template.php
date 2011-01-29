<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Title</title>
<?php foreach ($styles as $style => $media) echo HTML::style($style, array('media' => $media)), "\n" ?>
<?php foreach($scripts as $file) { echo HTML::script($file, NULL, TRUE), "\n"; }?>
</head>

<body>
	<div id="admin_header_navbar">
            <div id="admin_header_title"><a href="<?php echo URL::base().Kohana::config('feciu_conf.url_base')?>">Administration area</a></div>
                <div id="admin_user_info"><a href="<?php echo URL::base().Kohana::config('feciu_conf.url_base')?>logout">logout</a></div>
		<br class="clear"/>
	</div>
	<div id="main_panel">
		<div id="left_panel">
                   <?php $contents['content'] = array();?>
                    <?php foreach($menu as $key =>$item){
                        
                        if($item->main){
                            if(strpos($_SERVER['REQUEST_URI'], $item->name)){
                                $contents['class'][$item->id] = 'hide_menu';
                            }else{
                                $contents['class'][$item->id] = 'hide_menu hide';
                            }
                                    $contents['content'][$item->id] = '<li class="first sidebar_header last"><a>'.$item->label.'</a></li>';
                                    $contents['content'][$item->id] .= '<li class="'.$contents['class'][$item->id].'"><a href="'.url::base().Kohana::config('feciu_conf.url_base').$item->name.'">'.$item->label.'</a></li>';
                                    
                               
                        }else{
                            $contents['content'][$item->adminpanel->id] .= '<li class="'.$contents['class'][$item->adminpanel->id].'"><a href="'.url::base().Kohana::config('feciu_conf.url_base').$item->name.'">'.$item->label.'</a></li>';
                        }
                        
                    }
                    foreach($contents['content'] as $item){
                        echo '<ul>'.$item.'</ul>';
                    }
                    
                    ?>
                    <?php if(Session::instance()->get('useradmin') == '1'):?>
                    <ul>
                        <li class="first sidebar_header1"><a href="#">Admin panel</a></li>
                        <li class=""><a href="<?php echo url::base().'admin/role'?>">role</a></li>
                        <li class=""><a href="<?php echo url::base().'admin/user'?>">users</a></li>
                        <li class="last"><a href="<?php echo url::base().'admin/panel'?>">models</a></li>
                    </ul>
                   <?php endif;?>
		

		</div>

		<div id="right_panel">
			<div id="main_content">

			<?php
			// show flash_vars() if available
			if(!is_null($error_message = Session::instance()->get_once('admin_message_error', null))) {
				echo '<p class="admin_message error">'.$error_message.'</p><br />';
			}
			// show flash_vars() if available
			if(!is_null($warning_message = Session::instance()->get_once('admin_message_warning', null))) {
				echo '<p class="admin_message warning">'.$warning_message.'</p><br />';
			}
			// show flash_vars() if available
			if(!is_null($success_message = Session::instance()->get_once('admin_message_success', null))) {
				echo '<p class="admin_message success">'.$success_message.'</p><br />';
			}
			?>


			<?php echo $content ?>
                            
                        
			</div>
		</div>
		<br class="clear" />
</div>
</body>
</html>