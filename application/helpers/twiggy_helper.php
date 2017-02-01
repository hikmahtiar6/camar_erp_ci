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

?>