<?php
/**
 * global user
 */
function get_user($show = '')
{
	$ci =& get_instance();
	$ci->load->model('login_model');

	$user = $ci->login_model->get_user_by_session_id();
	if($user)
	{
		if($show != '')
		{
			return $user[$show];
		}

		return $user;
	}
	else
	{
		return '';
	}
}
