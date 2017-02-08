<?php

function dump($dumping)
{
	return var_dump($dumping);
}

function arr_push($arr, $data)
{
	return array_push($arr, $data);
}

function arr_sum($arr)
{
	return array_sum($arr);
}

function get_shift_end($shift_start, $apt, $target_prod)
{
	if($apt != '' || $apt != NULL)
	{
		return date('H:i:s', strtotime($shift_start) + time($apt * $target_prod));
	}
	else
	{
		return date('H:i:s', strtotime($shift_start) + time($target_prod));
	}
}

function same($word1, $word2)
{
	if($word1 == $word2)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function create_date_range($strDateFrom, $strDateTo) 
{
	$aryRange=array();

	$iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
	$iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

	if ($iDateTo>=$iDateFrom)
	{
	    array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
	    while ($iDateFrom<$iDateTo)
	    {
	        $iDateFrom+=86400; // add 24 hours
	        array_push($aryRange,date('Y-m-d',$iDateFrom));
	    }
	}
	return $aryRange;
}

function convert_dice($dice)
{
	$dice_txt = ($dice == null) ? '' : $dice;
	
	$txt = '';
	$expl = explode(",", $dice_txt);

	if(count($expl) > 0)
	{
		foreach($expl as $rexpl)
		{
			if($rexpl != '' || $rexpl != null)
			{
				$txt .= $rexpl.', ';
			}
		}
	}
	else
	{
		$txt = $dice_txt;
	}

	return rtrim($txt, ', ');
}

function count_dice($dice)
{
	$arr = array();
	$expl = preg_split('@,@', substr($dice, 1, 1000000000000000000000), NULL, PREG_SPLIT_NO_EMPTY);
	//$expl = explode(",", substr($dice, 1, 1000000000000000000000));

	return count($expl);
}

function get_date_start_header($header_id, $other)
{
	$ci =& get_instance();

	$ci->load->model('master/detail_model');

	$data = $ci->detail_model->get_date_start($header_id);
	if($data->date_start_header != NULL)
	{
		return date('d-m-Y', strtotime($data->date_start_header));
	}

	return date('d-m-Y', strtotime($other));
}

function get_date_finish_header($header_id, $other)
{
	$ci =& get_instance();

	$ci->load->model('master/detail_model');

	$data = $ci->detail_model->get_date_finish($header_id);
	if($data->date_finish_header != NULL)
	{
		return date('d-m-Y', strtotime($data->date_finish_header));
	}

	return date('d-m-Y', strtotime($other));
}

function date_to_time($date)
{
	return strtotime($date);
}

function week_in_year() {
	
	$dt = [];
	$year           = date('Y');
	$firstDayOfYear = mktime(0, 0, 0, 1, 1, $year);
	$nextMonday     = strtotime('monday', $firstDayOfYear);
	$nextSunday     = strtotime('sunday', $nextMonday);
	
	while (date('Y', $nextMonday) == $year) {
		$dt[] =  array(
			'date_start'  => date('d/m/Y', $nextMonday),
			'date_finish' => date('d/m/Y', $nextSunday)
		);
	
		$nextMonday = strtotime('+1 week', $nextMonday);
		$nextSunday = strtotime('+1 week', $nextSunday);
		
	}
	
	return $dt;	
}

?>