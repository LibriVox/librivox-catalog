const group_id_reader = 3; //value in db, no reason this will ever change

const mode_edit_profile = 0;
const mode_add_profile  = 1;
const mode_add_reader   = 2;

$(document).ready(function() {
	$('.profile_modal_link').on('click', function() {
		switch ($(this).attr('id'))
		{
		case 'profile_modal_link':
			get_profile(0);
			break;
		case 'addnew_modal_link':
			show_form(mode_add_profile);
			break;
		case 'add_reader_toggle':
			show_form(mode_add_reader);
			break;
		}
	});

	$('#edit_profile_submit').on('click', function() {
		$('#response_message').html('');
		edit_profile();
	});
});

function get_profile(user_id) {
	$.ajax({
		url: CI_ROOT + 'private/user/get_profile',
		type: 'post',
		data: { user_id: user_id },
		complete: function(r) {
			var response_obj = jQuery.parseJSON(r.responseText);
			if (response_obj.status == 'SUCCESS')
				show_form(mode_edit_profile, response_obj.data.user);
		},
	});
};

$.fn.enableControl = function(state) {
	if (state)
		this.removeAttr('disabled');
	else
		this.attr('disabled', 'disabled');
	return this;
}

$.fn.toggleControlAndLabel = function(state) {
	this.toggle(state);
	this.closest('label').toggle(state);
	return this;
};

function show_form(mode, user) {
	const user_add = {
		id: 0,
		username: '',
		display_name: '',
		email: '',
		website: '',
		max_projects: 0,
		user_groups: {},
		show_password: false,
		is_admin: true,
		is_mc: true
	};

	var action = 'add', title = 'Add New User', submit = 'Add User';
	var show_email = false, show_max_projects = true, show_groups = true;

	switch (mode)
	{
	case mode_add_profile:
		user = user_add;
		break;
	case mode_add_reader:
		user = user_add;
		user.user_groups[0] = { id: group_id_reader };
		title = 'Add New Reader';
		submit = 'Add Reader';
		show_max_projects = false;
		show_groups = false;
		break
	default:
		action = 'update';
		title = user.display_name;
		submit = 'Save Changes';
		show_email = true;
		show_groups = user.is_mc;
		break;
	}

	// populate values, enable/disable/show/hide controls
	$('#profile_label').html(title);
	$('#response_message').html('');
	$('#profile_form #action').val(action);
	$('#profile_form #user_id').val(user.id);
	$('#profile_form #username').val(user.username).enableControl(user.is_admin);
	$('#profile_form #display_name').val(user.display_name).enableControl(user.is_mc);
	$('#profile_form #email').val(user.email).toggleControlAndLabel(show_email);
	$('#profile_form #website').val(user.website).enableControl(user.is_mc);
	$('#profile_form #max_projects').val(user.max_projects).enableControl(user.is_mc).toggleControlAndLabel(show_max_projects);
	$('#profile_form #password_label').toggle(user.show_password);
	$('#profile_form #password').val('').toggleControlAndLabel(user.show_password);
	$('#profile_form #confirm_password').val('').toggleControlAndLabel(user.show_password);
	$('#profile_form #groups_block').toggle(show_groups);
	$('.group_box').attr('checked', false);
	jQuery.each(user.user_groups, function(i, value) {
		$('#groups_' + value.id).attr('checked', true);
	});
	$('#edit_profile_submit').html(submit);

	$('#profile_modal').modal('show');
}

function edit_profile() {
	var action = $('#profile_form #action').val();

	$.ajax({
		url: CI_ROOT + 'private/user/' + action + '_profile',
		type: 'post',
		data: $('#profile_form').serialize(),
		complete: function(r) {
			var response_obj = jQuery.parseJSON(r.responseText);

			if (response_obj.status == 'SUCCESS')
				$('#profile_modal').modal('hide');
			else
				$('#response_message').html(response_obj.data.message).show();
		},
	});
};
