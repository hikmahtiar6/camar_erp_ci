<?php
/**
 * CLass Do
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */
class DeliveryOrder extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('master/do_model');
    }
    
    public function index()
    {
        $this->twiggy->display('admin/do/index');
    }
    
    /**
     * save all row
     */
    public function save_all()
    {
        $post = $this->input->post('post');
                
        foreach($post as $row)
        {
            $data_save = array(
                'CustomerName' => $row['customerName'],
                'Supplier'     => $row['supplier'],
                'DoContractNo' => $row['doContractNo'],
                'DoDate'       => change_format_date($row['doDate']),
                'Status'       => $row['status'],
                'RcvDate'      => change_format_date($row['rcvDate']),
                'RcvNo'        => $row['rcvNo'],
                'LineNo'       => $row['lineNo'],
                'Note'         => $row['catatan'],
                'DieType'      => $row['dieType'],
                'SubComp'      => $row['subComp'],
                'DiesId'       => $row['productCode'],
                'Size'      => $row['size'],
                'Unit'      => $row['unit'],
                'Qty'       => $row['qty'],
                'FinalIdx'  => $row['finalIdx'],
            );
            
            if($row['masterDoId'] == '')
            {
                $save = $this->do_model->save($data_save);
            }
            else
            {
                $save = $this->do_model->update($row['masterDoId'], $data_save);
            }
        }
        
        $response = array(
            'message' => 'Berhasil menyimpan',
            'status'  => 'success'
        );
    
        $this->output->set_output(json_encode($response));
        
        //var_dump($post);
    }
    
    /**
     * de;ete data do
     */
    public function delete()
    {
        $response = array(
            'message' => 'DO terpilih gagal dihapus',
            'status'  => 'danger'
        );
        
        $id = $this->input->post('id');
        
        $delete = $this->do_model->delete($id);
        if($delete)
        {
            $response = array(
                'message' => 'DO terpilih telah dihapus',
                'status'  => 'success'
            );
        }
        
        $this->output->set_output(json_encode($response));
    }
    
    /**
     * get data do
     */
    public function get_data()
    {
        $response = array();
        $data = $this->do_model->get_data()->result();
        
        if($data)
        {
            foreach($data as $row)
            {
                $do_date = $row->DoDate;
                $rcv_date = $row->RcvDate;
                if($do_date != "" || $do_date != null) {
                    $do_date = change_format_date($row->DoDate, $format = 'd/m/Y', $replace = "-", $changeTo = '/');
                }
                
                if($rcv_date != "" || $rcv_date != null) {
                    $rcv_date = change_format_date($row->RcvDate, $format = 'd/m/Y', $replace = "-", $changeTo = '/');
                }
                $response[] = array(
                    'masterDoId'   => $row->MasterDoId,
                    'customerName' => $row->CustomerName,
                    'supplier'     => $row->Supplier,
                    'doContractNo' => $row->DoContractNo,
                    'doDate'       => $do_date,
                    'status'       => $row->Status,
                    'rcvDate'      => $rcv_date,
                    'rcvNo'   => $row->RcvNo,
                    'lineNo'   => $row->LineNo,
                    'catatan'   => $row->Note,
                    'dieType'   => $row->DieType,
                    'subComp'   => $row->SubComp,
                    'productCode'   => $row->DiesId,
                    'size'  => $row->Size,
                    'unit'  => $row->Unit,
                    'qty'  => $row->Qty,
                    'finalIdx'  => $row->FinalIdx,
                );
            }
        }
        
        $this->output->set_output(json_encode($response));
    }
}
?>