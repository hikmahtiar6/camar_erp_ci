<?php
/**
 * CLass Po
 * 
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class Po extends CI_Controller {

	/**
	 * Codeigniter Constructur
	 */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('master/pr_model');
        $this->load->model('master/po_model');
        $this->load->model('master/die_model');
        $this->load->model('master/machine_model');
        $this->load->model('master/vendor_model');
    }
    
    /**
     * Index Page
     * 
     * @return HTML
     */
    public function index()
    {
    	$data_po_headers = $this->po_model->get_data_header()->result();

		$this->twiggy->set('data_headers', $data_po_headers);
        $this->twiggy->display('admin/po/index');
    }

    /**
     * Get data
     *
     * @return  JSON
     */
    public function get_data()
    {
    	// po number and data initial
    	$po_no = $this->input->post('po_no');
    	$response = array();

    	$get_data = $this->po_model->get_detail_advance('', $po_no)->result();

    	// jika data tersedia
    	// maka iterasi data
    	if($get_data)
    	{
    		foreach($get_data as $get_row)
    		{
    			$response[] = array(
    				'pod_id'        => $get_row->PurchaseOrderDetailId,
    				'component'     => $get_row->DieTypeComponentId,
    				'sub_component' => $get_row->DieSubTypeComponentId,
    				'dies_po_id'    => $get_row->DiesId,
    				'price_chn'     => ($get_row->PriceChn != null) ? to_decimal($get_row->PriceChn) : '',
    				'price_idr'     => ($get_row->PriceIdr != null) ? to_decimal($get_row->PriceIdr) : '',
    				'seq_no'        => $get_row->DiesSeqNo,
    				'section_id'    => $get_row->SectionId,
    				'section_desc'  => $get_row->SectionDescription
    			);
    		}
    	}

    	output_json($response);
    }

    public function edit($po_no = 'new')
    {
    	$pr_header = $this->pr_model->get_header_for_po();

		$document_no = $this->generate_document_number(date('Y-m-d'));
		$document_pr = '';
		$sub_no = '';
		$tgl_fix = date('d/m/Y');

		if($po_no != 'new')
		{
			$document_no = replaced_text($po_no, 'cmr', '/');

			$get_data = $this->po_model->get_data_header('', $document_no)->row();
			$tgl = ($get_data) ? $get_data->TransactionDate : $tgl_fix;
			$sub_no = ($get_data) ? $get_data->PurchaseOrderSubNo : '';
			$tgl_fix = date('d/m/Y', strtotime($tgl));

		}

		$this->twiggy->set('id', $po_no);
		$this->twiggy->set('posting', '');
		$this->twiggy->set('tgl', $tgl_fix);
		$this->twiggy->set('document_pr', $document_pr);
		$this->twiggy->set('document_no', $document_no);
		$this->twiggy->set('sub_no', $sub_no);
    	$this->twiggy->set('pr_header', $pr_header);
		$this->twiggy->display('admin/po/edit');
    }

    /**
     * Get Purchase Request No
     */
    public function get_pr_no()
    {
    	$po_no = $this->input->post('po_no');
    	$pr_header = $this->pr_model->get_header_for_po();
    	$response = array();
    	$data = array();
    	$data2 = array();
		
    	if($pr_header)
    	{
    		foreach($pr_header as $pr_row)
    		{
    			$data[] = array(
    				'value' => $pr_row->PurchaseRequestNo,
    				'text'  => $pr_row->PurchaseRequestNo
    			);
    		}
    	}

    	// jika post po_no tersedia
    	if($po_no)
    	{
    		$document_no = replaced_text($po_no, 'cmr', '/');

			$get_data = $this->po_model->get_data_header('', $document_no)->result();
			if($get_data)
	    	{
	    		foreach($get_data as $pr_row)
	    		{
	    			$data2[] = array(
	    				'value' => $pr_row->PurchaseRequestNo,
	    				'text'  => $pr_row->PurchaseRequestNo
	    			);
	    		}

    			usort($data2, function($a, $b) {
    				return $b['value'] <> $a['value'];
    			});

		    	$data = array_merge($data2, $data);
	    	}

    	}

    	$response['data'] = $data;
	    $response['used'] = $this->set_pr_used($data2);

    	output_json($response);
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
			if($id == 'new')
			{
				$data_save2 = array(
					//'PurchaseRequestHeaderId' => $document_pr,
					'PurchaseOrderSubNo'      => $no_sub_po,
					'PurchaseOrderNo'         => $document_no,
					'TransactionDate'         => change_format_date($tgl. date(' H:i:s')),
				);

				$data_save2['PurchaseRequestNo'] = $pr;
				$data_save2['DocumentDate'] = date('Y-m-d H:i:s');

				$save = $this->po_model->save_header($data_save2);

			}
			else
			{

			}
			
		}

		$json = array(
			'status' => $response,
			'id'     => $id
		);

		$this->output->set_output(json_encode($json));

	}

	/**
	 * Save Price
	 */
	public function save_price()
	{
		$id        = $this->input->post('dies_id');
		$price_idr = $this->input->post('price_idr');
		$price_chn = $this->input->post('price_chn');


		$data_update = array(
			'PriceChn' => $price_chn,
			'PriceIdr' => $price_idr
		);
		$update = $this->po_model->update_detail($id, $data_update);

		if($update) 
		{
			echo 'updated';
		}
		else
		{
			echo 'not updated';
		}


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

	/**
	 * Save PR -> PO (Sesuai Komponen)
	 * 
	 * @return JSON
	 */
	public function get_detail_po_from_pr()
	{
		$pr_no = $this->input->post('purchase_request_no');
		$po_no = $this->input->post('purchase_order_no');

		$response = array('status' => 'success');

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

		output_json($response);
	}

	/**
	 * Menhapus data detail
	 *
	 * @return JSon
	 */
	public function delete_detail()
	{
		$id = $this->input->post('id');

		// delete data
		$del = $this->po_model->delete_detail($id);

		if($del)
		{
			$response = array(
				'status'  => 'success',
				'message' => 'Berhasil menghapus PO terpilih'
			);
		}
		else
		{
			$response = array(
				'status'  => 'error',
				'message' => 'Gagal menghapus PO terpilih'
			);
		}

		output_json($response);
	}

	private function generate_dies_id($vendorInitial, $component, $section_id, $machine_type, $hole, $year, $numbering)
	{
		return $vendorInitial.'.'.$component.'.'.$section_id.'.'.$machine_type.'.'.$hole.'.'.$year.add_zero($numbering, 4);
	}

	private function set_pr_used($array)
	{
		$str = '';
		foreach($array as $row)
		{
			$str .= $row['value'].', ';
		}

		return rtrim($str, ', ');
	} 

}
?>