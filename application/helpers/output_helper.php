<?php
/**
 * Output Helper
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */

/**
 * @return JSON
 */
function output_json($json)
{
	$ci =& get_instance();

	return $ci->output->set_output(json_encode($json));
}