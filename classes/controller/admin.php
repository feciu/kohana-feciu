<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Kohana API for ORM models
 *
 * @package    Kocrud
 * @category   Controllers
 * @author     Birkir R Gudjonsson (birkir.gudjonsson@gmail.com)
 */
class Controller_Admin extends Controller_Template {

    public $auth;

    public $template    = 'admin_template';
    public $fields      = array('id','name');

    protected $listing_display_keys	= array('id' => 'ID');
    protected $listing_ordering_keys	= array();
    protected $model_friendly_name      = '';
    protected $model                    = '';
    protected $action_key               =array();
    protected $items_per_page           =10;


    function action_index() {
        $this->action_listing();
    }


    function action_listing() {


        $menu = Jelly::select('adminpanel')->execute();
        $this->template->menu  = $menu;

        $content = '';
        if(! $this->model) {
            if( ! Kohana::find_file('classes/model', $content)) {
                $this->template->content = '';
                $this->template->menu = $menu;
                Session::instance()->set('admin_message_error', 'Brak modeli');
                return true;
            }
            $content = strtolower(key($menu));
            $this->request->redirect('admin/'.$content);
        }else {
            $content = $this->model;
        }




        //pagination
        $count = Jelly::select($content)->count();

        $pagination = Pagination::factory(array(
                'total_items'    => $count,
                'items_per_page' => $this->items_per_page
        ));
        //print_r($_GET);
        $list = Jelly::select($content)->limit($pagination->items_per_page)->offset($pagination->offset)->execute();

        $view_list = View::factory('_crud_listing');
        $view_list->listing_display_keys	= $this->listing_display_keys;
        $view_list->listing_ordering_keys	= $this->listing_ordering_keys;
        $view_list->dataset                     = $list;
        $view_list->action_key                  = $this->action_key;
        $view_list->model_friendly_name         = $this->model_friendly_name;


//        s->limit( $pagination->items_per_page )
//			->offset( $pagination->offset )

        //listing template
        $view_content = View::factory('listing');
        $view_content->model_friendly_name  = $this->model_friendly_name;
        $view_content->total_results        = $list->count();
        $view_content->pagination           = $pagination;
        $view_content->list                 = $view_list;



        $this->template->content = $view_content;

    }

    function action_add() {
        $menu = Jelly::select('adminpanel')->execute();
        $this->template->menu  = $menu;

        $form = Jelly::factory($this->model);
        //$form->uzytkownik = Jelly::select('uzytkownik',1);
        $form->form->add('add', 'submit','Add new '.$this->model_friendly_name);
//			->add('_add_edit' , 'submit','Add new '.$this->model_friendly_name.' and edit');

        if(isset($_POST['form']['name']))$_POST['form']['slug'] = $_POST['form']['name'];

        if($form->form->load($_POST)->validate()) {
            if($form->form->validate()) {
                $form->save();
                if(isset ($_POST['form']['add'])) {
                    Session::instance()->set('admin_message_success', $this->model_friendly_name.' created successfully');
                    $this->request->redirect( 'admin/'.$this->model );
                }
                if(isset ($_POST['form']['_add_edit'])) {
                    $this->request->redirect( 'admin/'.$this->model.'/edit/'.$id );
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


        $this->template->content = $view_content;
    }

    function action_edit($id) {
        $menu = Jelly::select('adminpanel')->execute();
        $this->template->menu  = $menu;


        $form = Jelly::select($this->model, $id);

        $form->form->add('edit', 'submit','Save '.$this->model_friendly_name .' (return to list)')
                ->add('edit_cont', 'submit','Save '.$this->model_friendly_name .' (continue editing)');
        if(isset($_POST['form']['name']))$_POST['form']['slug'] = $_POST['form']['name'];

        if($form->form->load($_POST)->validate()) {
            if($form->form->validate()) {
                $form->save();
                if(isset ($_POST['form']['edit'])) {
                    Session::instance()->set('admin_message_success', $this->model_friendly_name.' saved successfully');
                    $this->request->redirect( 'admin/'.$this->model );
                }
                if(isset ($_POST['form']['edit_cont'])) {
                    $this->request->redirect( 'admin/'.$this->model.'/edit/'.$id );
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

    function action_delete($id) {
        $del = Jelly::select($this->model)->load($id);
        $del->delete();
        Session::instance()->set('admin_message_success', $this->model_friendly_name.' delete successfully');
        $this->request->redirect( 'admin/'.$this->model );
    }

    function action_logout() {
        $auth = Auth::instance();
        Session::instance()->delete('useradmin');
        $auth->logout();
        $this->request->redirect('admin');
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
                }else{
                    if($_POST['login'] == 'admin'){
                        Session::instance()->set('useradmin', '1');
                    }
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
    public function after() {
        if ($this->auto_render) {
            $this->template->styles = array('assets/css/admin.css'  => 'screen');
        }
        //$this->template = View::factory('login');
        $this->template->styles = array('assets/css/admin.css'  => 'screen');
        $this->template->scripts = array(   'assets/js/jquery-1.3.2.min.js',
                'assets/js/admin.js',
                'assets/ckeditor/ckeditor.js',);
        return parent::after();
    }


} // End

