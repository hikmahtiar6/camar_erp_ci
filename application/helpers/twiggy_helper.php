<?php

function dump($dumping)
{
	return var_dump($dumping);
}

function arr_push($arr, $data)
{
	array_push($arr, $data);
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

function week_in_year($desc = false, $custom = '') {
	
	$ci =& get_instance();

	$dt = [];
	$year = date('Y');
	$yearEnd = date('Y');

	$month = date('m');
	$startMonth = 1;

	$firstDayOfYear = mktime(0, 0, 0, $startMonth, 1, $year);
	$nextMonday     = strtotime('sunday', $firstDayOfYear);
	$nextSunday     = strtotime('saturday', $nextMonday);

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
			'no'          => add_zero($no),
			'date_start'  => $date_start,
			'date_finish' => $date_finish
		);	
		
		/*if($nextSunday <= strtotime($yearEnd.'-'.$endMonth.'-31')) {
		}*/
	
		$nextMonday = strtotime('+1 week', $nextMonday);
		$nextSunday = strtotime('+1 week', $nextSunday);

		$no++;
		
	}

	if($desc == 'true')
	{
		usort($dt, function($a, $b) {
			return $b['no'] - $a['no'];
		});
	}

	return $dt;	
}

function add_zero($numbering, $type = 2)
{
	return str_pad($numbering, $type, '0', STR_PAD_LEFT);
}

/**
 * Date Y-m-d
 * @param  $date
 * @return Text
 */
