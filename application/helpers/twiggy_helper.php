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

function create_date_range($strDateFrom, $strDateTo) {
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

?>