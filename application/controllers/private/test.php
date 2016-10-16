<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller {

   function reload_genres()
   {
      $this->load->library('mahana_hierarchy');

      $this->mahana_hierarchy->resync();

   }

   public function get_ancestors()
   {
      //$this->load->library('mahana_hierarchy');

      //$ancestors = $this->mahana_hierarchy->get_ancestors(6);

     // var_dump($ancestors);

      $this->load->helper('previewer_helper');

      $ancestors = "6, 14";

      $genres_array = explode(',',$ancestors);
      foreach ($genres_array as $key => $genre) {
         $genre_strings[]     = build_genre_element($genre);
      } 

      var_dump($genre_strings);   

   }

   public function test_user_session()
   {

      $user = $this->session->all_userdata();
      var_dump($user);

      var_dump($this->ion_auth->is_admin());

      var_dump($this->ion_auth->in_group('frogs', $user['user_id']));

      var_dump($this->ion_auth->in_group('reader', $user['user_id']));

      var_dump($this->librivox_auth->get_user_id());
   }

   public function test_something()
   {

   	echo 'TEST Controller';


   	$this->load->model('form_generator_model');
   	$project_launch_data = $this->form_generator_model->get_by('project_code', $this->input->post('project_code', true));

   	var_dump($project_launch_data);


   	$this->load->model('project_model');
   	$projects = $this->project_model->get_by('ProjectID', 6698);

   	var_dump($projects);

   	   	$this->load->model('form_generator_model');
   	$project_launch_data = $this->form_generator_model->get_by('project_code', $this->input->post('project_code', true));

   	var_dump($project_launch_data);
   }

   public function test_genres()
   {
      $config['table'] = 'genres';
      $this->load->library('mahana_hierarchy', $config);

      //$this->mahana_hierarchy->resync();

      $genres = $this->mahana_hierarchy->get_grouped_children();//$this->mahana_hierarchy->get();

      $this->data['genres'] = $genres; 

      //$test = $this->mahana_hierarchy->get_grouped_children();
      //var_dump($test);

      $this->load->view('test/test_genres', $this->data);
   }

   public function test_slug()
   {

         $config = array(
             'field' => 'url_librivox',
             'title' => 'title',
             'table' => 'projects',
             'id' => 'id',
         );
         $this->load->library('slug', $config);

         $project_info['title_prefix'] = 'The';
         $project_info['title']        = 'Art of War by Sun Tzu';

         $data = array(
             'title' =>  trim($project_info['title_prefix']. ' '. $project_info['title']),
         );
         $project_info['url_librivox'] =  $this->slug->create_uri($data);

         echo '<br />'. $project_info['url_librivox'];

   }

}

/* End of file Controllername.php */
/* Location: ./application/controllers/Controllername.php */