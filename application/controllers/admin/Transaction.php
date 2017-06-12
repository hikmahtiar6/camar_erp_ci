<?php
/**
 * Transaction Controller
 */
class Transaction extends CI_Controller 
{
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// check session
		$this->auth->_is_authentication();

		// load model
		$this->load->model('section_model');
		$this->load->model('master_model');
		$this->load->model('master/detail_model');
		$this->load->model('master/header_model');
		$this->load->model('master/shift_model');
		$this->load->model('master/query_model');
		$this->load->model('master/lot_model');
		$this->load->model('master/operatorLot_model');
		$this->load->model('master/finishing_model');
	}

	/**
	 * Index Page
	 */
	public function index()
	{
		$this->twiggy->template('admin/transaction/index')->display();
	}

	/**
	 * Create or Edit page (select machine)
	 */
	public function create($search = '')
	{
		$shift_data = $this->shift_model->get_data();
		$machine_data = $this->master_model->get_data_machine();
		$this->twiggy->set('machine_data', $machine_data);
		$this->twiggy->set('shift_data', $shift_data);

		$week = date('W') - 5;
		$week_next = $week + 10;

		$this->twiggy->set('week', $week);
		$this->twiggy->set('week_next', $week_next);

		if($search != '')
		{
			$this->twiggy->template('admin/transaction/step1search')->display();
		}
		else
		{
			$this->twiggy->template('admin/transaction/step1')->display();
		}
	}

	public function detail($header_id)
	{
		$machine = '';
		$dates = false;

		$get_header = $this->header_model->get_data_by_id($header_id);
		if($get_header)
		{
			$machine = $get_header->machine_id;
			$dates = create_date_range($get_header->date_start,$get_header->date_finish);
		}

		$shift_data = $this->master_model->get_data_shift();
		$section_data = $this->get_data_section($header_id);
		$finishing_data = $this->finishing_model->get_data();

		$get_operator = $this->operatorLot_model->get_data_by(array('grup' => 'operator'));
		$get_operator2 = $this->operatorLot_model->get_data_by(array('grup' => 'wakil'));
		
		$this->twiggy->set('operator1', $get_operator);
		$this->twiggy->set('operator2', $get_operator2);
		$this->twiggy->set('shift_data', $shift_data);
		$this->twiggy->set('finishing_data', $finishing_data);
		$this->twiggy->set('section_data', $section_data);
		$this->twiggy->set('tanggal_data', $this->get_tanggal($header_id));
		$this->twiggy->set('dates', $dates);
		$this->twiggy->set('header_id', $header_id);
		$this->twiggy->set('machine_id', $machine);
		$this->twiggy->template('admin/transaction/index')->display();
	}

	/**
	 * Data Transacation
	 */
	public function data($header_id = '')
	{
		$data = array();

		$dt_start = date('Y-m-d', strtotime($this->session->userdata('date_start')));
		$dt_finish = date('Y-m-d', strtotime($this->session->userdata('date_finish')));
		$shift = $this->session->userdata('shift');
		$get_md = $this->section_model->get_data_detail($dt_start, $dt_finish, $shift, $machine_id = '', $section_id = '',$header_id);

		$machine = '';
		$header_data = $this->header_model->get_data_by_id($header_id);
		if($header_data)
		{
			$machine = $header_data->machine_id;
		}


		$sum = array();

		if($get_md)
		{
			$no = 1;
			foreach($get_md as $gmd)
			{
				$get_master_query =  $this->query_model->get_master_advance($machine, $gmd->section_id)->row();
				$target_prod_btg = $gmd->target_prod;

				$f2_estfg = ($get_master_query) ? $get_master_query->F2_EstFG : '';
				$weight_standard = ($get_master_query) ? $get_master_query->WeightStandard : '';


				if($f2_estfg != NULL)
				{
					$target_prod_btg = $f2_estfg * $gmd->target_prod; 
				}

				array_push($sum, $weight_standard * $target_prod_btg * $gmd->Length);
				
				$data[] = array(
					'no'               => $no,
					'id'               => $gmd->master_detail_id,
					'machine_id'	   => $gmd->machine_id,
					'header_id'	       => $gmd->header_id,
					'tanggal1'         => ($gmd->tanggal == null) ? '' : date('d-m-Y', strtotime($gmd->tanggal)),
					'tanggal2'         => ($gmd->tanggal == null) ? '<label class="editable-empty">Silahkan diisi</label>' : date('d-m-Y', strtotime($gmd->tanggal)),
					'shift'            => $gmd->shift,
					'shift_name'       => $gmd->ShiftDescription,
					'section_id'       => $gmd->section_id,
					'section_name'     => $gmd->SectionDescription,
					'mesin'            => $gmd->machine_type,
					'billet'           => ($get_master_query) ? $get_master_query->BilletTypeId : '-',
					'len'              => $gmd->LengthId,
					'len_name'         => $gmd->Length,
					'finishing'        => $gmd->finishing,
					'finishing_name'   => $gmd->finishing_name,
					'target_prod'      => ($gmd->target_prod == null) ? '' : $gmd->target_prod,
					'index_dice_value' => ($gmd->index_dice == null) ? '' : $gmd->index_dice,
					'index_dice'       => $this->convert_dice($gmd->index_dice),
					'index_dice_count' => $this->count_dice($gmd->index_dice),
					'ppic_note'        => ($gmd->ppic_note == null) ? '' : $gmd->ppic_note,
					'master_id'        => $gmd->master_id,
					'target_prod_btg'  => $target_prod_btg,
					'die_type'         => ($get_master_query) ? $get_master_query->DieTypeName : '-',
					'weight_standard'  => $weight_standard,
					'target_section'   => $weight_standard * $target_prod_btg * $gmd->Length,
					'total_target'     => array_sum($sum),
					'shift_start'      => date('H:i:s', strtotime($gmd->ShiftStart)),
					//'shift_end'        => date('H:i:s', strtotime($gmd->ShiftStart) + time($gmd->actual_pressure_time * $gmd->target_prod)),
					'null'             => '-',
					'apt'              => '',
					'action'           => '',
				);

				$no++;
			}
		}

		$response = array(
			'data' => $data,
			'recordsTotal' => count($data)
		);

		$this->output->set_output(json_encode($response));
	}

	private function convert_dice($dice)
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

	private function count_dice($dice)
	{
		$arr = array();
		$expl = preg_split('/,/', $dice, NULL, PREG_SPLIT_NO_EMPTY);
		//$expl = explode(",", substr($dice, 1, 1000000000000000000000));

		return count($expl);
	}

	/**
	 * Koreksi
	 */
	public function koreksi()
	{
		$post = $this->input->post();

		$machine_id  = $post['mesin'];
		$date_start =  str_replace('/', '-', $post['date_start']);
		$date_finish =  str_replace('/', '-', $post['date_finish']);

		$searching = array(
			'machine_id' => $machine_id
		);

		$get_header = $this->header_model->get_data_by($searching);

		if(!$get_header)
		{
			$response = array(
				'status'  => 'warning',
				'message' => 'Mesin belum diinputkan, silahkan buat SPK baru sesuai mesin yg diinginkan'
			);
		} 
		else
		{
			$data_for_update_header = array(
				'date_start'  => date('Y-m-d', strtotime($date_start)),
				'date_finish' => date('Y-m-d', strtotime($date_finish)),
			);

			$this->session->set_userdata('date_start', $date_start);
			$this->session->set_userdata('date_finish', $date_finish);

			$update_header = $this->header_model->update($get_header->header_id, $data_for_update_header);

			$url = site_url('admin/transaction/detail/'.$get_header->header_id);

			$response = array(
				'status'  => 'success',
				'message' => 'Data tersedia, anda bisa mengedit detail spk',
				'url'     => $url
			);
		}

		return $this->output->set_output(json_encode($response));
		
	}

	/**
	 * Save action
	 */
	public function save()
	{
		$post = $this->input->post();

		$id = $post['id'];
		$machine_id  = $post['mesin'];
		$week  = $post['week'];
		$date_start =  str_replace('/', '-', $post['date_start']);
		$date_finish =  str_replace('/', '-', $post['date_finish']);

		$data_for_insert_header = array(
			'machine_id'  => $machine_id,
			'date_start'  => date('Y-m-d', strtotime($date_start)),
			'date_finish' => date('Y-m-d', strtotime($date_finish)),
			'week'        => ltrim($week),
		);

		$searching = array(
			'week'       => ltrim($week),
			'machine_id' => $machine_id
		);

		$this->session->set_userdata('date_start', $date_start);
		$this->session->set_userdata('date_finish', $date_finish);
		$this->session->set_userdata('shift', '0');

		$get_header = $this->header_model->get_data_by($searching);
		if($get_header)
		{
			/*$data_for_update_header = array(
				'date_start'  => date('Y-m-d', strtotime($date_start)),
				'date_finish' => date('Y-m-d', strtotime($date_finish)),
			);*/

			//$update_header = $this->header_model->update($get_header->header_id, $data_for_update_header);

			$url = site_url('admin/transaction/detail/'.$get_header->header_id);

			$response = array(
				'status'  => 'success',
				'message' => 'Data sudah tersedia, anda bisa mengedit detail spk',
				'url'     => $url
			);
		}
		else
		{
			$saving = $this->section_model->save('header', $data_for_insert_header);
			if($saving)
			{

				$sec_id = '';

				$get_section = $this->query_model->get_master_advance($machine_id, '')->row();
				if($get_section)
				{
					$sec_id = $get_section->SectionId;
				}

				$tgl = '';
				$get_head = $this->header_model->get_data_by_id($this->section_model->get_last_insert_id());
				if($get_head)
				{
					$tgl = $get_head->date_start;
				}

				$data_for_insert_detail = array(
					'header_id'  => $this->section_model->get_last_insert_id(),
					'tanggal'    => $tgl,
					'section_id' => $sec_id,
					'shift'      => 'SH-15/10-0001'
				);

				$url = site_url('admin/transaction/detail/'.$this->section_model->get_last_insert_id());

				$saving_detail = $this->detail_model->save($data_for_insert_detail);

				$response = array(
					'message' => 'Transaksi berhasil disimpan',
					'status'  => 'success',
					'url'     => $url,
				);
			}
			else
			{
				$response = array(
					'message' => 'Transaksi gagal disimpan',
					'status'  => 'error',
				);
			}
		}
	
		return $this->output->set_output(json_encode($response));
	}

	public function get_tanggal_header($header_id)
	{
		$row = array();
		$header_data = $this->header_model->get_data_by_id($header_id);

		$data = false;

		if($header_data)
		{
			$machine_id = $header_data->machine_id;

			$data = create_date_range($header_data->date_start, $header_data->date_finish);
		}

		if($data)
		{
			foreach($data as $r)
			{
				$row[] = array(
					'value' => date('d-m-Y', strtotime($r)),
					'text'  => date('d-m-Y', strtotime($r)),
				);
			}
		}

		return $this->output->set_output(json_encode($row));
	}

	private function get_tanggal($header_id)
	{
		$row = array();
		$header_data = $this->header_model->get_data_by_id($header_id);

		$data = false;

		if($header_data)
		{
			$machine_id = $header_data->machine_id;

			$data = create_date_range($header_data->date_start, $header_data->date_finish);
		}

		if($data)
		{
			foreach($data as $r)
			{
				$row[] = array(
					'value' => date('d-m-Y', strtotime($r)),
					'text'  => date('d-m-Y', strtotime($r)),
				);
			}
		}

		return $row;
	}

	/**
	 * edit save view
	 */
	public function edit($id)
	{

		if($id != 'new')
		{
			$get_detail = $this->detail_model->get_data_by_id($id);
			if($get_detail)
			{
				$get_header = $this->header_model->get_data_by_id($get_detail->header_id); 
				if($get_header)
				{
					$this->twiggy->set('get_header', $get_header);				
				}
			}
		}

		$machine_data = $this->master_model->get_data_machine();
		/*$section_data = $this->section_model->get_data();
		$shift_data = $this->master_model->get_data_shift();
		$len_data = $this->master_model->get_data_len();
		$billet_data = $this->master_model->get_data_billet();
		$finishing_data = $this->master_model->get_data_finishing();

		$this->twiggy->set('section_data', $section_data);
		$this->twiggy->set('shift_data', $shift_data);
		$this->twiggy->set('len_data', $len_data);
		$this->twiggy->set('billet_data', $billet_data);
		$this->twiggy->set('finishing_data', $finishing_data);*/
		$this->twiggy->set('machine_data', $machine_data);
		$this->twiggy->template('admin/transaction/edit')->display();
	}

	/**
	 * delete transaksi
	 */
	public function delete($id)
	{
		$del = $this->section_model->delete($id);
		if($del)
		{
			$response = array(
				'message' => 'Transaksi yg terpilih berhasil dihapus',
				'status'  => 'success',
			);
		}
		else
		{
			$response = array(
				'message' => 'Transaksi yg terpilih  gagal dihapus',
				'status'  => 'danger',
			);
		}

		$this->output->set_output(json_encode($response));
	}

	/**
	 * update inline
	 */
	public function update_inline()
	{
		$post = $this->input->post();
		$id = $post['id'];
		$type = $post['type'];
		$val = $post['value'];
		$machine = (isset($post['machine'])) ? $post['machine'] : '';

		switch ($type) {

			case 'index_dice':

				$data = array(
					$type => $val
				);

				$update = $this->section_model->update($id, $data);
				if($update) 
				{
					$dice = '';
					$get_detail = $this->detail_model->get_data_by_id($id);
					if($get_detail)
					{
						$dice = $get_detail->index_dice;
					}
					$response = array(
						'dice'       => ($this->convert_dice($dice) != '') ? $this->convert_dice($dice) : 'Silahkan pilih',
						'dice_count' => $this->count_dice($dice)
					);
				}
				else
				{
					$response = array(
						'dice'       => 'no updated',
						'dice_count' => '0',
					);
				}

				return $this->output->set_output(json_encode($response));

			break;

			case 'section_id':
				
				$data = array(
					$type => $val
				);

				$update = $this->section_model->update($id, $data);
				if($update) 
				{
					$section_name = '';
					$billet = '';
					$f2_estfg = '';
					$weight_standard = '';
					$die_type_name = '';

					$get_master = $this->query_model->get_master_advance($machine, $val)->row();
					if($get_master)
					{
						$section_name = $get_master->SectionDescription;
						$f2_estfg = $get_master->F2_EstFG;
						$weight_standard = $get_master->WeightStandard;
						$billet = $get_master->BilletTypeId; 
						$die_type_name = $get_master->DieTypeName; 
					}
					$response = array(
						'status'          => 'success',
						'section_name'    => $section_name,
						'section_id'      => $val,
						'billet_id'       => $billet,
						'weight_standard' => $weight_standard,
						'die_type_name'   => $die_type_name,
						'detail_section'  => $val
					);
				}
				else
				{
					$response = array(
						'status' => 'error',
					);
				}

				return $this->output->set_output(json_encode($response));

				break;
			
			case 'tanggal':

				$data = array(
					$type => date('Y-m-d', strtotime($val))
				);

				$update = $this->section_model->update($id, $data);
				if($update) 
				{
					$response = 'yes updated';
				}
				else
				{
					$response = 'no updated';
				}

				return $this->output->set_output(json_encode($response));

			break;

			default:
				
				$data = array(
					$type => $val
				);

				$update = $this->section_model->update($id, $data);
				if($update) 
				{
					$response = 'yes updated';
				}
				else
				{
					$response = 'no updated';
				}

				return $this->output->set_output(json_encode($response));

				break;
		}
 
	}

	/**
	 * delete selected
	 */
	public function delete_selected()
	{
		$id = $this->input->post('id');

		$response = array(
			'status'  => 'error',
			'message' => 'transaksi gagal dihapus'
		);

		foreach($id as $row)
		{
			$get_detail = $this->detail_model->get_data_by_id($row);
			if($get_detail)
			{
				$get_header = $this->header_model->get_data_by_id($get_detail->header_id);
				if($get_header)
				{
					$del = $this->detail_model->delete($get_detail->master_detail_id);

					//$del = $this->header_model->delete($get_header->header_id);
					if($del)
					{
						$response = array(
							'status'  => 'success',
							'message' => 'transaksi berhasil dihapus'
						);
					
						//$del_detail = $this->detail_model->delete($get_detail->master_detail_id);
					}
					else
					{
						$response = array(
							'status'  => 'error',
							'message' => 'transaksi gagal dihapus'
						);
					}
				}
			}
		}

		return $this->output->set_output(json_encode($response));
	}

	public function add_row_by_header()
	{
		$header_id = $this->input->post('header_id');
		$tanggal = $this->input->post('tanggal');
		$get_header = $this->header_model->get_data_by_id($header_id);
		$tgl = '';

		$dt_start = date('Y-m-d', strtotime($this->session->userdata('date_start')));
		$dt_finish = date('Y-m-d', strtotime($this->session->userdata('date_finish')));
		$shift = $this->session->userdata('shift');

		if($get_header)
		{
			$tgl = date('Y-m-d', strtotime($get_header->date_start));
		}

		$data_insert_detail = array(
			'header_id' => $header_id,
			'tanggal'   => $tgl,
			'section_id'=> '035',
			'shift'     => 'SH-15/10-0001'
		);

		$get_last_data_detail = $this->detail_model->advance_search($dt_start, $dt_finish, $shift, $machine_id = '', $section_id = '', $header_id, $limit = '', $start = '', $order = 'master_detail_id', $type_order = 'DESC')->row();

		if($get_last_data_detail)
		{
			$sec_id = $get_last_data_detail->section_id;
			if($sec_id == NULL)
			{
				$sec_id = '035';
			}

			/*if($tanggal == '')
			{
				$tgl = $get_last_data_detail->tanggal;
			}
			else
			{
				$tgl = date('Y-m-d', strtotime($tanggal));		
			}*/

			$data_insert_detail = array(
				'header_id'   => $header_id,
				'tanggal'     => $tgl,
				'section_id'  => $sec_id,
				'shift'       => 'SH-15/10-0001',
				'len'         => $get_last_data_detail->len,
				'finishing'   => $get_last_data_detail->finishing,
				'target_prod' => $get_last_data_detail->target_prod
			);
		}


		$saving = $this->detail_model->save($data_insert_detail);
		if($saving)
		{
			$response = array(
				'status'  => 'success',
				'message' => 'transaksi berhasil ditambahkan'
			);
		}
		else
		{
			$response = array(
				'status'  => 'error',
				'message' => 'transaksi gagal ditambahkan'
			);
		}
		return $this->output->set_output(json_encode($response));
	}

	

	/**
	 * json jqgrid
	 */
	public function json($header_id) 
	{
		$page  = ($this->input->post('page')) ? $this->input->post('page') : 1;
		$limit = ($this->input->post('rows')) ? $this->input->post('rows') : 10000000 ;
		$sidx  = ($this->input->post('sidx')) ? $this->input->post('sidx') : 'master_detail_id';
		$sord  = ($this->input->post('sord')) ? $this->input->post('sord') : 'master_detail_id';
		$search_get  = $this->input->post('_search');
		
		$date = ($this->input->post('tanggal') != '') ? date('Y-m-d', strtotime($this->input->post('tanggal'))) : '';
		
		$sum = array();

		$dt_start = date('Y-m-d', strtotime($this->session->userdata('date_start')));
		$dt_finish = date('Y-m-d', strtotime($this->session->userdata('date_finish')));
		$shift = $this->session->userdata('shift');
		 
		if(!$sidx) $sidx=1;

		$get_md = $this->section_model->get_data_detail_new($dt_start, $dt_finish, $shift, $machine_id = '', $section_id = '',$header_id, '', '');

		$machine = '';
		$header_data = $this->header_model->get_data_by_id($header_id);
		if($header_data)
		{
			$machine = $header_data->machine_id;
		}
		 
		# Untuk Single Searchingnya #
		/*$where = ""; //if there is no search request sent by jqgrid, $where should be empty
			$searchField = isset($_GET['searchField']) ? $_GET['searchField'] : false;
		$searchString = isset($_GET['searchString']) ? $_GET['searchString'] : false;
		if ($search_get == 'true') 
		{
			$where = array($searchField => $searchString);
		}*/
		# End #
		 
		//$count = $this->section_model->count_master_advance($where);
		
		$count = count($get_md);

		$total_pages = 0;
		if($count > 0) 
		{
			$total_pages = ceil($count/$limit);
		}


		if ($page > $total_pages) $page=$total_pages;
			$start = $limit*$page - $limit;
		if($start <0) $start = 0;		
		 
		$data1 = $get_md = $this->section_model->get_data_detail_new($dt_start, $dt_finish, $shift, $machine_id = '', $section_id = '',$header_id, $limit + $start, $start, $date);

		//echo ($limit + $start).'-'.$start;
		
		$response = new stdClass();

		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;

		$i=0;
		foreach($data1 as $gmd)
		{
			$get_master_query =  $this->query_model->get_master_advance($machine, $gmd->section_id)->row();

			$target_prod_btg = $gmd->target_prod;
			$f2_estfg = ($get_master_query) ? $get_master_query->F2_EstFG : '';
			$weight_standard = ($get_master_query) ? (float) round($get_master_query->WeightStandard, 3) : '';
			$hole_count = ($get_master_query) ? $get_master_query->HoleCount : '';

			if($f2_estfg != NULL)
			{
				$target_prod_btg = $f2_estfg * $gmd->target_prod * $hole_count; 
			}
			$len = $gmd->Length;
			$target_section = $weight_standard * $target_prod_btg * $len;

			$btn_lot = "Isi Lot";
			$btn_class = "btn-primary";
			if($gmd->is_posted)
			{
				$btn_class = "btn-success";
				$btn_lot = "Lihat Lot";
			}

			$tgl = ($gmd->tanggal == null) ? '' : date('d-m-Y', strtotime($gmd->tanggal));
			$response->rows[$i]['id']   = $gmd->master_detail_id;
			$response->rows[$i]['cell'] = array(
				$gmd->master_detail_id,
				'<a href="javascript:;" class="btn btn-xs '.$btn_class.' waves-effect" data-toggle="modal" data-target="#defaultModal" onclick="window.TRANSACTION.handleModalLot('.$gmd->master_detail_id.')">'.$btn_lot.'</a>', 
				$tgl,
				$gmd->ShiftDescription,
				$gmd->SectionDescription,
				$gmd->section_id,
				$gmd->machine_id_header,
				($get_master_query) ? $get_master_query->BilletTypeId : '-',
				$len,
				$gmd->finishing_name,
				$this->convert_dice($gmd->index_dice),
				//$this->count_dice($gmd->index_dice),
				($gmd->target_prod == null) ? '' : $gmd->target_prod,
				($gmd->ppic_note == null) ? '' : $gmd->ppic_note,
				$target_prod_btg,
				$weight_standard,
				$target_section,
				($get_master_query) ? $get_master_query->DieTypeName : '-'
			);
			$i++;
		}

		$this->output->set_output(json_encode($response));
	}

	public function crud()
	{
		$oper = $this->input->post('oper');
		$id = $this->input->post('id');
		$tanggal = $this->input->post('tanggal');
		$shift = $this->input->post('shift');
		$section_id = $this->input->post('section_id');
		$len = $this->input->post('len');
		$ppic_note = $this->input->post('ppic_note');
		$finishing = $this->input->post('finishing');
		$target_prod = $this->input->post('target_prod');
		$index_dice = $this->input->post('index_dice');
		$idxdice = $this->input->post('idxdice');
		 
		switch ($oper) {
			case 'add':
			break;
			case 'edit':
				$datanya=array(
					'tanggal'    => date('Y-m-d', strtotime($tanggal)),
					'shift'      => $shift,
					'section_id' => $section_id,
					'len'        => $len,
					'ppic_note'  => $ppic_note,
					'finishing'  => $finishing,
					'target_prod'=> $target_prod,
					'index_dice' => $this->set_idxdice($idxdice),
				);
				$this->detail_model->update($id, $datanya);
			break;
			case 'del':
				$this->detail_model->delete($id);
				$this->lot_model->delete_lot($id);
				$this->lot_model->delete_head_lot($id);
			break;
		}	
	}

	private function set_idxdice($array)
	{
		$str = '';
		foreach($array as $row)
		{
			$str .= $row.', ';
		}

		return rtrim($str, ', ');
	}

	public function get_rumus($master_detail_id, $target_prod_key = '')
	{
		$target_prod = '';
		$machine = '';
		$target_prod_btg = '';
		$weight_standard = '';
		$die_type_name = '';
		$len_post = ($this->input->post('len')) ? $this->input->post('len') : '';

		$get_detail = $this->detail_model->get_data_by_id($master_detail_id);
		if($get_detail)
		{

			$header_data = $this->header_model->get_data_by_id($get_detail->header_id);
			if($header_data)
			{
				$machine = $header_data->machine_id;
			}

			$get_master_query =  $this->query_model->get_master_advance($machine, $get_detail->section_id)->row();

			$die_type_name = ($get_master_query) ? $get_master_query->DieTypeName : ''; 

			//$target_prod = $get_detail->target_prod;
			$target_prod_btg = $get_detail->target_prod;
			$f2_estfg = ($get_master_query) ? $get_master_query->F2_EstFG : '';
			$weight_standard = ($get_master_query) ? (float) round($get_master_query->WeightStandard, 3) : '';
			$len = $get_detail->Length;
			$hole_count = ($get_master_query) ? $get_master_query->HoleCount : '';

			if($len_post != '')
			{
				$len = $len_post;
			}

			if(is_numeric($target_prod_key))
			{
				$target_prod = $target_prod_key;
			}

			if($f2_estfg != NULL)
			{
				$target_prod_btg = $f2_estfg * $target_prod * $hole_count; 
			}
			$target_section = $weight_standard * $target_prod_btg * $len;

			//array_push($sum, $weight_standard * $target_prod_btg * $gmd->Length);
		}

		$response = array(
			'target_prod_btg' => $target_prod_btg,
			'weight_standard' => $weight_standard,
			'target_section'  => $target_section,
			'die_type_name'   => $die_type_name
		);

		return $this->output->set_output(json_encode($response));
	}

	public function grid_dinamic($header_id = '', $machine_id = 'no', $shift = 0, $tgl = '')
	{
		$tanggal = $this->input->post('tanggal');
		
		if($header_id == 'no')
		{
			$header_id = '';
		}

		$get_operator = $this->operatorLot_model->get_data_by(array('grup' => 'operator'));
		$get_operator2 = $this->operatorLot_model->get_data_by(array('grup' => 'wakil'));
		$grid_data = $this->detail_model->get_data_for_grid_dinamic($header_id, $shift, true, $machine_id, $tgl);

		$this->twiggy->set('get_operator', $get_operator);
		$this->twiggy->set('get_operator2', $get_operator2);
		$this->twiggy->set('grid_data', $grid_data);
		$this->twiggy->set('header', $header_id);
		$this->twiggy->set('tgl', $tgl);
		$this->twiggy->set('tanggal', $tanggal);
		$this->twiggy->template('admin/transaction/grid.dinamic')->display();
	}

	/**
	 * get data section
	 */
	private function get_data_section($header_id)
	{
		$row = array();
		$header_data = $this->header_model->get_data_by_id($header_id);

		$machine_id = '';

		if($header_data)
		{
			$machine_id = $header_data->machine_id;
		}

		$data = $this->query_model->get_master_section($machine_id)->result();

		return $data;
	}

}
?>