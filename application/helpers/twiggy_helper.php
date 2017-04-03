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

function split_text($str, $spliten)
{
	return explode($spliten, $str);
}

function strpos_text($str, $check)
{
	return strpos($str, $check) !== false;
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
					$expl2 = end((explode(".", $rexpl)));
					$txt .= substr($expl2, 2, 10).', ';
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
	
	$ci =& get_instance();

	$dt = [];
	$year = date('Y');
	$yearEnd = date('Y');

	$month = date('m');
	$startMonth = 1;

	$firstDayOfYear = mktime(0, 0, 0, $startMonth, 1, $year);
	$nextMonday     = strtotime('monday', $firstDayOfYear);
	$nextSunday     = strtotime('sunday', $nextMonday);

	$no = 1;
	
	while (date('Y', $nextMonday) == $year) {

		$date_start  = date('d/m/Y', $nextMonday);
		$date_finish = date('d/m/Y', $nextSunday);

		$awal = $month - 1;
		$end = $month + 1;

		if($awal == 0)
		{
			$awal = 1;
			$yearEnd - 1;
		}


			$dt[] =  array(
				'no'          => $no,
				'date_start'  => $date_start,
				'date_finish' => $date_finish
			);	
		if($nextSunday <= strtotime($yearEnd.'-'.$endMonth.'-31')) {
		}
	
		$nextMonday = strtotime('+1 week', $nextMonday);
		$nextSunday = strtotime('+1 week', $nextSunday);

		$no++;
		
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

function get_detail_advance($machine_id = '', $tanggal = '', $shift = 0, $distinct = false)
{	
	$ci =& get_instance();
	$ci->load->model('master/query_model');

	return $ci->query_model->get_report_advance($machine_id, $tanggal, $shift, $distinct)->result();
}

function get_detail_advance_lot($machine_id = '', $tanggal = '', $shift = 0)
{	
	$ci =& get_instance();
	$ci->load->model('master/query_model');

	return $ci->query_model->get_report_advance_lot($machine_id, $tanggal, $shift)->result();
}

function sum_target_section($header_id, $machine, $shift, $tgl)
{
	$ci =& get_instance();
	$ci->load->model('master/detail_model');

	$target_prod_btg = '';
	$weight_standard = '';
	$len = '';

	$sum = array();

	$data = $ci->detail_model->get_data_for_grid_dinamic($header_id, $shift, false, $machine, $tgl);
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

function check_array($array)
{
	if(is_array($array))
	{
		return true;
	}

	return false;
}

function selisih_waktu($waktu_akhir, $waktu_awal)
{
	$awal  = new DateTime($waktu_awal);
	$akhir = new DateTime($waktu_akhir);

	$diff  = $awal->diff($akhir);
	
	$res = '-';

	if($diff->h > 0)
	{
		$res .= $diff->h.' jam ';
	}

	if($diff->i > 0)
	{
		$res .= $diff->i .' menit';
	}

	return $res;
}

function trims($str)
{
	return trim($str);
}

function get_hasil_prod_btg($master_detail_id, $machine_id, $section_id)
{
	$ci =& get_instance();
	$ci->load->model('master/lot_model');

	$get_sum_jml_btg = $ci->lot_model->suming('a.jumlah_di_rak_btg', 0, $master_detail_id, $machine_id, $section_id)->row();

	if($get_sum_jml_btg)
	{
		return $get_sum_jml_btg->jml;
	}

	return 0;
}

function get_berat_hasil($master_detail_id, $machine_id, $section_id)
{
	$ci =& get_instance();
	$ci->load->model('master/query_model');
	$ci->load->model('master/lot_model');


	$get_master_query =  $ci->query_model->get_master_advance($machine_id, $section_id)->row();
	$len = ($get_master_query) ? (float) round($get_master_query->Length, 3) : '';
	$get_counting_ak = $ci->lot_model->counting('a.berat_ak', 0, $master_detail_id, $machine_id, $section_id)->row();
	$get_sum_ak = $ci->lot_model->suming('a.berat_ak', 0, $master_detail_id, $machine_id, $section_id)->row();
	$get_sum_jml_btg = $ci->lot_model->suming('a.jumlah_di_rak_btg', 0, $master_detail_id, $machine_id, $section_id)->row();
	$rata2_berat_ak = ($get_sum_ak->jml != NULL) ? (float) round($get_sum_ak->jml / $get_counting_ak->jml * 2 / 1000, 3) : '';	

	return $len * $get_sum_jml_btg->jml * $rata2_berat_ak;
}

function get_total_billet_kg($master_detail_id, $machine_id, $section_id, $p_billet_aktual, $jumlah_billet)
{
	$ci =& get_instance();
	$ci->load->model('master/query_model');
	$ci->load->model('master/lot_model');

	$get_master_query =  $ci->query_model->get_master_advance($machine_id, $section_id)->row();
	$get_sum_ak = $ci->lot_model->suming('a.berat_ak', 0, $master_detail_id, $machine_id, $section_id)->row();
	$billet_weight = ($get_master_query) ? (float) round($get_master_query->BilletWeight, 3) : '';

	$berat_billet = ($get_sum_ak->jml != NULL) ? (float) round($p_billet_aktual * $jumlah_billet * $billet_weight, 2) : '';

	return $berat_billet;
}

function get_target_prod_btg($machine_id, $section_id, $target_prod, $len)
{
	$ci =& get_instance();
	$ci->load->model('master/query_model');
	$ci->load->model('master/lot_model');

	$get_master_query =  $ci->query_model->get_master_advance($machine_id, $section_id)->row();

	$target_prod_btg = $target_prod;
	$f2_estfg = ($get_master_query) ? $get_master_query->F2_EstFG : '';
	$hole_count = ($get_master_query) ? $get_master_query->HoleCount : '';
	$weight_standard = ($get_master_query) ? (float) round($get_master_query->WeightStandard, 3) : '';

	if($f2_estfg != NULL)
	{
		$target_prod_btg = $f2_estfg * $target_prod * $hole_count; 
	}
	$target_section = $weight_standard * $target_prod_btg * $len;

	return array(
		'target_prod_btg'   => $target_prod_btg,
		'target_section_kg' => $target_section
	);
}

function get_dies_department($tgl, $shift, $machine)
{
	$ci =& get_instance();
	$ci->load->model('master/indexdice_model');

	$data = $ci->indexdice_model->filter_dies_departement($tgl, $shift, $machine);
	return $data;
}

function convert_dies_department($data)
{
	$txt  = '';

	foreach($data as $row) 
	{
		$txt .= $row->index_dice.'|'.$row->SectionDescription. ", ";
	}

	$txt  = rtrim($txt, ", ");

	if(strpos_text($txt, ","))
	{
		return explode(",", $txt);
	}
	else
	{
		if($txt != " " && $txt != "")
		{
			return array($txt);
		}
	}

	return false;
}

function check_dies_log($dies_id, $date)
{
	$ci =& get_instance();
	$ci->load->model('master/indexdice_model');

	$data = $ci->indexdice_model->get_dies_log($date, $status = '', $location = '', $dies_id)->row();
	return $data;
}

?>