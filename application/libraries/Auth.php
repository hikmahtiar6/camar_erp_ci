<?php
class Auth
{
	public function _is_authentication()
	{
		$ci =& get_instance();
		if(!$ci->session->userdata('user_id'))
		{
			redirect('login');
		}
	}

}

?>