function indonesian_date($date, $type = 'full'){
	$bln_indo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
	
	if($type == 'three') 
	{
		$bln_indo = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");
	}
 
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

function suming_lot($field, $master_detail_id)
{	
	$ci =& get_instance();
	$ci->load->model('master/detail_model');

	return $ci->detail_model->suming_lot($field, $master_detail_id);
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
	return (float) number_format($val, 2, ',', '.');
}

function to_decimal($val, $num = 2, $add_null_after = false)
{
	$decimal = (float) round($val, $num, PHP_ROUND_HALF_ODD);
	if($add_null_after)
	{
		return sprintf("%0.".$num."f",$decimal);	
	}
	return $decimal;
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
	
	$res = '';

	if($diff->h > 0)
	{
		$res .= $diff->h.' jam ';
	}

	if($diff->i > 0)
	{
		$res .= $diff->i .' menit';
	}
	
	if($res == '')
	{
		return '-';
	}

	return $res;
}

function trims($str)
{
	return trim($str);
}

function get_hasil_prod_btg($mesin_id = '', $section_id = '', $shift = '', $tgl = '', $master_detail_id = '')
{
	error_reporting(0);
	$ci =& get_instance();
	$ci->load->model('master/lot_model');

	return $ci->lot_model->get_hasil_prod_btg($mesin_id, $section_id, $shift, $tgl, $master_detail_id);
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

function get_berat_billet($master_detail_id, $machine_id, $section_id, $p_billet_aktual, $jumlah_billet)
{
	$ci =& get_instance();
	$ci->load->model('master/query_model');
	$ci->load->model('master/lot_model');

	$get_master_query =  $ci->query_model->get_master_advance($machine_id, $section_id)->row();

	$billet_weight = ($get_master_query) ? (float) round($get_master_query->BilletWeight, 3) : '';
	$get_sum_ak = $ci->lot_model->suming('a.berat_ak', 0, $master_detail_id, $machine_id, $section_id)->row();

	$berat_billet = ($get_sum_ak->jml != NULL) ? (float) round($p_billet_aktual * $jumlah_billet * $billet_weight, 2) : '';	

	return $berat_billet;
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
		
		if(strpos_text($row->index_dice, ","))
		{
			$expl = explode(",", $row->index_dice);
			foreach($expl as $exp)
			{
				$txt .= $exp.'|'.$row->SectionDescription.", ";
			}
		}
		else
		{
			$txt .= $row->index_dice.'|'.$row->SectionDescription. ", ";	
		}
		
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

function get_last_status_log_dies($dies_id)
{
	$ci =& get_instance();
	$ci->load->model('master/indexdice_model');

	$data = $ci->indexdice_model->get_last_status_log($dies_id);
	if($data)
	{
		return $data->DiesStatus;
	}

	return '-';
}

function get_last_location_log_dies($dies_id)
{
	$ci =& get_instance();
	$ci->load->model('master/indexdice_model');

	$data = $ci->indexdice_model->get_last_location_log($dies_id);
	if($data)
	{
		return $data->Location;
	}

	return '-';
}

function get_last_datetime_log_dies($dies_id)
{
	$ci =& get_instance();
	$ci->load->model('master/indexdice_model');

	$data = $ci->indexdice_model->get_last_datetime_log($dies_id);
	if($data)
	{
		return $data->LogTime;
	}

	return '-';
}

function get_last_problem_log_dies($dies_id)
{
	$ci =& get_instance();
	$ci->load->model('master/indexdice_model');

	$res['problem'] = '';
	$res['problem_id'] = '';
	$res['koreksi'] = '';
	$res['korektor'] = '';

	$data = $ci->indexdice_model->get_last_problem_log($dies_id);
	if($data)
	{
		if($data->DiesStatusId == 29)
		{
			$res['problem'] = $data->Problem;
			$res['problem_id'] = $data->DiesProblemId;
			$res['koreksi'] = $data->Koreksi;
			$res['korektor'] = $data->Korektor;
		}
	}

	return $res;
}

function get_last_log_by_dies($dies_id)
{
	$ci =& get_instance();
	$ci->load->model('master/indexdice_model');

	$data = $ci->indexdice_model->get_last_log_by_dies($dies_id);

	return $data;
}

function get_lot_scrap($header_id, $tgl, $shift = '')
{
	$ci =& get_instance();
	$ci->load->model('master/scrap_model');

	$data = $ci->scrap_model->get_data_tgl_header($header_id, $tgl, $shift);
	if($data)
	{
		return array(
			'scrap'   => $data->Scrap,
			'lost'    => $data->Lost,
			'endbutt' => $data->EndButt,
			'opr1'    => $data->Opr1,
			'opr2'    => $data->Opr2,
		);
	}

	return array(
		'scrap'   => '',
		'lost'    => '',
		'endbutt' => '',
		'opr1'    => '',
		'opr2'    => '',
	);
}

function sum_scrap($field, $machine, $shift, $tanggal)
{
	$ci =& get_instance();
	$ci->load->model('master/scrap_model');
	
	return $ci->scrap_model->sum_field($field, $machine, $shift, $tanggal);
}

function get_last_billet_actual($master_detail_id = '', $machine_id = '', $section_id = '')
{
	$ci =& get_instance();
	$ci->load->model('master/lot_model');
	
	return $ci->lot_model->get_last_billet_actual($master_detail_id, $machine_id, $section_id);
}

/**
 * Get rata2 berat Akt
 */
function get_rata2_berat_akt($master_detail_id = '')
{
	$ci =& get_instance();
	$ci->load->model('master/lot_model');
	
	$data = $ci->lot_model->get_lot_berat_actual($master_detail_id)->result();
	
	$sum = 0;
	
	if($data)
	{
		foreach($data as $row) 
		{
			$sum += $row->BeratAkt;
		}
	}
	
	$hasil = ($sum /count($data) * 2) / 1000;
	
	return $hasil;
}

/**
 * Get hasil berat billet
 */
function get_hasil_prod_kg($master_detail_id = '', $len = '0', $rata2_berat_ak = '')
{
	$ci =& get_instance();
	$ci->load->model('master/lot_model');
	
	$data = $ci->lot_model->get_lot_hasil($master_detail_id)->result();
	
	$sum = 0;
	
	if($data)
	{
		foreach($data as $row) 
		{
			$sum += $row->JumlahBtgRak;
		}
	}
	
	$hasil = $sum * $len * $rata2_berat_ak;
	
	return $hasil;
}

/**
 * Get total billet
 */
function get_total_billet($master_detail_id = '', $billet_weight = 0)
{
	$ci =& get_instance();
	$ci->load->model('master/lot_model');
	
	$data = $ci->lot_model->get_lot_billet($master_detail_id)->result();
	
	$sum = 0;
	
	if($data)
	{
		foreach($data as $row) 
		{
			$sum += $row->PBilletActual * $row->JumlahBillet * $billet_weight;
		}
	}
	
	$hasil = $sum;
	
	return $hasil;
}

/**
 * Get jumlah billet kg
 */
function get_jumlah_billet_kg($panjang = 0, $jumlah = 0, $billet_weight = 0)
{
	return $panjang * $jumlah * $billet_weight;
}

/**
 * get isian lot
 */
function get_isian_data_lot($master_detail_id, $type)
{
	$ci =& get_instance();
	$ci->load->model('master/lot_model');
	
	$data = $ci->lot_model->get_isian_billet_by_master_detail_id($master_detail_id, $type);

	return $data;
}

/**
 * count die pr
 */
function count_die_pr_by_header($header_id)
{
	$ci =& get_instance();
	$ci->load->model('master/pr_model');
	return $ci->pr_model->count_die_by_header($header_id);
}

function super_unique_die($array)
{
	$res = array();
	$result = array_map("unserialize", array_unique(array_map("serialize", $array)) );

	foreach ($result as $rr) {
		$res[] = array(
			'text' => $rr['text'],
			'value' => $rr['value'],
			'id' => $rr['id'],
		);
	}

	return $res;
}

/**
 * function get data history card by dies id
 */
function get_data_history_card_by_dies_id($dies)
{
	$ci =& get_instance();
	$ci->load->model('master/indexdice_model');
	return $ci->indexdice_model->filter_history_card_fix('', $dies, '');
}

/**
 * convert last dies in pr
 */
function get_last_dies_pr($year, $seqno)
{
	$new_year = substr($year, 2, 2);
	$seq = add_zero($seqno, 4);

	return $new_year.$seq;
}

/**
 * Replaced Text
 */
function replaced_text($subject = '', $search = '/', $replace = 'cmr')
{
	return str_replace($search, $replace, $subject);
}
?>