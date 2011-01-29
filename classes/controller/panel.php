<?php defined('SYSPATH') OR die('No direct access allowed.');


class Controller_Panel extends Controller_Template {


    public $template    = 'admin_template';
    public $fields      = array('id','name');

    protected $listing_display_keys	= array('name'=>'Name');
    protected $listing_ordering_keys	= array('id');
    protected $model_friendly_name      = 'Models';
    protected $model                    = '';
    protected $action_key                   = array('Active'=> array('alt'=>'accept'),
                                                    'Build' => array('alt'=>'database_gear'),
                                                    'Delete' => array('alt'=>'delete','onclick'=>'return confirm_delete("")'),
                                                    'Edit' => array('alt'=>'edit'),
                                                    );

    protected $items_per_page           = 10;
    




    public function action_index(){

        $menu = Jelly::select('adminpanel')->execute();
        $this->template->menu  = $menu;

        $list = Kohana::list_files('feciu/classes/controller/admin', array(MODPATH));
        foreach ($list as $model => $path)
	{
		// Clean up the model name, and make it relative to the model folder
		$model = trim(str_replace(array('feciu/classes/controller/admin', EXT), '', $model), DIRECTORY_SEPARATOR);
                $contoler_name[] = trim(str_replace(array('/', EXT), '', $model), "\\");
        }
        foreach($menu as $item){
            if(in_array($item->name, $contoler_name)){
                Jelly::select('adminpanel',$item->id)->set('show',1)->save();
            }else{
                Jelly::select('adminpanel',$item->id)->set('show',0)->save();
            }
        }
        $menu = Jelly::select('adminpanel')->execute();
        $content = 'role';
        //pagination
        $count = count($menu);
        $pagination = Pagination::factory(array(
			'total_items'    => $count,
			'items_per_page' => $this->items_per_page
		));
        $view_list = View::factory('_crud_listing_panel');
        $view_list->listing_display_keys	= $this->listing_display_keys;
	$view_list->listing_ordering_keys	= $this->listing_ordering_keys;
        $view_list->dataset                     = $menu;
        $view_list->dataset_conf                = $menu;
        $view_list->active_controler            = $contoler_name;
        $view_list->action_key                  = $this->action_key;
        $view_list->model_friendly_name         = $this->model_friendly_name;



//        s->limit( $pagination->items_per_page )
//			->offset( $pagination->offset )

        //listing template
        $view_content = View::factory('listing');
        $view_content->model_friendly_name  = $this->model_friendly_name;
        $view_content->total_results        = $count;
        $view_content->pagination           = $pagination;
        $view_content->list                 = $view_list;
        $view_content->add_new              = true;



        $this->template->content = $view_content;

    }


    function action_add(){
        
       $menu = Jelly::select('adminpanel')->execute();
       $this->template->menu  = $menu;

       $form = Jelly::factory('adminpanel');
       $form->form->add('add', 'submit','Add new '.$this->model_friendly_name);
       
       if($form->load($_POST)->validate()){
           $form->save();
           if($this->bulid_model_file($_POST['form']['name'])){
               Session::instance()->set('admin_message_success','Model'. $_POST['form']['name'].' add');
               $this->request->redirect('admin/panel');
           }
       }
       
       $data['form'] = $form->form;
       $view_add = View::factory('formo/html/form',$data);

       //listing template
        $view_content = View::factory('edit');
        $view_content->model_friendly_name  = $this->model_friendly_name;
        $view_content->total_results        = '';
        $view_content->pagination           = '';
        $view_content->form                 = $view_add;
        $view_content->add_new              = true;


       $this->template->content = $view_content;
   }
   function action_edit($id){
        $menu = Jelly::select('adminpanel')->execute();
        $this->template->menu  = $menu;
       

       $form = Jelly::select('adminpanel', $id);

       $form->form->add('edit', 'submit','Save '.$this->model_friendly_name .' (return to list)')
                  ->add('edit_cont', 'submit','Save '.$this->model_friendly_name .' (continue editing)');


       if($form->form->load($_POST)->validate()){
            if($form->form->validate()){
                $form->save();
                if(isset ($_POST['form']['edit'])){
                    Session::instance()->set('admin_message_success', $this->model_friendly_name.' saved successfully');
                    $this->request->redirect( 'admin/panel' );
                }
                if(isset ($_POST['form']['edit_cont'])){
                    $this->request->redirect( 'admin/panel/edit/'.$id );
                }

            }
       }


       $data['form'] = $form->form;
       $view_add = View::factory('formo/html/form',$data);

       //listing template
        $view_content = View::factory('edit');
        $view_content->model_friendly_name  = $this->model_friendly_name;
        $view_content->total_results        = '';
        $view_content->pagination           = '';
        $view_content->form                 = $view_add;
        $view_content->edit                 = TRUE;



       $this->template->content = $view_content;
   }


   public function action_build($id){
       $model = Jelly::select('adminpanel',$id);
        echo   $class_name = $model->name;
       $migrate = Migration::factory($class_name,'jelly')->sync();
       $table_item = count($migrate->_tables)-1;
       $table = $migrate->_tables[$table_item]->name;


       $this->bulid_controler_file($class_name,$table);
       $this->bulit_config();
       Session::instance()->set('admin_message_success', $class_name.' bulit');
       $this->request->redirect('admin/panel');
   }

