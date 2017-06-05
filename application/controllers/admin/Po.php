<?php
/**
 * CLass Po
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Po extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->load->model('master/pr_model');
        $this->load->model('master/po_model');
        $this->load->model('master/die_model');
        $this->load->model('master/machine_model');
        $this->load->model('master/vendor_model');
    }
    
    public function index()
    {
    	$data_po_headers = $this->po_model->get_data_header()->result();

		$this->twiggy->set('data_headers', $data_po_headers);
        $this->twiggy->display('admin/po/index');
    }

    public function edit($id = 'new')
    {
    	$pr_header = $this->pr_model->get_headers('po')->result();
		$document_no = $this->generate_document_number(date('Y-m-d'));
		$document_pr = '';
		$sub_no = '';
		$tgl = date('d/m/Y');

		if($id != 'new')
		{
			$get_last_data_pr_date = $this->po_model->get_last_data_by_year_month($id);
			$document_no = $get_last_data_pr_date;
			$tgl = $this->po_model->get_last_data_by_year_month($id, '', 'TransactionDate');
			$sub_no = $this->po_model->get_last_data_by_year_month($id, '', 'PurchaseOrderSubNo');
			$tgl = date('d/m/Y', strtotime($tgl));
		}

		$this->twiggy->set('id', $id);
		$this->twiggy->set('tgl', $tgl);
		$this->twiggy->set('document_pr', $document_pr);
		$this->twiggy->set('document_no', $document_no);
		$this->twiggy->set('sub_no', $sub_no);
    	$this->twiggy->set('pr_header', $pr_header);
		$this->twiggy->display('admin/po/edit');
    }

    public function save_header()
	{
		$id = 'new';
		$response = 'error';
		$id = $this->input->post('id');
		$tgl = $this->input->post('tgl');
		$no_sub_po = $this->input->post('po_no');
		$document_no = $this->input->post('document_no');
		$document_pr = $this->input->post('document_pr');

		$data_save = array(
			//'PurchaseRequestHeaderId' => $document_pr,
			'PurchaseOrderSubNo'      => $no_sub_po,
			'PurchaseOrderNo'         => $document_no,
			'TransactionDate'         => change_format_date($tgl. date(' H:i:s')),
		);

		//return 0;

		foreach($document_pr as $pr)
		{
			if($id != 'new')
			{
				$save = true;//$this->po_model->update_header($id, $data_save);
				if($save)
				{
					$response = 'success';
					$id = $id;
				}
			}
			else
			{
				$data_save['PurchaseRequestNo'] = $pr;
				$data_save['DocumentDate'] = date('Y-m-d H:i:s');
				$save = $this->po_model->save_header($data_save);
				if($save)
				{
					$update_pr['IsUsed'] = 1;
					$save_pr = $this->pr_model->update_header('', $update_pr, $pr);

					$response = 'success';
					$id = $this->db->insert_id();
				}
			}
		}


		/*if($id != 'new')
		{
			$save = $this->po_model->update_header($id, $data_save);
			if($save)
			{
				$response = 'success';
				$id = $id;
			}
		}
		else
		{
			$data_save['DocumentDate'] = date('Y-m-d H:i:s');
			$save = $this->po_model->save_header($data_save);
			if($save)
			{
				$response = 'success';
				$id = $this->db->insert_id();
			}
		}*/

		$json = array(
			'status' => $response,
			'id'     => $id
		);

		$this->output->set_output(json_encode($json));

	}

	public function delete_header()
	{
		$id = $this->input->post('id');
		$del = $this->po_model->delete_header($id);
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

    private function generate_document_number($date = '')
	{
		$id = 'PO';
		$date_now = date('y/m');
		$date_from_data = date('y/m', strtotime($date));
		$numbering = add_zero(1, 4);

		// cek jika tahun bulan sama
		if($date_now == $date_from_data)
		{
			// cek data terakhir
			$get_last_data_pr = $this->po_model->get_last_data_by_year_month('', 'PO-'.$date_now);
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
	
	public function get_header_pr_by_po_id($po_id)
	{
		$response = '';

		$po_no_document = '';
		$get_po_document = $this->po_model->get_data_header($po_id)->row();
		if($get_po_document)
		{
			$po_no_document = $get_po_document->PurchaseOrderNo;
		}

		$get_pr_no_document = $this->po_model->get_data_header('', $po_no_document)->result();
		if($get_pr_no_document)
		{
			foreach($get_pr_no_document as $row)
			{
				$response[] = array(
					'text'  => $row->PurchaseRequestNo,
					'value' => $row->PurchaseRequestNo,
					'id'    => $row->PurchaseRequestNo,
				);

			}
			$response = super_unique_die($response);
		}

		output_json($response);
	}

	public function get_detail_po_from_pr()
	{
		$pr_no = $this->input->post('purchase_request_no');
		$po_no = $this->input->post('purchase_order_no');

		foreach($pr_no as $pr)
		{
			$get_pr = $this->pr_model->get_detail_by_header('', $pr);

			if($get_pr)
			{
				foreach($get_pr as $row_pr)
				{
					$get_initial_vendor = $this->vendor_model->get_data($row_pr->VendorId)->row();
					$get_machine_type = $this->machine_model->get_data_type_id($row_pr->MachineTypeId)->row();

					$pr_detail_id = $row_pr->DiePurchaseRequestDetailId;
					$dies_req_id = $row_pr->DiesId;
					$seq_no = $row_pr->DiesSeqNo;
					$section_id = $row_pr->SectionId;
					$die_type_comp_id = $row_pr->DieTypeComponentId;
					$year = $row_pr->DiesYear;
					$numbering = $row_pr->DiesSeqNo;
					$hole = $row_pr->HoleCount;
					$vendor_initial = ($get_initial_vendor) ? $get_initial_vendor->Initial : '';
					$machine_type_initial = ($get_machine_type) ? $get_machine_type->Initial : '';
					$billet_type_id = $row_pr->BilletTypeId;

					// get type component
					$get_sub_component = $this->die_model->get_data_component($die_type_comp_id);
					if($get_sub_component)
					{
						foreach($get_sub_component as $row_sub)
						{
							$sub_component = $row_sub->DieTypeComponentId;
							$dies_final = $this->generate_dies_id($vendor_initial, $sub_component, $section_id, $machine_type_initial, $hole, substr($year, 2, 2), $numbering);

							$po_detail_save['PurchaseOrderNo'] = $po_no;
							$po_detail_save['PurchaseRequestDetailId'] = $pr_detail_id;
							$po_detail_save['SectionId'] = $section_id;
							$po_detail_save['DiesRequestId'] = $dies_req_id;
							$po_detail_save['DiesId'] = $dies_final;
							$po_detail_save['DieTypeComponentId'] = $die_type_comp_id;
							$po_detail_save['DieSubTypeComponentId'] = $sub_component;
							$po_detail_save['BilletTypeId'] = $billet_type_id;
							$po_detail_save['MachineTypeId'] = $machine_type_initial;
							$po_detail_save['HoleCount'] = $hole;
							$po_detail_save['DiesSeqNo'] = $seq_no;
							$po_detail_save['DiesYear'] = $year;

							$get_detail_by_dies = $this->po_model->get_data_detail('','', $dies_final)->row();
							if($get_detail_by_dies)
							{
								$save = $this->po_model->update_detail($dies_final, $po_detail_save);
							}
							else
							{
								$save = $this->po_model->save_detail($po_detail_save);
							}

						}	 
					}
				}
			}
		}
	}

	private function generate_dies_id($vendorInitial, $component, $section_id, $machine_type, $hole, $year, $numbering)
	{
		return $vendorInitial.'.'.$component.'.'.$section_id.'.'.$machine_type.'.'.$hole.'.'.$year.add_zero($numbering, 4);
	} 

}
?>