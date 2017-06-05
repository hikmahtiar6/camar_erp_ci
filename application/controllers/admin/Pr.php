<?php
class Pr extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('section_model');
		$this->load->model('master/machine_model');
		$this->load->model('master/die_model');
		$this->load->model('master/pr_model');
		$this->load->model('master/billet_model');
		$this->load->model('master/vendor_model');
		$this->load->model('master/query_model');

		// check session
		$this->auth->_is_authentication();
	}

	public function index()
	{
		$data_pr_headers = $this->pr_model->get_headers()->result();

		$this->twiggy->set('data_headers', $data_pr_headers);
		$this->twiggy->display('admin/pr/index');
	}

	private function year_data()
	{
		$year_data = array();
		$year = date('Y');
		$year_last = $year - 5;
		for($year; $year>$year_last; $year--)
		{
			$year_data[] = $year;
		}

		return $year_data;
	}

	public function edit($id = 'new')
	{
		$sections = $this->section_model->get_section_grouping()->result();
		$machines = $this->machine_model->get_data_type();
		$dietypeid = $this->die_model->get_data_type();
		$billet_type_data = $this->billet_model->get_billet_type()->result();
		$vendor_data = $this->vendor_model->get_data()->result();
		$document_no = $this->generate_document_number(date('Y-m-d'));
		$vendor = '';
		$vendor_id = '';
		$posting = '';
		$tgl = date('d/m/Y');
		$year_data = $this->year_data();

		if($id != 'new')
		{
			$get_last_data_pr_date = $this->pr_model->get_last_data_by_year_month($id);
			$document_no = $get_last_data_pr_date;
			$posting = $this->pr_model->get_last_data_by_year_month($id, '', 'PostedDate');
			$tgl = $this->pr_model->get_last_data_by_year_month($id, '', 'TransactionDate');
			$vendor = $this->pr_model->get_last_data_by_year_month($id, '', 'Initial');
			$vendor_id = $this->pr_model->get_last_data_by_year_month($id, '', 'BusinessPartnerId');
			$tgl = date('d/m/Y', strtotime($tgl));
		}

		$this->twiggy->set('tgl', $tgl);
		$this->twiggy->set('id', $id);
		$this->twiggy->set('year_data', $year_data);
		$this->twiggy->set('vendor', $vendor);
		$this->twiggy->set('posting', $posting);
		$this->twiggy->set('vendor_id', $vendor_id);
		$this->twiggy->set('document_no', $document_no);
		$this->twiggy->set('dietype_data', $dietypeid);
		$this->twiggy->set('section_data', $sections);
		$this->twiggy->set('machine_data', $machines);
		$this->twiggy->set('vendor_data', $vendor_data);
		$this->twiggy->set('billet_type_data', $billet_type_data);
		$this->twiggy->display('admin/pr/edit');
	}

	public function edit_detail($id = '')
	{
		$sections = $this->section_model->get_section_grouping()->result();
		$machines = $this->machine_model->get_data_type();
		$dietypeid = $this->die_model->get_data_type();
		$billet_type_data = $this->billet_model->get_billet_type()->result();

		$get_detail = $this->pr_model->get_detail($id);

		$year_data = $this->year_data();
		$this->twiggy->set('get_detail', $get_detail);
		$this->twiggy->set('year_data', $year_data);
		$this->twiggy->set('dietype_data', $dietypeid);
		$this->twiggy->set('section_data', $sections);
		$this->twiggy->set('machine_data', $machines);
		$this->twiggy->set('billet_type_data', $billet_type_data);
		$this->twiggy->display('admin/pr/edit_detail');
	}

	public function get_detail_by_header($header_id = 'new')
	{
		$response = array();

		$data = $this->pr_model->get_detail_by_header($header_id);
		if($data)
		{
			foreach($data as $row)
			{
				$response[] = array(
					'prId'        => $row->DiePurchaseRequestDetailId,
					'sectionId'   => $row->SectionId,
					'sectionName' => $row->SectionDescription,
					'diesId'      => $row->DiesId,
					'dieTypeName' => $row->DieTypeName,
					'hole'        => $row->HoleCount,
					'billet'      => to_decimal($row->BilletDiameter),
					'machineType' => $row->MachineTypeId,
					'year'        => $row->DiesYear,
					'component'   => $this->die_model->get_component_parent($row->DieTypeId)
				);
			}
		}

		output_json($response);
	}

	public function save_header()
	{
		$id = 'new';
		$response = 'error';
		$header_id = $this->input->post('header_id');
		$vendor = $this->input->post('vendor');
		$tgl = $this->input->post('tgl');
		$document_no = $this->input->post('document_no');

		$data_save = array(
			'VendorId'          => $vendor,
			'PurchaseRequestNo' => $document_no,
			'TransactionDate'   => change_format_date($tgl),
			'PurchasingType'    => 'PDies',
		);

		if($header_id != 'new')
		{
			$save = $this->pr_model->update_header($header_id, $data_save);
			if($save)
			{
				$response = 'success';
				$id = $header_id;
			}
		}
		else
		{
			$data_save['DocumentDate'] = date('Y-m-d H:i:s');
			$save = $this->pr_model->save_header($data_save);
			if($save)
			{
				$response = 'success';
				$id = $this->db->insert_id();
			}
		}

		$json = array(
			'status' => $response,
			'id'     => $id
		);

		$this->output->set_output(json_encode($json));

	}

	public function save_detail()
	{
		$response = array();
		$data_save = array();

		$header = $this->input->post('header');
		$vendor = $this->input->post('vendor');
		$section = $this->input->post('section-id');		
		$machine_type = $this->input->post('machine-type');		
		$die_type = $this->input->post('die-type');		
		$billet_type = $this->input->post('billet-type');		
		$hole = $this->input->post('hole');		
		$qty = $this->input->post('qty');
		$year = $this->input->post('dies-year');

		$component_parent = $this->die_model->get_component_parent($die_type);
		$get_initial_vendor = $this->vendor_model->get_data($vendor)->row();
		$get_machine_type = $this->machine_model->get_data_type_id($machine_type)->row();

		$last_seq_no = $this->get_last_data($vendor, $section, $machine_type, $die_type, $billet_type, $year);
		$vendor_initial = ($get_initial_vendor) ? $get_initial_vendor->Initial : '';
		$machine_type_initial = ($get_machine_type) ? $get_machine_type->Initial : '';

		$numb = ($last_seq_no != '') ? $last_seq_no + 1 : 1 ;
		$total = $numb + $qty;

		for($numb; $numb<$total; $numb++)
		{
			$data_save[] = array(
				'PurchaseRequestHeaderId' => $header,
				'SectionId'               => $section,
				'DiesId'                  => $this->generate_dies_id($vendor_initial, $component_parent, $section, $machine_type_initial, $hole, substr($year,2, 4), $numb),
				'DieTypeId'               => $die_type,
				'BilletTypeId'            => $billet_type,
				'HoleCount'               => $hole,
				'MachineTypeId'           => $machine_type,
				'DieTypeComponentId'      => $component_parent,
				'DiesSeqNo'               => $numb,
				'DiesYear'                => $year,
			);
		}

		$save = $this->pr_model->save_detail($data_save, true);

		$response = array(
			'message' => 'Gagal ditambahkan',
			'status'  => 'error',
			'id'      => $header
		);	

		if($save)
		{
			$response = array(
				'message' => 'Berhasil ditambahkan',
				'status'  => 'success',
				'id'      => $header
			);
		}

		output_json($response);
	}

	public function update_detail()
	{
		$response = array();

		$id = $this->input->post('id');
		$header = $this->input->post('header');
		$vendor = $this->input->post('vendor');
		$section = $this->input->post('section-id');		
		$machine_type = $this->input->post('machine-type');		
		$die_type = $this->input->post('die-type');		
		$billet_type = $this->input->post('billet-type');		
		$hole = $this->input->post('hole');
		$year = $this->input->post('dies-year');
		$seqno = $this->input->post('seqno');

		$component_parent = $this->die_model->get_component_parent($die_type);
		$get_initial_vendor = $this->vendor_model->get_data($vendor)->row();
		$get_initial_vendor = $this->vendor_model->get_data($vendor)->row();
		$get_machine_type = $this->machine_model->get_data_type_id($machine_type)->row();

		$last_seq_no = $this->get_last_data($vendor, $section, $machine_type, $die_type, $billet_type, $year);
		$vendor_initial = ($get_initial_vendor) ? $get_initial_vendor->Initial : '';
		$machine_type_initial = ($get_machine_type) ? $get_machine_type->Initial : '';

		$data_save = array(
			'SectionId'               => $section,
			'DiesId'                  => $this->generate_dies_id($vendor_initial, $component_parent, $section, $machine_type_initial, $hole, substr($year,2, 4) , $seqno),
			'DieTypeId'               => $die_type,
			'BilletTypeId'            => $billet_type,
			'HoleCount'               => $hole,
			'MachineTypeId'           => $machine_type,
			'DieTypeComponentId'      => $component_parent,
			'DiesYear'                => $year
		);


		$save = $this->pr_model->update_detail($id, $data_save);

		$response = array(
			'message' => 'Gagal disimpan',
			'status'  => 'error',
			'id'      => $id
		);	

		if($save)
		{
			$response = array(
				'message' => 'Berhasil disimpan',
				'status'  => 'success',
				'id'      => $header
			);
		}

		output_json($response);
	}

	public function delete_header()
	{
		$id = $this->input->post('id');
		$del = $this->pr_model->delete_header($id);
		$response = array(
			'message' => 'Gagal dihapus',
			'status'  => 'error',
		);	

		if($del)
		{
			$del_die = $this->pr_model->delete_detail('', $id);
			$response = array(
				'message' => 'Berhasil dihapus',
				'status'  => 'success',
			);
		}

		output_json($response);
	}

	public function delete_detail()
	{
		$id = $this->input->post('id');
		$del = $this->pr_model->delete_detail($id);
		$response = array(
			'message' => 'Gagal dihapus',
			'status'  => 'error',
		);	

		if($del)
		{
			$response = array(
				'message' => 'Berhasil dihapus',
				'status'  => 'success',
			);
		}

		output_json($response);
	}

	public function print_out($id = '')
	{
		$data_detail = $this->query_model->get_report_pr($id)->result();

		$this->twiggy->set('data_detail', $data_detail);
		$this->twiggy->display('admin/report/pr/layar');
	}

	private function generate_document_number($date = '')
	{
		$id = 'PRQ';
		$date_now = date('y/m');
		$date_from_data = date('y/m', strtotime($date));
		$numbering = add_zero(1, 4);

		// cek jika tahun bulan sama
		if($date_now == $date_from_data)
		{
			// cek data terakhir
			$get_last_data_pr = $this->pr_model->get_last_data_by_year_month('', 'PRQ-'.$date_now);
			if($get_last_data_pr)
			{
				// cek jika number sama
				if($numbering == $this->get_number_document_no($get_last_data_pr))
				{
					$numb = $numbering + 1;
					return $id.'-'.$date_now.'-'.add_zero($numb, 4);
				}
				else
				{
					$numb = $this->get_number_document_no($get_last_data_pr) + 1;
					return $id.'-'.$date_now.'-'.add_zero($numb, 4);
				}
			}
			else
			{
				return $id.'-'.$date_now.'-'.$numbering;
			}
		}

		return $id.'-'.$date_now.'-'.$numbering;
	}

	private function get_number_document_no($document_no)
	{
		$expl = explode('-', $document_no);
		return $expl[2];
	}

	private function generate_dies_id($vendorInitial, $component, $section_id, $machine_type, $hole, $year, $numbering)
	{
		return $vendorInitial.'.'.$component.'.'.$section_id.'.'.$machine_type.'.'.$hole.'.'.$year.add_zero($numbering, 4);
	}

	private function get_number_die_id($dies_id)
	{
		$expl = explode('.', $dies_id);
		return $expl[5];
	}

	public function get_last_hole_count()
	{
		$vendor_id = $this->input->post('vendor_id');
		$section_id = $this->input->post('section_id');
		$machine_type = $this->input->post('machine_type');
		$die_type = $this->input->post('die_type');
		$billet_type = $this->input->post('billet_type');

		$response = 1;

		$get_data = $this->pr_model->get_last_data_r($vendor_id, $section_id, $machine_type, $die_type, $billet_type)->row();
		if($get_data)
		{
			$response = $get_data->HoleCount;
		}
		else
		{
			$get_data2 = $this->pr_model->get_last_data($vendor_id, $section_id, $machine_type, $die_type, $billet_type)->row();
			if($get_data2)
			{
				$response = $get_data2->HoleCount;
			}
		}

		$this->output->set_output($response);
	}

	public function get_last_data($vendor_id, $section_id, $machine_type, $die_type, $billet_type, $year = '',  $show = 'DiesSeqNo')
	{

		if($year == '')
		{		
			$year = date('Y');
		}

		$response = '';

		$get_data = $this->pr_model->get_last_data($vendor_id, $section_id, $machine_type, $die_type, $billet_type, $year)->row_array();
		if($get_data)
		{
			if(array_key_exists($show, $get_data))
			{
				$response = $get_data[$show];
				return $response;
			}

			return '';
		}

		return '';
	}

	public function set_posted()
	{
		$id = $this->input->post('id');
		$data_save = array(
			'PostedDate' => date('Y-m-d H:i:s')
		);

		$save = $this->pr_model->update_header($id, $data_save);
		$response = array(
			'message' => 'Gagal posting',
			'status'  => 'error',
		);	

		if($save)
		{
			$response = array(
				'message' => 'Berhasil diposting',
				'status'  => 'success',
			);
		}

		output_json($response);
	}
}
?>