   public function action_rebuild($id){
       $model = Jelly::select('adminpanel',$id);
      $class_name = $model->name;
       $migrate = Migration::factory($class_name,'jelly')->sync();
       $table_item = count($migrate->_tables)-1;
       $table = $migrate->_tables[$table_item]->name;



       $this->bulid_controler_file($class_name,$table);
//       $this->bulit_config();
       Session::instance()->set('admin_message_success', $class_name.' rebulit');
       $this->request->redirect('admin/panel');
   }
   public function action_a(){
       $migrate = Migration::factory('adminpanel','jelly')->sync();
   }
   public function action_delete($id){
       
       $model = Jelly::select('adminpanel',$id);
       $class_name = $model->name;
       $migate = Migration::factory($class_name,'jelly')->remove();

       
       $model_path = MODPATH.'feciu'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR;
       $model_file = $model_path.$class_name.EXT;
       if(file_exists($model_file)){
            unlink($model_file);
       }
       
       $controller_path = MODPATH.'feciu'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR;
       $controller_file = $controller_path.$class_name.EXT;
       if(file_exists($controller_file)){
            unlink($controller_file);
       }
       $model->delete();
       $this->bulit_config();
       Session::instance()->set('admin_message_success', 'Model '.$class_name.' removed');
       $this->request->redirect('admin/panel');

   }
   public function before() {
        parent::before();

        $this->auth = Auth::instance();

        if(!$this->auth->logged_in()) {
            if ($_POST) {
                // See if user checked 'Stay logged in'
                $remember = isset($_POST['rememberme']) ? TRUE : FALSE;

                // Try to log the user in
                if (! $this->auth->login($_POST['login'], $_POST['password'], $remember)) {
                    // There was a problem logging in
                    $error = TRUE;
                }

                // Redirect to the index page if the user was logged in successfully
                if ($this->auth->logged_in()) {
                    $this->request->redirect('admin');
                }else{
                    $this->template = View::factory('login');
                }

            }else {
                $this->template = View::factory('login');
            }
        }

    }
     public function after()
	{
		if ($this->auto_render)
		{

			$this->template->styles = array( 'assets/css/admin.css'  => 'screen');
                        $this->template->scripts = array(   'assets/js/jquery-1.3.2.min.js',
                                                             'assets/js/admin.js',);
		}

		return parent::after();
	}


     private function bulid_model_file($class_name){

        $class_name = strtolower($class_name);
        $path = MODPATH.'feciu'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR;
        $file = $path.$class_name.EXT;
        file_put_contents($file, $this->content_model($class_name));
        return true;
        //mkdir($path, 0777, TRUE);
     }

     private function bulid_controler_file($class_name,$table){

        $class_name = strtolower($class_name);
        $path = MODPATH.'feciu'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR;
        $file = $path.$class_name.EXT;
        file_put_contents($file, $this->content_controller($class_name,$table));
        return true;
     }

     private function bulit_config(){
        $path = MODPATH.'feciu'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR;
        $file = $path.'feciu'.EXT;
        file_put_contents($file, $this->content_config());
        return true;
     }


     private function content_model($class_name){
         $content = '<?php
class Model_'.ucfirst($class_name).' extends Jelly_Model
{
    public static function initialize(Jelly_Meta $meta)
    {
         $meta->fields += array(\'id\' => new Field_Primary(array(
                                    \'render\' => FALSE,
                                    )),
                                \'name\' => new Field_String(array(
                                    \'rules\' => array(\'not_empty\' => array(TRUE)),
                                )),
                                \'slug\' => new Field_Slug(array(
                                    \'render\' => FALSE,
                                )),
                         );
    }
}';
         return $content;

     }

     private function content_controller($class_name,$table){

         $columns = Database::instance()->list_columns($table);
         
         $content = "<?php defined('SYSPATH') OR die('No direct access allowed.');


class Controller_Admin_".ucfirst($class_name)." extends Controller_Admin {

	protected \$model	 		= '$class_name';
	protected \$model_friendly_name          = '".ucfirst($class_name)."';";

        $content .= "\n// colums in database ";
        foreach($columns as $key => $item){
             $content .= "'$key' => '".ucfirst($key)."', ";
        }
        $content .= "\n\t";
	$content .="protected \$listing_display_keys         = array(";

        foreach($columns as $key => $item){
             $content .= "'$key' => '".ucfirst($key)."', \n\t\t\t\t\t\t\t";
        }
         
	$content .= ");
        protected \$listing_ordering_keys        = array('name');
        protected \$action_key                   = array('Delete' => array('alt'=>'delete','onclick'=>'return confirm_delete(\"\")'),
                                                        'Edit'  =>  array('alt'=>'edit'),
                                                  );

}";
         return $content;

     }

     private function content_config(){
         $model_name = array();
         $list = Kohana::list_files('feciu/classes/controller/admin', array(MODPATH));
         foreach ($list as $model => $path)
	{
		// Clean up the model name, and make it relative to the model folder
		$model = trim(str_replace(array('feciu/classes/controller/admin', EXT), '', $model), DIRECTORY_SEPARATOR);
                $model_name[] = trim(str_replace(array('/', EXT), '', $model), "\\");
        }

         $content = "<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
    // array( name => url )
    'site' => array(\n\t";

        foreach($model_name as $item){
            $content .= "'".ucfirst($item)."' => '$item', \n\t";

        }

        $content .="),
);
";
        return $content;
     }
}