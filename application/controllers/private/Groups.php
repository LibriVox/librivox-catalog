<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Groups extends Private_Controller
{

	public function index()
	{
		$this->data['page_title'] = 'Group Manager';
		$this->data['menu_header'] = $this->load->view('private/common/menu_header', $this->data, TRUE);

		$this->load->model('group_model');
		$this->data['groups'] = $this->group_model->order_by('name', 'asc')->get_all();

		foreach ($this->data['groups'] as $key => $group)
		{
			$projects = $this->group_model->get_group_details($group->id);
			$this->data['groups'][$key]->projects = $projects;
		}

		$this->insertMethodJS();

		$this->template->write_view('head', 'common/workflow_head.php', $this->data);
		$this->template->write_view('content_left', $this->base_path . '/' . build_view_path(__METHOD__), $this->data);
		$this->template->render();
	}

	public function update_group()
	{
		$fields = $this->input->post(null, true);

		$project_ids = $fields['add_project'];
		unset($fields['add_project']);

		$this->load->model('group_model');

		if ($fields['id'])
		{
			$this->group_model->update($fields['id'], $fields);
		}
		else
		{
			unset($fields['id']);
			$fields['id'] = $this->group_model->insert($fields);
		}

		if (!empty($project_ids))
		{
			$this->load->model('group_project_model');
			$project_id_array = explode(',', $project_ids);
			foreach ($project_id_array as $key => $project_id)
			{
				$this->group_project_model->insert(array('group_id' => $fields['id'], 'project_id' => $project_id));
			}
		}
	}

	public function delete_group()
	{
		$fields = $this->input->post(null, true);
		$this->load->model('group_model');
		$this->load->model('group_project_model');

		$this->group_model->delete($fields['group_id']);

		$this->group_project_model->delete_by(array('group_id' => $fields['group_id']));
	}

	public function remove_project()
	{
		$this->load->model('group_project_model');
		$fields = $this->input->post(null, true);

		$this->group_project_model->delete_by($fields);
	}
}

/* End of file groups.php */
/* Location: ./application/controllers/groups.php */