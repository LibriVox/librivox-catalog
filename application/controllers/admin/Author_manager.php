<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Author_manager extends Private_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->base_path = 'admin';

		$this->load->model('author_model');

		$this->template->add_css('css/libs/datatable.css');
		$this->template->add_js('js/libs/jquery.dataTables.js');
		$this->template->add_js('js/libs/jquery.jeditable.js');
	}

	public function index($route = 'unconfirmed', $id = 0)
	{
		$this->load->helper('previewer_helper');
		$this->data['page_title'] = 'Author Manager';
		$this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);
		$this->data['author_blurb_modal'] = $this->load->view('admin/author_manager/author_blurb_modal', $this->data, TRUE);
		$this->data['author_projects_modal'] = $this->load->view('admin/author_manager/author_projects_modal', $this->data, TRUE);
		$this->data['author_pseudonyms_modal'] = $this->load->view('admin/author_manager/author_pseudonyms_modal', $this->data, TRUE);
		$this->data['author_new_modal'] = $this->load->view('admin/author_manager/author_new_modal', $this->data, TRUE);

		if ($route == 'unconfirmed')
		{
			// Default: view unconfirmed authors
			$this->data['authors'] = $this->author_model->order_by('id', 'asc')->get_many_by(array('linked_to' => '0', 'confirmed' => '0'));
			$this->data['page_title'] = 'Unconfirmed Authors | '. $this->data['page_title'];
		}
		elseif ($route == 'id')
		{
			// Individual: view author by ID
			$this->data['authors'] = array($this->author_model->get($id));
			$author = $this->data['authors'][0];
			if ($author) $this->data['page_title'] = format_author_name($author) .' | '. $this->data['page_title'];
		}
		elseif ($route == 'all')
		{
			// Old way: view all non-duplicate authors (very slow!)
			ini_set('memory_limit', '-1'); // Still a hack
			$this->data['authors'] = $this->author_model->order_by('id', 'asc')->get_many_by(array('linked_to' => '0'));
			$this->data['page_title'] = 'All Authors | '. $this->data['page_title'];
		}
		elseif ($route == 'project')
		{
			// Authors and translators by project ID
			$this->data['authors'] = array();
			$results_to_cast = array();
			$this->load->model('project_model');

			$project = $this->project_model->get($id);
			if ($project) $this->data['page_title'] = create_full_title($project) .' | '. $this->data['page_title'];

			$project_authors = $this->project_model->get_authors_by_project($id, 'author', include_sections: true);
			if ($project_authors) $results_to_cast = array_merge($results_to_cast, $project_authors);

			$project_translators = $this->project_model->get_authors_by_project($id, 'translator');
			if ($project_translators) $results_to_cast = array_merge($results_to_cast, $project_translators);

			// Hack: get_authors_by_project returns an array of *arrays*.
			// We need to cast them as objects, to match types with the db->get*() results
			foreach ($results_to_cast as $person) {
				$this->data['authors'][] = (object) $person;
			}
		}

		$this->insertMethodCSS();
		$this->insertMethodJS();

		$this->_render($this->base_path . '/' . build_view_path(__METHOD__), $this->data);
	}

	public function update_author_value()
	{
		$id = $this->input->post('id', true);
		$value = $this->input->post('value', true);

		list($field, $author_id) = explode('-', $id);

		$this->author_model->update($author_id, array($field => trim($value)));

		if ($field == 'linked_to' && $value)
		{
			// update - project_authors, sections
			$this->load->model('project_author_model');
			$this->load->model('section_model');

			$update['author_id'] = trim($value);

			//need to check for duplicate before trying update
			// duplicates are removed because of key - non-duplicates are updated to new author_id

			$project_authors = $this->project_author_model->get_many_by(array('author_id' => $author_id));
			$new_project_authors = $this->project_author_model->get_many_by(array('author_id' => $update['author_id']));

			require_once(APPPATH . 'libraries/Underscore.php');
			$project_ids = __()->pluck($project_authors, 'project_id');
			$new_project_ids = __()->pluck($new_project_authors, 'project_id');

			$exists = array_intersect($project_ids, $new_project_ids);
			//$new = array_diff($project_ids, $new_project_ids);

			//delete $exists
			if (!empty($exists))
			{
				$this->db->where(array('author_id' => $author_id))->where_in('project_id', $exists)->delete('project_authors');
				//echo $this->db->last_query();
			}

			//now we can just update what is left
			$this->project_author_model->update_by(array('author_id' => $author_id), array('author_id' => $update['author_id']));

			//nothing fancy needed here
			$this->section_model->update_by(array('author_id' => $author_id), array('author_id' => $update['author_id']));
		}

		//another cludge
		if ($field == 'blurb')
		{
			$this->ajax_output(array('value' => $value), TRUE, FALSE);
		}

		echo $value;
		return;
	}

	public function get_author_projects()
	{
		$author_id = $this->input->post('author_id', true);
		$projects = $this->author_model->get_author_projects($author_id);

		$this->ajax_output(array('projects' => $projects), TRUE, FALSE);
	}

	public function get_author_pseudonyms()
	{
		$author_id = $this->input->post('author_id', true);

		$this->load->model('author_pseudonym_model');
		$author_pseudonyms = $this->author_pseudonym_model->get_many_by(array('author_id' => $author_id));

		$this->ajax_output(array('author_pseudonyms' => $author_pseudonyms), TRUE, FALSE);
	}

	public function update_add_pseudonym()
	{
		$this->load->model('author_pseudonym_model');

		$fields = $this->input->post(null, true);
		$message = '';

		if ($fields['id'])
		{
			$this->author_pseudonym_model->update($fields['id'], $fields);
			$message = 'Updated';
		}
		else
		{
			unset($fields['id']);
			$this->author_pseudonym_model->insert($fields);
			$message = 'Added';
		}

		$this->ajax_output(array('message' => $message), TRUE, FALSE);
	}

	public function delete_pseudonym()
	{
		$this->load->model('author_pseudonym_model');

		$fields = $this->input->post(null, true);
		$message = '';

		if ($fields['id'])
		{
			$this->author_pseudonym_model->delete($fields['id']);
			$message = 'Deleted';
		}

		$this->ajax_output(array('message' => $message), (bool)$fields['id'], FALSE);
	}

	public function add_new_author()
	{
		$this->load->model('author_model');

		$fields = $this->input->post(null, true);
		$message = 'Unable to save';

		$insert_id = $this->author_model->insert($fields);

		if ($insert_id)
		{
			$message = 'Added';
		}

		$this->ajax_output(array('message' => $message), (bool)$insert_id, FALSE);
	}

	//// ********* TESTING ************/////

	function test_link_author()
	{
		$this->load->model('project_author_model');
		$this->load->model('section_model');

		$author_id = 6;
		$new_author_id = 7;

		$project_authors = $this->project_author_model->get_many_by(array('author_id' => $author_id));

		var_dump($project_authors);

		$new_project_authors = $this->project_author_model->get_many_by(array('author_id' => $new_author_id));

		var_dump($new_project_authors);

		require_once(APPPATH . 'libraries/Underscore.php');
		$project_ids = __()->pluck($project_authors, 'project_id');
		$new_project_ids = __()->pluck($new_project_authors, 'project_id');

		var_dump($project_ids);
		var_dump($new_project_ids);

		$exists = array_intersect($project_ids, $new_project_ids);
		$new = array_diff($project_ids, $new_project_ids);

		var_dump($exists);
		var_dump($new);

		//delete $exists
		if (!empty($exists))
		{
			$this->db->where(array('author_id' => $author_id))->where_in('project_id', $exists)->delete('project_authors');
			echo $this->db->last_query();
		}

		//now we can just update what is left
		$this->project_author_model->update_by(array('author_id' => $author_id), array('author_id' => $new_author_id));
	}

	//INSERT INTO `librivox_catalog_new`.`project_authors` (`project_id`, `author_id`, `type`) VALUES ('3314', '7', 'author'), ('4223', '7', 'author');
}

/* End of file author_cleanup.php */
/* Location: ./application/controllers/author_cleanup.php */
