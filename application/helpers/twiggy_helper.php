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

function convert_dice2($dice)
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
				if(strpos($rexpl, '.') !== false) {
				  // explodable
					$expl2 = explode(".", $rexpl);
					$txt .= $expl2[0].'.'.$expl2[1].', ';
				} else {
				  // not explodable
					$txt .= $rexpl.', ';
				}
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
	$expl = preg_split('/,/', $dice, NULL, PREG_SPLIT_NO_EMPTY);
	//$expl = explode(",", substr($dice, 1, 1000000000000000000000));

	return count($expl);
}

function get_date_start_header($header_id, $show, $other)
{
	$ci =& get_instance();
	$ci->load->model('master/detail_model');

	$result = $other;

	$data = $ci->detail_model->get_date_start($header_id);
	if($data->date_start_header != NULL)
	{
		$result = $data->date_start_header; 
	}

	if($show != '')
	{
		$result = str_replace("/", "-", $show); 
	}

	return date('d-m-Y', strtotime($result));
}

function get_date_finish_header($header_id, $show, $other)
{
	$ci =& get_instance();
	$ci->load->model('master/detail_model');

	$result = $other;


	$data = $ci->detail_model->get_date_finish($header_id);
	if($data->date_finish_header != NULL)
	{
		$result = $data->date_finish_header;

		if(strtotime($data->date_finish_header) <= strtotime($other))
		{
			$result = $other;
		}
	}

	if($show != '')
	{
		$result = str_replace("/", "-", $show); 
	}

	return date('d-m-Y', strtotime($result));
}

function date_to_time($date)
{
	return strtotime($date);
}

function week_in_year() {
	
	$dt = [];
	$year           = date('Y');
	$yearEnd           = date('Y');

	$month          = date('m');
	$startMonth = $month - 1;
	$endMonth = $month + 1;
	if($startMonth == 0) {
		$startMonth = 12;
		$year = $year - 1;
	}

	if($endMonth == 13) {
		$endMonth = 1;
		$yearEnd = $yearEnd + 1;
	}

	$firstDayOfYear = mktime(0, 0, 0, $startMonth, 1, $year);
	$nextMonday     = strtotime('monday', $firstDayOfYear);
	$nextSunday     = strtotime('sunday', $nextMonday);
	
	while (date('Y', $nextMonday) == $year) {

		$date_start  = date('d/m/Y', $nextMonday);
		$date_finish = date('d/m/Y', $nextSunday);

		if($nextSunday <= strtotime($yearEnd.'-'.$endMonth.'-31')) {
			$dt[] =  array(
				'date_start'  => $date_start,
				'date_finish' => $date_finish
			);	
		}
	
		$nextMonday = strtotime('+1 week', $nextMonday);
		$nextSunday = strtotime('+1 week', $nextSunday);
		
	}
	
	return $dt;	
}

function add_zero($numbering)
{
	return str_pad($numbering, 2, '0', STR_PAD_LEFT);
}

/**
 * Date Y-m-d
 * @param  $date
 * @return Text
 */
function indonesian_date($date){
	$bln_indo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
 
	$tahun = substr($date, 0, 4);
	$bulan = substr($date, 5, 2);
	$tgl   = substr($date, 8, 2);
 
	$result = $tgl . " " . $bln_indo[(int)$bulan-1] . " ". $tahun;		
	return($result);
}

function indonesia_day($date)
{
	$array_hari = array(1=>"Senin","Selasa","Rabu","Kamis","Jumat", "Sabtu","Minggu");
	return $array_hari[date('N', strtotime($date))];
}

function get_record_detail_by_header($header_id, $shift = 0)
{
	$ci =& get_instance();
	$ci->load->model('master/detail_model');

	return $ci->detail_model->count_record_by_header_shift($header_id, $shift);
}

function sum_target_section($header_id, $machine, $shift)
{
	$ci =& get_instance();
	$ci->load->model('master/detail_model');

	$target_prod_btg = '';
	$weight_standard = '';
	$len = '';

	$sum = array();

	$data = $ci->detail_model->get_data_for_grid_dinamic($header_id, $shift, false);
	if($data)
	{
		foreach($data as $row)
		{
			$get_master_query =  $ci->query_model->get_master_advance($machine, $row->section_id)->row();

			$target_prod = $row->target_prod;
			$f2_estfg = ($get_master_query) ? $get_master_query->F2_EstFG : '';
			$weight_standard = ($get_master_query) ? (float) round($get_master_query->WeightStandard, 3) : '';
			$hole_count = ($get_master_query) ? $get_master_query->HoleCount : '';
			$len = $row->Length;
			$target_prod_btg = $f2_estfg * $target_prod * $hole_count;
			$target_section = $weight_standard * $target_prod_btg * $len;

			array_push($sum, $target_section);
		}
		
		return array_sum($sum); 
	}

	return '0';
}

function number_float($val) {
	return (float) number_format($val, 3, ',', '.');
}

?>