<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Genre_manager extends Private_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->base_path = 'admin';

		$this->load->library('Mahana_hierarchy');
		$this->load->model('genre_model');
	}

	public function index()
	{
		$this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

		$this->data['genres'] = $this->mahana_hierarchy->get_sorted_children();

		$this->data['genre_dropdown'][0] = 'Top Level';
		foreach ($this->data['genres'] as $genre)
		{
			$depth_mark = $genre['deep'] ? str_repeat('&nbsp;&nbsp;&nbsp;', $genre['deep']) : '';
			$this->data['genre_dropdown'][$genre['id']] = $depth_mark . $genre['name'];
		}

		$this->load->helper('form');

		$this->insertMethodJS();

		$this->_render($this->base_path . '/' . build_view_path(__METHOD__), $this->data);
	}

	public function update_genre()
	{
		$fields = $this->input->post(null, true);

		if ($fields['id'])
		{

			// In case we're changing our `parent_id`, be sure we don't become our own ancestor!
			$ancestors = $this->mahana_hierarchy->get_ancestors($fields['parent_id']);
			foreach ($ancestors as $ancestor)
			{
				if ($ancestor['id'] == $fields['id'])
				{
					$this->ajax_output(array('message' => 'Cannot set item as its own ancestor'), FALSE, FALSE);
					return;
				}
			}
			unset($ancestors);

			$this->genre_model->update($fields['id'], $fields);
		}
		else
		{
			$this->genre_model->insert($fields);
		}

		$this->mahana_hierarchy->resync();

		$this->ajax_output(array('fields' => $fields), TRUE, FALSE);
	}

}

/* End of file genre_manager.php */
/* Location: ./application/controllers/genre_manager.php */