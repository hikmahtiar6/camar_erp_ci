<?php
/**
 * Helper for Date
 *
 * @author  HIkmahtiar <hikmahtiar.cool@gmail.com>
 */

function change_format_date($date, $format = 'Y-m-d', $replace = "/", $changeTo = '-')
{
	$date_format = str_replace($replace, $changeTo, $date);
	$to_time = strtotime($date_format);

	return date($format, $to_time);
}

/**
 * DAte now
 */
function date_now()
{
	return date('Y-m-d');
}

/**
 * get week number
 */
function get_week($date = '')
{
	if($date == '')
	{
		$date = date('Y-m-d');
	}

	return date('W', strtotime($date));
}
?>