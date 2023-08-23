<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: japasys Ltd
# Author link: https://www.japasys.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Invoice_model extends CI_Model
{

    public function get_bank($param = '')
    {


        $this->db->select('*');
        $this->db->from('bank_add');
        $this->db->where('status', '1');
        $this->db->order_by('bank_name', 'ASC');
        $query = $this->db->get();

        return $query->result_array();
    }
    public function get_sales($param = '')
    {

        $attributes = array('ENGINE' => 'InnoDB');
        $this->db->select('u.*,su.roleid');
        $this->db->from('users u');
        $this->db->join('sec_userrole su', 'su.user_id = u.user_id', 'left');
        $this->db->where('su.roleid', '5');
        $this->db->where('u.status', '1');
        if ($param) {
            $this->db->where('u.user_id', $param);
        }
        $this->db->order_by('u.first_name', 'ASC');
        $query = $this->db->get();

        return $query->result_array();
    }
    public function get_salesadmin($param = '')
    {

        $attributes = array('ENGINE' => 'InnoDB');
        $this->db->select('u.*,su.roleid');
        $this->db->from('users u');
        $this->db->join('sec_userrole su', 'su.user_id = u.user_id', 'left');
        if ($param) {
            $this->db->where('u.user_id', $param);
        }
        $this->db->order_by('u.first_name', 'ASC');
        $query = $this->db->get();

        return $query->result_array();
    }
    public function get_period($param = '')
    {
        $this->db->select('*');
        $this->db->from('target_period');
        if ($param) {
            $this->db->where('id', $param);
        }
        $this->db->order_by('start_date', 'DESC');
        $query = $this->db->get();

        return $query->result_array();
    }
    public function get_target_product($param = '')
    {
        $this->db->select('*');
        $this->db->from('target_product');
        if ($param) {
            $this->db->where('period_id', $param);
        }
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();

        return $query->result_array();
    }
    public function get_target_amount($param = '')
    {
        $this->db->select('*');
        $this->db->from('target_amount');
        if ($param) {
            $this->db->where('period_id', $param);
        }
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();

        return $query->result_array();
    }
    public function get_target_product_group($param = '')
    {
        $this->db->group_by('product_sku');
        if ($param) {
            $this->db->where('period_id', $param);
        }

        $query = $this->db->get('target_product');

        return $query->result_array();
    }
    public function get_target_product_bysku_bysalesid($param = '', $param2 = '', $param3)
    {
        $this->db->select('*');
        $this->db->where('product_sku', $param);
        $this->db->where('sales_id', $param2);
        $this->db->where('period_id', $param3);
        $query = $this->db->get('target_product');

        return $query->row();
    }
    public function get_product_by_sku($sku = '')
    {

        $this->db->where('product_id', $sku);
        $query = $this->db->get('product_information');

        return $query->row();
    }
    public function target_period_delete($param = '')
    {
        $this->db->where('id', $param);
        $this->db->delete('target_period');

        $this->db->where('period_id', $param);
        $this->db->delete('target_product');

        $this->db->where('period_id', $param);
        $this->db->delete('target_amount');
    }
    public function target_delete($param = '', $param2)
    {
        $this->db->where('period_id', $param);
        $this->db->where('product_sku', $param2);
        $this->db->delete('target_product');
    }
    public function target_amount_delete($param = '', $param2)
    {
        $this->db->where('period_id', $param);
        $this->db->delete('target_amount');
    }
    public function add_target_period()
    {
        $period          = $this->input->post('period', TRUE);
        $start_date          = $this->input->post('from_date', TRUE);
        $end_date            = $this->input->post('to_date', TRUE);


        $date = strtotime($start_date);
        $dat = date('Y', $date);
        $month = date('F', $date);
        if ($period != $month) {
            $this->session->set_flashdata(array('exception' => 'Pastikan rentan waktu sesuai dengan periode yang ditentukan! '));
            redirect('target_invoice');
        } else {
            $this->db->where('period', $period);
            $this->db->like('start_date', $dat, 'both');
            $get_periode = $this->db->get('target_period')->row();
            if ($get_periode) {
                $this->session->set_flashdata(array('exception' => 'Periode <b>' . $period . '-' . $dat . '</b> sudah tersedia'));
                redirect('target_invoice');
            } else {
                $data = array(
                    'period'          => $period,
                    'start_date'      => $start_date,
                    'end_date'        => $end_date,
                );
                $this->db->insert('target_period', $data);
            }
        }
    }
    public function add_target_product()
    {
        $period_id              = $this->input->post('period_id', TRUE);
        $sku                    = $this->input->post('product_id', TRUE);
        $sales_id               = $this->input->post('sales_id', TRUE);
        $target_qty               = $this->input->post('target_qty', TRUE);

        $this->db->where('period_id', $period_id);
        $this->db->where('product_sku', $sku);
        $cekTargetProduct = $this->db->get('target_product')->row();

        if ($cekTargetProduct->product_sku) {

            $this->session->set_flashdata(array('exception' => $this->input->post('product_name') . ' sudah masuk dalam target'));
            redirect('target_target/' . $period_id);
        } else {

            foreach ($sales_id as $key => $val) {
                $arr[] = array(
                    'period_id' => $period_id,
                    'product_sku' => $sku,
                    'sales_id' => $val,
                    'qty' => $target_qty[$key]
                );
            }
            $this->session->set_flashdata(array('message' => $this->input->post('product_name') . ' berhasil masuk dalam target'));
        }

        $this->db->insert_batch('target_product', $arr);
    }
    public function add_target_amount()
    {
        $period_id              = $this->input->post('period_id', TRUE);
        $sales_id               = $this->input->post('sales_id', TRUE);
        $amount               = $this->input->post('amount', TRUE);

        $this->db->where('period_id', $period_id);
        $cekTargetProduct = $this->db->get('target_amount')->row();

        if ($cekTargetProduct->period_id) {

            $this->session->set_flashdata(array('exception' =>  'Target pendapatan diperiode ini sudah ada'));
            redirect('target_amount/' . $period_id);
        } else {

            foreach ($sales_id as $key => $val) {
                $arr[] = array(
                    'period_id' => $period_id,
                    'sales_id' => $val,
                    'amount' => $amount[$key]
                );
            }
            $this->session->set_flashdata(array('message' => ' berhasil menargetkan pendapatan'));
        }
        // echo '<pre>';
        // var_dump($arr);
        // echo '</pre>';
        // die;
        $this->db->insert_batch('target_amount', $arr);
    }

    public function customer_list()
    {
        $query = $this->db->select('*')
            ->from('customer_information')
            ->where('status', '1')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function tax_fileds()
    {
        return $taxfield = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();
    }

    public function pos_customer_setup()
    {
        $query = $this->db->select('*')
            ->from('customer_information')
            ->where('customer_name', 'Walking Customer')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function allproduct()
    {
        $this->db->select('*');
        $this->db->from('product_information');
        $this->db->order_by('product_name', 'asc');
        $this->db->limit(30);
        $query   = $this->db->get();
        $itemlist = $query->result();
        return $itemlist;
    }

    public function vat_tax_setting()
    {
        $this->db->select('*');
        $this->db->from('vat_tax_setting');
        $query   = $this->db->get();
        return $query->row();
    }


    public function todays_invoice()
    {
        $this->db->select('a.*,b.customer_name');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $this->db->where('a.date', date('Y-m-d'));
        $this->db->order_by('a.invoice', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function customer_dropdown()
    {
        $data = $this->db->select("*")
            ->from('customer_information')
            ->get()
            ->result();

        $list[''] = 'Select Customer';
        if (!empty($data)) {
            foreach ($data as $value)
                $list[$value->customer_id] = $value->customer_name;
            return $list;
        } else {
            return false;
        }
    }

    public function customer_search($customer_id)
    {
        $query = $this->db->select('*')
            ->from('customer_information')
            ->group_start()
            ->like('customer_name', $customer_id)
            ->or_like('customer_mobile', $customer_id)
            ->group_end()
            ->limit(30)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function count_invoice()
    {
        return $this->db->count_all("invoice");
    }

    public function getInvoiceList($postData = null)
    {
        $response = array();
        $usertype = $this->session->userdata('user_type');
        $fromdate = $this->input->post('fromdate', TRUE);
        $todate   = $this->input->post('todate', TRUE);
        if (!empty($fromdate)) {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        ## Read value
        $draw         = $postData['draw'];
        $start        = $postData['start'];
        $rowperpage   = $postData['length']; // Rows display per page
        $columnIndex  = $postData['order'][0]['column']; // Column index
        $columnName   = $postData['columns'][$columnIndex]['data'];
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue  = $postData['search']['value']; // Search value

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (b.customer_name like '%" . $searchValue . "%' or a.invoice like '%" . $searchValue . "%' or a.date like'%" . $searchValue . "%' or a.invoice_id like'%" . $searchValue . "%' or u.first_name like'%" . $searchValue . "%'or u.last_name like'%" . $searchValue . "%')";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $this->db->join('users u', 'u.user_id = a.sales_by', 'left');
        if ($usertype == 2) {
            $this->db->where('a.sales_by', $this->session->userdata('user_id'));
        }
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);

        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $this->db->join('users u', 'u.user_id = a.sales_by', 'left');
        if ($usertype == 2) {
            $this->db->where('a.sales_by', $this->session->userdata('user_id'));
        }
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);

        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,b.customer_name,u.first_name,u.last_name");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $this->db->join('users u', 'u.user_id = a.sales_by', 'left');
        if ($usertype == 2) {
            $this->db->where('a.sales_by', $this->session->userdata('user_id'));
        }
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);

        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {
            $button = '';
            $prints = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";

            $prints .= '  <a href="' . $base_url . 'invoice_details/' . $record->invoice_id . '" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('invoice') . '"><i class="fa fa-window-restore" aria-hidden="true"></i></a>';

            $prints .= '  <a href="' . $base_url . 'invoice_pad_print/' . $record->invoice_id . '" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('pad_print') . '"><i class="fa fa-fax" aria-hidden="true"></i></a>';

            $prints .= '  <a href="' . $base_url . 'pos_print/' . $record->invoice_id . '" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('pos_invoice') . '"><i class="fa fa-fax" aria-hidden="true"></i></a>';
            if ($this->permission1->method('manage_invoice', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'invoice_edit/' . $record->invoice_id . '" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
            }
            $button .= '  <a href="' . $base_url . 'inv_delete/' . $record->invoice_id . '/' . number_format($record->total_amount, 0, '.', '') . '" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('cancel') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';


            $details = '  <a href="' . $base_url . 'invoice_details/' . $record->invoice_id . '" class="" >' . $record->invoice . '</a>';

            $data[] = array(
                'sl'               => $sl,
                'invoice'          => $details,
                'salesman'         => $record->first_name . ' ' . $record->last_name,
                'customer_name'    => $record->customer_name,
                'final_date'       => date("d-M-Y", strtotime($record->date)),
                'due_date'         => date("d-M-Y", strtotime($record->due_date)),
                'total_amount'     => $record->total_amount,
                'button'           => $button,
                'prints'           => $prints,

            );
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }


    public function invoice_taxinfo($invoice_id)
    {
        return $this->db->select('*')
            ->from('tax_collection')
            ->where('relation_id', $invoice_id)
            ->get()
            ->result_array();
    }

    public function retrieve_invoice_editdata($invoice_id)
    {
        $this->db->select('a.*, sum(c.quantity) as sum_quantity, a.total_tax as taxs,a. prevous_due,b.customer_name,c.*,c.tax as total_tax,c.product_id,d.product_name,d.product_model,d.tax,d.unit,d.*');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('invoice_details c', 'c.invoice_id = a.invoice_id');
        $this->db->join('product_information d', 'd.product_id = c.product_id');
        $this->db->where('a.invoice_id', $invoice_id);
        $this->db->group_by('d.product_id');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function pmethod_dropdown()
    {
        $data = $this->db->select('*')
            ->from('acc_coa')
            ->where_in('PHeadName', ['Cash In Boxes', 'Cash in Banks']) //Perubahan : sebelumnya where(Pheadname,cash in boxes)
            ->get()
            ->result();

        $list[''] = 'Select Method';
        if (!empty($data)) {
            $list[0] = 'Credit Sales'; //perubahaan - before Credit Sale
            foreach ($data as $value)
                $list[$value->HeadCode] = $value->HeadName;
            return $list;
        } else {
            return false;
        }
    }
    public function pmethod_dropdown_new()
    {
        $data = $this->db->select('*')
            ->from('acc_coa')
            ->where('PHeadName', 'Cash In Boxes')
            ->get()
            ->result();

        $list[''] = 'Select Method';
        if (!empty($data)) {

            foreach ($data as $value)
                $list[$value->HeadCode] = $value->HeadName;
            return $list;
        } else {
            return false;
        }
    }
    //perubahan : penambahan func delete invoice
    public function invoice_delete($invoice_id = "", $total_price = "")
    {
        $invoice_info = $this->db->select('*')->from('invoice')->where('invoice_id', $invoice_id)->get()->row();
        $customer_info = $this->db->select('*')->from('customer_information')->where('customer_id', $invoice_info->customer_id)->get()->row();

        //delete acc transaction VNo
        $this->db->where('VNo', $invoice_id);
        $this->db->delete("acc_transaction");
        //kembalikan limit saldo

        $limitsaldo = array(
            'limit' => $customer_info->limit + $total_price
        );

        $this->db->where('customer_id', $customer_info->customer_id);
        $this->db->update('customer_information', $limitsaldo);
        //delete invoice detail
        $this->db->where('invoice_id', $invoice_id);
        $this->db->delete("invoice_details");
        //delete invoice 
        $this->db->where('invoice_id', $invoice_id);
        $this->db->delete("invoice");
    }
    public function invoice_entry()
    {
        $tablecolumn         = $this->db->list_fields('tax_collection');
        $num_column          = count($tablecolumn) - 4;
        $invoice_id          = $this->generator(10);
        $invoice_id          = strtoupper($invoice_id);
        $createby            = $this->session->userdata('id');
        $createdate          = date('Y-m-d H:i:s');
        $product_id          = $this->input->post('product_id');
        $currency_details    = $this->db->select('*')->from('web_setting')->get()->result_array();
        $quantity            = $this->input->post('product_quantity', TRUE);
        $invoice_no_generated = $this->input->post('invoic_no');
        $changeamount        = $this->input->post('change', TRUE);
        $multipayamount      = $this->input->post('pamount_by_method', TRUE);
        $multipaytype        = $this->input->post('multipaytype', TRUE);
        $paidamount          = $this->input->post('paid_amount', TRUE);


        $bank_id = $this->input->post('bank_id', TRUE);
        if (!empty($bank_id)) {
            $bankname = $this->db->select('bank_name')->from('bank_add')->where('bank_id', $bank_id)->get()->row()->bank_name;

            $bankcoaid = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName', $bankname)->get()->row()->HeadCode;
        } else {
            $bankcoaid = '';
        }
        $available_quantity = $this->input->post('available_quantity', TRUE);
        $result = array();
        foreach ($available_quantity as $k => $v) {
            if ($v < $quantity[$k]) {
                $this->session->set_userdata(array('error_message' => display('you_can_not_buy_greater_than_available_qnty')));
                redirect('Cinvoice');
            }
        }

        $customer_id = $this->input->post('customer_id', TRUE);

        //Full or partial Payment record.
        $paid_amount    = $this->input->post('paid_amount', TRUE);
        $transection_id = $this->generator(8);
        $tax_v = 0;
        for ($j = 0; $j < $num_column; $j++) {
            $taxfield        = 'tax' . $j;
            $taxvalue        = 'total_tax' . $j;
            $taxdata[$taxfield] = $this->input->post($taxvalue);
            $tax_v    += $this->input->post($taxvalue);
        }
        $taxdata['customer_id'] = $customer_id;
        $taxdata['date']        = (!empty($this->input->post('invoice_date', TRUE)) ? $this->input->post('invoice_date', TRUE) : date('Y-m-d'));
        $taxdata['relation_id'] = $invoice_id;
        if ($tax_v > 0) {
            $this->db->insert('tax_collection', $taxdata);
        }

        if ($multipaytype[0] == 0) {
            $is_credit = 1;
        } else {
            $is_credit = '';
        }

        $fixordyn = $this->db->select('*')->from('vat_tax_setting')->get()->row();
        $is_fixed   = '';
        $is_dynamic = '';

        if ($fixordyn->fixed_tax == 1) {
            $is_fixed   = 1;
            $is_dynamic = 0;
            $paid_tax = $this->input->post('total_vat_amnt', TRUE);
        } elseif ($fixordyn->dynamic_tax == 1) {
            $is_fixed   = 0;
            $is_dynamic = 1;
            $paid_tax = $this->input->post('total_tax', TRUE);
        }
        //Data inserting into invoice table
        $datainv = array(
            'invoice_id'      => $invoice_id,
            'customer_id'     => $customer_id,
            'date'            => (!empty($this->input->post('invoice_date', TRUE)) ? $this->input->post('invoice_date', TRUE) : date('Y-m-d')),
            'due_date'        => (!empty($this->input->post('due_date', TRUE)) ? $this->input->post('due_date', TRUE) : date('Y-m-d')), // perubahan : penambahan field due_date
            'total_amount'    => $this->input->post('grand_total_price', TRUE),
            'total_tax'       => $this->input->post('total_tax', TRUE),
            'invoice'         => $this->input->post('invoice_no', TRUE),
            'invoice_details' => (!empty($this->input->post('inva_details', TRUE)) ? $this->input->post('inva_details', TRUE) : 'Terimakasih telah berbelanja di tempat kami'),
            'invoice_discount' => $this->input->post('invoice_discount', TRUE),
            'total_discount'  => $this->input->post('total_discount', TRUE),
            'total_vat_amnt'  => $this->input->post('total_vat_amnt', TRUE),
            // 'total_tax'     => $this->input->post('total_tax', TRUE), //perubahan : penambahan line total tax. sebelumnya ga ada
            'paid_amount'     => $this->input->post('paid_amount', TRUE),
            'due_amount'      => $this->input->post('due_amount', TRUE),
            'prevous_due'     => $this->input->post('previous', TRUE),
            'shipping_cost'   => $this->input->post('shipping_cost', TRUE),
            'sales_by'        => $this->input->post('sales_by', TRUE) ?: $this->session->userdata('id'), //perubahan : sebelumnya hanya $this->session->userdata('id')
            'admin_by'        => $this->session->userdata('id'), //perubahan : penambahan line admin_by. sebelumnya ga ada
            'status'          => 1,
            'payment_type'    => 1,
            'bank_id'         => (!empty($this->input->post('bank_id', TRUE)) ? $this->input->post('bank_id', TRUE) : null),
            'is_credit'       => $is_credit,
            'is_fixed'        => $is_fixed,
            'is_dynamic'      => $is_dynamic,
        );

        $this->db->insert('invoice', $datainv);
        $prinfo  = $this->db->select('product_id,Avg(rate) as product_rate')->from('product_purchase_details')->where_in('product_id', $product_id)->group_by('product_id')->get()->result();
        $purchase_ave = [];
        $i = 0;
        foreach ($prinfo as $avg) {
            $purchase_ave[] =  $avg->product_rate * $quantity[$i];
            $i++;
        }
        $sumval   = array_sum($purchase_ave);
        $cusifo   = $this->db->select('*')->from('customer_information')->where('customer_id', $customer_id)->get()->row();
        $headn    = $customer_id . '-' . $cusifo->customer_name;
        $coainfo  = $this->db->select('*')->from('acc_coa')->where('HeadName', $headn)->get()->row();
        $customer_headcode = $coainfo->HeadCode;
        // Cash in Hand debit
        $cc = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  111000001,
            'Narration'      =>  'Cash in Hand in Sale for Invoice No - ' . $invoice_no_generated . ' customer- ' . $cusifo->customer_name,
            'Debit'          =>  $paidamount,
            'Credit'         =>  0,
            'IsPosted'       =>  1,
            'CreateBy'       =>  $createby,
            'CreateDate'     =>  $createdate,
            'IsAppove'       =>  1
        );

        // bank ledger
        $bankc = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  $bankcoaid,
            'Narration'      =>  'Paid amount for customer  Invoice No - ' . $invoice_no_generated . ' customer -' . $cusifo->customer_name,
            'Debit'          =>  $paidamount,
            'Credit'         =>  0,
            'IsPosted'       =>  1,
            'CreateBy'       =>  $createby,
            'CreateDate'     =>  $createdate,
            'IsAppove'       =>  1
        );

        ///Inventory credit
        $coscr = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  1141,
            'Narration'      =>  'Inventory credit For Invoice No' . $invoice_no_generated,
            'Debit'          =>  0,
            'Credit'         =>  $sumval, //purchase price asbe
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $coscr);

        // Customer Transactions
        //Customer debit for Product Value
        $cosdr = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  $customer_headcode,
            'Narration'      =>  'Customer debit For Invoice No -  ' . $invoice_no_generated . ' Customer ' . $cusifo->customer_name,
            'Debit'          =>  $this->input->post('n_total', TRUE) - (!empty($this->input->post('previous', TRUE)) ? $this->input->post('previous', TRUE) : 0),
            'Credit'         =>  0,
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $cosdr);

        $total_saleamnt = $this->input->post('n_total', TRUE) - (!empty($this->input->post('previous', TRUE)) ? $this->input->post('previous', TRUE) : 0);
        $withoutinventory = $total_saleamnt - $sumval;
        $income = $total_saleamnt - (!empty($this->input->post('total_tax', TRUE)) ? $this->input->post('total_tax', TRUE) : $this->input->post('total_vat_amnt', TRUE));

        $pro_sale_income = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  511001,
            'Narration'      =>  'Product Sales revenue For Invoice NO - ' . $invoice_no_generated . ' Customer ' . $cusifo->customer_name,
            'Debit'          =>  0,
            'Credit'         =>  $income,
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $pro_sale_income);

        $tax_info = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  2114,
            'Narration'      =>  'Value added tax For Invoice NO - ' . $invoice_no_generated . ' Customer ' . $cusifo->customer_name,
            'Debit'          =>  0,
            'Credit'         =>  $paid_tax,
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $tax_info);

        ///Customer credit for Paid Amount

        $cuscredit = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  $customer_headcode,
            'Narration'      =>  'Customer credit for Paid Amount For Customer Invoice NO- ' . $invoice_no_generated . ' Customer- ' . $cusifo->customer_name,
            'Debit'          =>  0,
            'Credit'         =>  $paidamount,
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        if ($multipaytype[0] != 0) {
            if ($this->input->post('paid_amount', TRUE) > 0) {
                $this->db->insert('acc_transaction', $cuscredit);
            }
        }

        $i = 0;
        if ($multipaytype[0] != 0) {
            foreach ($multipaytype  as $multipaytype) {

                $paymethod = array(
                    'VNo'            =>  $invoice_id,
                    'Vtype'          =>  'INVOICEPayment',
                    'VDate'          =>  $createdate,
                    'COAID'          =>  $multipaytype,
                    'Narration'      =>  'Paid amount for customer  Invoice No - ' . $invoice_no_generated . ' customer -' . $cusifo->customer_name,
                    'Debit'          =>  $multipayamount[$i],
                    'Credit'         =>  0,
                    'IsPosted'       =>  1,
                    'CreateBy'       =>  $createby,
                    'CreateDate'     =>  $createdate,
                    'IsAppove'       =>  1
                );
                $this->db->insert('acc_transaction', $paymethod);
                $i++;
            }
        }


        $customerinfo = $this->db->select('*')->from('customer_information')->where('customer_id', $customer_id)->get()->row();
        $rate                = $this->input->post('product_rate', TRUE);
        $p_id                = $this->input->post('product_id', TRUE);
        $total_amount        = $this->input->post('total_price', TRUE);
        $discount_rate       = $this->input->post('discountvalue', TRUE);
        $discount_per        = $this->input->post('discount', TRUE);
        $discount_rate2       = $this->input->post('discountvalue2', TRUE);
        $discount_per2        = $this->input->post('discount2', TRUE);
        $discount_rate3       = $this->input->post('discountvalue3', TRUE);
        $discount_per3        = $this->input->post('discount3', TRUE);
        $vat_amnt            = $this->input->post('vatvalue', TRUE);
        $vat_amnt_pcnt       = $this->input->post('vatpercent', TRUE);
        $tax_amount          = $this->input->post('tax', TRUE);
        $invoice_description = $this->input->post('desc', TRUE);
        $serial_n            = $this->input->post('serial_no', TRUE);

        // perubahan : penambahan pengurangan limit saldo
        $limitsaldo = array(
            'limit' => $customerinfo->limit - $this->input->post('grand_total_price', TRUE)
        );

        $this->db->where('customer_id', $customer_id);
        $this->db->update('customer_information', $limitsaldo);

        for ($i = 0, $n = count($p_id); $i < $n; $i++) {
            $product_quantity = $quantity[$i];
            $product_rate     = $rate[$i];
            $product_id       = $p_id[$i];
            $serial_no        = (!empty($serial_n[$i]) ? $serial_n[$i] : null);
            $total_price      = $total_amount[$i];
            $supplier_rate    = $this->supplier_price($product_id);
            $disper           = $discount_per[$i];
            $discount         = $discount_rate[$i];
            $disper2           = $discount_per2[$i];
            $discount2         = $discount_rate2[$i];
            $disper3           = $discount_per3[$i];
            $discount3         = $discount_rate3[$i];
            $vatper           = $vat_amnt_pcnt[$i];
            $vatanmt          = $vat_amnt[$i];
            $tax              = ($tax_amount ? $tax_amount[$i] : 0);
            $description      = (!empty($invoice_description) ? $invoice_description[$i] : null);

            $data1 = array(
                'invoice_details_id' => $this->generator(15),
                'invoice_id'         => $invoice_id,
                'product_id'         => $product_id,
                'serial_no'          => '',
                'batch_id'           => $serial_no,
                'quantity'           => $product_quantity,
                'rate'               => $product_rate,
                'description'        => $description,
                'discount'           => $discount,
                'discount_per'       => $disper,
                'discount2'           => $discount2,
                'discount_per2'       => $disper2,
                'discount3'           => $discount3,
                'discount_per3'       => $disper3,
                'vat_amnt'           => $vatanmt,
                'vat_amnt_per'       => $vatper,
                'tax'                => $tax,
                'paid_amount'        => $paidamount,
                'due_amount'         => $this->input->post('due_amount', TRUE),
                'supplier_rate'      => $supplier_rate,
                'total_price'        => $total_price,
                'status'             => 1
            );
            if (!empty($quantity)) {
                $this->db->insert('invoice_details', $data1);
            }
        }
        $message = 'Mr.' . $customerinfo->customer_name . ',
        ' . 'You have purchase  ' . $this->input->post('grand_total_price', TRUE) . ' ' . $currency_details[0]['currency'] . ' You have paid .' . $this->input->post('paid_amount', TRUE) . ' ' . $currency_details[0]['currency'];

        $config_data = $this->db->select('*')->from('sms_settings')->get()->row();
        if ($config_data->isinvoice == 1) {
            $smsapi =   $this->smsgateway->send([
                'apiProvider' => 'nexmo',
                'username'    => $config_data->api_key,
                'password'    => $config_data->api_secret,
                'from'        => $config_data->from,
                'to'          => $customerinfo->customer_mobile,
                'message'     => $message
            ]);
        }
        return  $invoice_id;
    }


    public function update_invoice()
    {
        $tablecolumn = $this->db->list_fields('tax_collection');
        $num_column  = count($tablecolumn) - 4;
        $invoice_id  = $this->input->post('invoice_id', TRUE);
        $invoice_no  = $this->input->post('invoice', TRUE);
        $createby    = $this->session->userdata('id');
        $createdate  = date('Y-m-d H:i:s');
        $customer_id = $this->input->post('customer_id', TRUE);
        $quantity    = $this->input->post('product_quantity', TRUE);
        $product_id  = $this->input->post('product_id', TRUE);
        $multipayamount = $this->input->post('pamount_by_method', TRUE);
        $multipaytype = $this->input->post('multipaytype', TRUE);
        $changeamount = $this->input->post('change', TRUE);
        if ($changeamount > 0) {
            $paidamount = $this->input->post('n_total', TRUE);
        } else {
            $paidamount = $this->input->post('paid_amount', TRUE);
        }


        $bank_id = $this->input->post('bank_id', TRUE);
        if (!empty($bank_id)) {
            $bankname = $this->db->select('bank_name')->from('bank_add')->where('bank_id', $bank_id)->get()->row()->bank_name;

            $bankcoaid = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName', $bankname)->get()->row()->HeadCode;
        } else {
            $bankcoaid = '';
        }

        $transection_id = $this->generator(8);


        $this->db->where('VNo', $invoice_id);
        $this->db->delete('acc_transaction');
        $this->db->where('relation_id', $invoice_id);
        $this->db->delete('tax_collection');
        if ($multipaytype[0] == 0) {
            $is_credit = 1;
        } else {
            $is_credit = '';
        }

        $fixordyn = $this->db->select('*')->from('vat_tax_setting')->get()->row();

        if ($fixordyn->fixed_tax == 1) {

            $paid_tax = $this->input->post('total_vat_amnt', TRUE);
        } elseif ($fixordyn->dynamic_tax == 1) {

            $paid_tax = $this->input->post('total_tax', TRUE);
        }



        $data = array(
            'invoice_id'      => $invoice_id,
            'customer_id'     => $this->input->post('customer_id', TRUE),
            'date'            => $this->input->post('invoice_date', TRUE),
            'due_date'        => $this->input->post('due_date', TRUE), //perubahan : penambahan field due_date
            'total_amount'    => $this->input->post('grand_total_price', TRUE),
            'total_tax'       => $this->input->post('total_tax', TRUE),
            'invoice_details' => $this->input->post('inva_details', TRUE),
            'due_amount'      => $this->input->post('due_amount', TRUE),
            'paid_amount'     => $this->input->post('paid_amount', TRUE),
            'invoice_discount' => $this->input->post('invoice_discount', TRUE),
            'total_discount'  => $this->input->post('total_discount', TRUE),
            'total_vat_amnt'  => $this->input->post('total_vat_amnt', TRUE),
            'total_tax'  => $this->input->post('total_vat_amnt', TRUE), //perubahan : sebelumnya baris ini tidak ada
            'prevous_due'     => $this->input->post('previous', TRUE),
            'shipping_cost'   => $this->input->post('shipping_cost', TRUE),
            'payment_type'    =>  $this->input->post('paytype', TRUE),
            'bank_id'         => (!empty($this->input->post('bank_id', TRUE)) ? $this->input->post('bank_id', TRUE) : null),
            'is_credit'       =>  $is_credit,
            'sales_by'       =>  $this->input->post('sales_by'), //perubahan : sebelumnya baris ini tidak ada

        );



        $prinfo  = $this->db->select('product_id,Avg(rate) as product_rate')->from('product_purchase_details')->where_in('product_id', $product_id)->group_by('product_id')->get()->result();
        $purchase_ave = [];
        $i = 0;
        foreach ($prinfo as $avg) {
            $purchase_ave[] =  $avg->product_rate * $quantity[$i];
            $i++;
        }
        $sumval = array_sum($purchase_ave);

        $cusifo = $this->db->select('*')->from('customer_information')->where('customer_id', $customer_id)->get()->row();
        $headn = $customer_id . '-' . $cusifo->customer_name;
        $coainfo = $this->db->select('*')->from('acc_coa')->where('HeadName', $headn)->get()->row();
        $customer_headcode = $coainfo->HeadCode;
        // Cash in Hand debit
        $cc = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  111000001,
            'Narration'      =>  'Cash in Hand for sale for Invoice No -' . $invoice_no . ' Customer ' . $cusifo->customer_name,
            'Debit'          =>  $paidamount,
            'Credit'         =>  0,
            'IsPosted'       =>  1,
            'CreateBy'       =>  $createby,
            'CreateDate'     =>  $createdate,
            'IsAppove'       =>  1
        );


        //Inventory credit
        $coscr = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  1141,
            'Narration'      =>  'Inventory credit For Invoice No' . $invoice_no,
            'Debit'          =>  0,
            'Credit'         =>  $sumval, //purchase price asbe
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $coscr);

        // bank ledger
        $bankc = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  $bankcoaid,
            'Narration'      =>  'Paid amount for  Invoice NO- ' . $invoice_no . ' customer ' . $cusifo->customer_name,
            'Debit'          =>  $paidamount,
            'Credit'         =>  0,
            'IsPosted'       =>  1,
            'CreateBy'       =>  $createby,
            'CreateDate'     =>  $createdate,
            'IsAppove'       =>  1
        );

        /// Sale income
        $pro_sale_income = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  511001,
            'Narration'      =>  'Product Sales revenue For Invoice NO - ' . $invoice_no . ' Customer ' . $cusifo->customer_name,
            'Debit'          =>  0,
            'Credit'         =>  $this->input->post('n_total', TRUE) - (!empty($this->input->post('previous', TRUE)) ? $this->input->post('previous', TRUE) : 0),
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $pro_sale_income);
        //Customer debit for Product Value
        $cosdr = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  $customer_headcode,
            'Narration'      =>  'Customer debit For Invoice NO - ' . $invoice_no . ' customer-  ' . $cusifo->customer_name,
            'Debit'          =>  $this->input->post('grand_total_price', TRUE),
            'Credit'         =>  0,
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $cosdr);

        $tax_info = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  2114,
            'Narration'      =>  'Value added tax For Invoice NO - ' . $invoice_no . ' Customer ' . $cusifo->customer_name,
            'Debit'          =>  0,
            'Credit'         =>  $paid_tax,
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $tax_info);

        //Customer credit for Paid Amount
        $customer_credit = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'INV',
            'VDate'          =>  $createdate,
            'COAID'          =>  $customer_headcode,
            'Narration'      =>  'Customer credit for Paid Amount For Invoice No -' . $invoice_no . ' Customer ' . $cusifo->customer_name,
            'Debit'          =>  0,
            'Credit'         =>  $paidamount,
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );

        if ($invoice_id != '') {
            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('invoice', $data);
        }

        if ($multipaytype[0] != 0) {
            if (!empty($this->input->post('paid_amount', TRUE))) {
                $this->db->insert('acc_transaction', $customer_credit);
            }
        }

        $i = 0;
        if ($multipaytype[0] != 0) {
            foreach ($multipaytype  as $multipaytype) {

                $paymethod = array(
                    'VNo'            =>  $invoice_id,
                    'Vtype'          =>  'INVOICEPayment',
                    'VDate'          =>  $createdate,
                    'COAID'          =>  $multipaytype,
                    'Narration'      =>  'Paid amount for customer  Invoice No - ' . $invoice_no . ' customer -' . $cusifo->customer_name,
                    'Debit'          =>  $multipayamount[$i],
                    'Credit'         =>  0,
                    'IsPosted'       =>  1,
                    'CreateBy'       =>  $createby,
                    'CreateDate'     =>  $createdate,
                    'IsAppove'       =>  1
                );
                $this->db->insert('acc_transaction', $paymethod);
                $i++;
            }
        }



        for ($j = 0; $j < $num_column; $j++) {
            $taxfield = 'tax' . $j;
            $taxvalue = 'total_tax' . $j;
            $taxdata[$taxfield] = $this->input->post($taxvalue);
        }
        $taxdata['customer_id'] = $customer_id;
        $taxdata['date']        = (!empty($this->input->post('invoice_date', TRUE)) ? $this->input->post('invoice_date', TRUE) : date('Y-m-d'));
        $taxdata['relation_id'] = $invoice_id;
        $this->db->insert('tax_collection', $taxdata);

        // Inserting for Accounts adjustment.
        ############ default table :: customer_payment :: inflow_92mizdldrv #################

        $invoice_d_id  = $this->input->post('invoice_details_id', TRUE);
        $quantity      = $this->input->post('product_quantity', TRUE);
        $rate          = $this->input->post('product_rate', TRUE);
        $p_id          = $this->input->post('product_id', TRUE);
        $total_amount  = $this->input->post('total_price', TRUE);
        $discount_rate = $this->input->post('discountvalue', TRUE);
        $discount_per  = $this->input->post('discount', TRUE);
        $vat_amnt      = $this->input->post('vatvalue', TRUE);
        $vat_amnt_pcnt = $this->input->post('vatpercent', TRUE);
        $invoice_description = $this->input->post('desc', TRUE);
        $this->db->where('invoice_id', $invoice_id);
        $this->db->delete('invoice_details');
        $serial_n       = $this->input->post('serial_no', TRUE);
        for ($i = 0, $n = count($p_id); $i < $n; $i++) {
            $product_quantity = $quantity[$i];
            $product_rate     = $rate[$i];
            $product_id       = $p_id[$i];
            $serial_no        = $serial_n[$i];
            $total_price      = $total_amount[$i];
            $supplier_rate    = $this->supplier_price($product_id);
            $discount         = $discount_rate[$i];
            $vatper           = $vat_amnt_pcnt[$i];
            $vatanmt          = $vat_amnt[$i];
            $dis_per          = $discount_per[$i];
            $desciption        = $invoice_description[$i];
            if (!empty($tax_amount[$i])) {
                $tax = $tax_amount[$i];
            } else {
                $tax = $this->input->post('tax');
            }


            $data1 = array(
                'invoice_details_id' => $this->generator(15),
                'invoice_id'         => $invoice_id,
                'product_id'         => $product_id,
                'serial_no'          => '',
                'batch_id'           => $serial_no,
                'quantity'           => $product_quantity,
                'rate'               => $product_rate,
                'discount'           => $discount,
                'total_price'        => $total_price,
                'discount_per'       => $dis_per,
                'tax'                => $this->input->post('total_tax', TRUE),
                'vat_amnt'           => $vatanmt,
                'vat_amnt_per'       => $vatper,
                'paid_amount'        => $paidamount,
                'supplier_rate'     => $supplier_rate,
                'due_amount'         => $this->input->post('due_amount', TRUE),
                'description'       => $desciption,
            );
            $this->db->insert('invoice_details', $data1);





            $customer_id = $this->input->post('customer_id', TRUE);
        }

        return $invoice_id;
    }


    //POS invoice entry
    public function pos_invoice_setup($product_id)
    {
        $product_information = $this->db->select('*')
            ->from('product_information')
            ->join('supplier_product', 'product_information.product_id = supplier_product.product_id')
            ->where('product_information.product_id', $product_id)
            ->get()
            ->row();

        if ($product_information != null) {

            $this->db->select('SUM(a.quantity) as total_purchase');
            $this->db->from('product_purchase_details a');
            $this->db->where('a.product_id', $product_id);
            $total_purchase = $this->db->get()->row();

            $this->db->select('SUM(b.quantity) as total_sale');
            $this->db->from('invoice_details b');
            $this->db->where('b.product_id', $product_id);
            $total_sale = $this->db->get()->row();

            $available_quantity = ($total_purchase->total_purchase - $total_sale->total_sale);

            $data2 = (object) array(
                'total_product'  => $available_quantity,
                'supplier_price' => $product_information->supplier_price,
                'price'          => $product_information->price,
                'supplier_id'    => $product_information->supplier_id,
                'product_id'     => $product_information->product_id,
                'product_name'   => $product_information->product_name,
                'product_model'  => $product_information->product_model,
                'unit'           => $product_information->unit,
                'tax'            => $product_information->tax,
                'image'          => $product_information->image,
                'serial_no'      => $product_information->serial_no,
                'product_vat'      => $product_information->product_vat,
            );



            return $data2;
        } else {
            return false;
        }
    }



    public function searchprod($cid)
    {
        $this->db->select('*');
        $this->db->from('product_information');
        if ($cid != 'all') {
            $this->db->where('category_id', $cid);
        }
        $this->db->order_by('product_name', 'asc');
        $query   = $this->db->get();
        $itemlist = $query->result();
        if ($cid = '') {
            return false;
        } else {
            return $itemlist;
        }
    }
    public function searchprod_byname($pname = null)
    {
        $this->db->select('*');
        $this->db->from('product_information');
        $this->db->like('product_name', $pname);
        $this->db->order_by('product_name', 'asc');
        $this->db->limit(20);
        $query = $this->db->get();
        $itemlist = $query->result();
        return $itemlist;
    }


    public function walking_customer()
    {
        return $data = $this->db->select('*')->from('customer_information')->like('customer_name', 'walking', 'after')->get()->result_array();
    }

    public function category_dropdown()
    {
        $data = $this->db->select("*")
            ->from('product_category')
            ->get()
            ->result();

        $list = array('' => 'select_category');
        if (!empty($data)) {
            foreach ($data as $value)
                $list[$value->category_id] = $value->category_name;
            return $list;
        } else {
            return false;
        }
    }

    public function category_list()
    {
        $this->db->select('*');
        $this->db->from('product_category');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    //Retrieve company Edit Data
    public function retrieve_company()
    {
        $this->db->select('*');
        $this->db->from('company_information');
        $this->db->limit('1');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function retrieve_setting_editdata()
    {
        $this->db->select('*');
        $this->db->from('web_setting');
        $this->db->where('setting_id', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    //Get Supplier rate of a product
    public function supplier_rate($product_id)
    {
        $this->db->select('supplier_price');
        $this->db->from('supplier_product');
        $this->db->where(array('product_id' => $product_id));
        $query = $this->db->get();
        return $query->result_array();

        $this->db->select('Avg(rate) as supplier_price');
        $this->db->from('product_purchase_details');
        $this->db->where(array('product_id' => $product_id));
        $query = $this->db->get()->row();
        return $query->result_array();
    }

    public function supplier_price($product_id)
    {
        $this->db->select('supplier_price');
        $this->db->from('supplier_product');
        $this->db->where(array('product_id' => $product_id));
        $supplier_product = $this->db->get()->row();


        $this->db->select('Avg(rate) as supplier_price');
        $this->db->from('product_purchase_details');
        $this->db->where(array('product_id' => $product_id));
        $purchasedetails = $this->db->get()->row();
        $price = (!empty($purchasedetails->supplier_price) ? $purchasedetails->supplier_price : $supplier_product->supplier_price);

        return (!empty($price) ? $price : 0);
    }


    public function autocompletproductdata($product_name)
    {
        $query = $this->db->select('*')
            ->from('product_information')
            ->like('product_name', $product_name, 'both')
            ->or_like('product_model', $product_name, 'both')
            ->order_by('product_name', 'asc')
            ->limit(15)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function retrieve_invoice_html_data($invoice_id)
    {
        $this->db->select(
            'a.total_tax,
                        a.*,
                        b.*,
                        c.*,
                        d.product_id,
                        d.product_name,
                        d.product_details,
                        d.unit,
                        d.product_model,
                        a.paid_amount as paid_amount,
                        a.due_amount as due_amount'
        );
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.invoice_id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('product_information d', 'd.product_id = c.product_id');
        $this->db->where('a.invoice_id', $invoice_id);
        $this->db->where('c.quantity >', 0);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function user_invoice_data($user_id)
    {
        return  $this->db->select('*')->from('users')->where('user_id', $user_id)->get()->row();
    }

    // product information retrieve by product id
    public function get_total_product_invoic($product_id)
    {
        $this->db->select('SUM(a.quantity) as total_purchase');
        $this->db->from('product_purchase_details a');
        $this->db->where('a.product_id', $product_id);
        $total_purchase = $this->db->get()->row();

        $this->db->select('SUM(b.quantity) as total_sale');
        $this->db->from('invoice_details b');
        $this->db->where('b.product_id', $product_id);
        $total_sale = $this->db->get()->row();

        $this->db->select('a.*,b.*');
        $this->db->from('product_information a');
        $this->db->join('supplier_product b', 'a.product_id=b.product_id');
        $this->db->where(array('a.product_id' => $product_id, 'a.status' => 1));
        $product_information = $this->db->get()->row();

        $this->db->select('SUM(quantity) as purchase_qty,batch_id,product_id');
        $this->db->from('product_purchase_details');
        $this->db->where('product_id', $product_id);
        $this->db->group_by('batch_id');
        $pur_product_batch = $this->db->get()->result();

        $this->db->select('SUM(quantity) as sale_qty,batch_id');
        $this->db->from('invoice_details');
        $this->db->where('product_id', $product_id);
        $this->db->group_by('batch_id');
        $sell_product_batch = $this->db->get()->result();

        $available_quantity = ($total_purchase->total_purchase - $total_sale->total_sale);
        $tablecolumn = $this->db->list_fields('tax_collection');
        $num_column = count($tablecolumn) - 4;
        $taxfield = '';
        $taxvar = [];
        for ($i = 0; $i < $num_column; $i++) {
            $taxfield = 'tax' . $i;
            $data2[$taxfield] = (!empty($product_information->$taxfield) ? $product_information->$taxfield : 0);
            $taxvar[$i]       = (!empty($product_information->$taxfield) ? $product_information->$taxfield : 0);
            $data2['taxdta']  = $taxvar;
        }

        $content = explode(',', $product_information->serial_no);


        $html = "";
        if (empty($pur_product_batch)) {
            $html .= "No Serial Found !";
        } else {
            // Select option created for product
            $html .= "<select name=\"serial_no[]\" onchange=\"invoice_product_batch()\"  class=\"serial_no_1 form-control basic-single\" id=\"serial_no_1\">";
            $html .= "<option value=''>" . display('select_one') . "</option>";
            foreach ($pur_product_batch as $p_batch) {
                $sellt_prod_batch = $this->db->select('SUM(quantity) as sale_qty,batch_id, product_id')->from('invoice_details')->where('product_id', $p_batch->product_id)->where('batch_id', $p_batch->batch_id)->get()->row();
                $pur_prod = (empty($sellt_prod_batch->sale_qty) ? 0 : $sellt_prod_batch->sale_qty);
                $available_prod = $p_batch->purchase_qty - $pur_prod;
                if ($available_prod > 0) {
                    # code...
                    $html .= "<option value=" . $p_batch->batch_id . ">" . $p_batch->batch_id . "</option>";
                }
            }
            $html .= "</select>";
        }

        $data2['total_product']  = $available_quantity;
        $data2['supplier_price'] = $product_information->supplier_price;
        $data2['price']          = $product_information->price;
        $data2['supplier_id']    = $product_information->supplier_id;
        $data2['unit']           = $product_information->unit;
        $data2['tax']            = $product_information->tax;
        $data2['product_vat']    = $product_information->product_vat;
        $data2['serial']         = $html;
        $data2['txnmber']        = $num_column;


        return $data2;
    }

    public function generator($lenth)
    {
        $number = array("1", "2", "3", "4", "5", "6", "7", "8", "9");

        for ($i = 0; $i < $lenth; $i++) {
            $rand_value = rand(0, 8);
            $rand_number = $number["$rand_value"];

            if (empty($con)) {
                $con = $rand_number;
            } else {
                $con = "$con" . "$rand_number";
            }
        }
        return $con;
    }


    public function stock_qty_check($product_id)
    {
        $this->db->select('SUM(a.quantity) as total_purchase');
        $this->db->from('product_purchase_details a');
        $this->db->where('a.product_id', $product_id);
        $total_purchase = $this->db->get()->row();

        $this->db->select('SUM(b.quantity) as total_sale');
        $this->db->from('invoice_details b');
        $this->db->where('b.product_id', $product_id);
        $total_sale = $this->db->get()->row();

        $this->db->select('a.*,b.*');
        $this->db->from('product_information a');
        $this->db->join('supplier_product b', 'a.product_id=b.product_id');
        $this->db->where(array('a.product_id' => $product_id, 'a.status' => 1));
        $product_information = $this->db->get()->row();

        $available_quantity = ($total_purchase->total_purchase - $total_sale->total_sale);
        return (!empty($available_quantity) ? $available_quantity : 0);
    }


    public function japasys_invoice_pos_print_direct($invoice_id = null)
    {
        $invoice_detail = $this->retrieve_invoice_html_data($invoice_id);
        $taxfield = $this->db->select('*')
            ->from('tax_settings')
            ->where('is_show', 1)
            ->get()
            ->result_array();
        $txregname = '';
        foreach ($taxfield as $txrgname) {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity  = 0;
        $subTotal_cartoon   = 0;
        $subTotal_discount  = 0;
        $subTotal_ammount   = 0;
        $descript           = 0;
        $isserial           = 0;
        $is_discount        = 0;
        $is_dis_val         = 0;
        $vat_amnt_per       = 0;
        $vat_amnt           = 0;
        $isunit             = 0;
        if (!empty($invoice_detail)) {
            foreach ($invoice_detail as $k => $v) {
                $invoice_detail[$k]['final_date'] = $this->occational->dateConvert($invoice_detail[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $invoice_detail[$k]['quantity'];
                $subTotal_ammount = $subTotal_ammount + $invoice_detail[$k]['total_price'];
            }

            $i = 0;
            foreach ($invoice_detail as $k => $v) {
                $i++;
                $invoice_detail[$k]['sl'] = $i;
                if (!empty($invoice_detail[$k]['description'])) {
                    $descript = $descript + 1;
                }
                if (!empty($invoice_detail[$k]['serial_no'])) {
                    $isserial = $isserial + 1;
                }
                if (!empty($invoice_detail[$k]['unit'])) {
                    $isunit = $isunit + 1;
                }
                if (!empty($invoice_detail[$k]['discount_per'])) {
                    $is_discount = $is_discount + 1;
                }
                if (!empty($invoice_detail[$k]['discount'])) {
                    $is_dis_val = $is_dis_val + 1;
                }
                if (!empty($invoice_detail[$k]['vat_amnt_per'])) {
                    $vat_amnt_per = $vat_amnt_per + 1;
                }
                if (!empty($invoice_detail[$k]['vat_amnt'])) {
                    $vat_amnt = $vat_amnt + 1;
                }
            }
        }
        $bank       = $this->invoice_model->get_bank();
        $payment_method_list = $this->db->select('*')->from('acc_coa')->where('PHeadName', 'Cash In Boxes')->get()->result();
        $terms_list = $this->db->select('*')->from('seles_termscondi')->get()->result();
        $totalbal = $invoice_detail[0]['total_amount'] + $invoice_detail[0]['prevous_due'];
        $amount_inword = $this->numbertowords->convert_number($totalbal);
        $user_id  = $invoice_detail[0]['sales_by'];
        $currency_details = $this->retrieve_setting_editdata();
        $users    = $this->user_invoice_data($user_id);
        $admin    = $this->user_invoice_data($invoice_detail[0]['admin_by']);
        $data = array(
            'title'                => display('pos_print'),
            'invoice_id'           => $invoice_detail[0]['invoice_id'],
            'invoice_no'           => $invoice_detail[0]['invoice'],
            'customer_name'        => $invoice_detail[0]['customer_name'],
            'customer_address'     => $invoice_detail[0]['customer_address'],
            'customer_mobile'      => $invoice_detail[0]['customer_mobile'],
            'customer_email'       => $invoice_detail[0]['customer_email'],
            'final_date'           => $invoice_detail[0]['final_date'],
            'due_date'           => $invoice_detail[0]['due_date'],
            'invoice_details'      => $invoice_detail[0]['invoice_details'],
            'total_amount'         => number_format($totalbal, 0, '.', ','),
            'subTotal_cartoon'     => $subTotal_cartoon,
            'subTotal_quantity'    => $subTotal_quantity,
            'invoice_discount'     => number_format($invoice_detail[0]['invoice_discount'], 0, '.', ','),
            'total_discount'       => number_format($invoice_detail[0]['total_discount'], 0, '.', ','),
            'total_tax'            => number_format($invoice_detail[0]['total_tax'], 0, '.', ','),
            'subTotal_ammount'     => number_format($subTotal_ammount, 0, '.', ','),
            'paid_amount'          => number_format($invoice_detail[0]['paid_amount'], 0, '.', ','),
            'due_amount'           => number_format($invoice_detail[0]['due_amount'], 0, '.', ','),
            'shipping_cost'        => number_format($invoice_detail[0]['shipping_cost'], 0, '.', ','),
            'invoice_all_data'     => $invoice_detail,
            'previous'             => number_format($invoice_detail[0]['prevous_due'], 0, '.', ','),
            'is_discount'         => $is_discount,
            'users_name'           => $users->first_name . ' ' . $users->last_name,
            'admin_by'              => $admin->first_name . ' ' . $admin->last_name,
            'tax_regno'            => $txregname,
            'am_inword'            => $amount_inword,
            'is_desc'              => $descript,
            'is_serial'            => $isserial,
            'is_dis_val'           => $is_dis_val,
            'vat_amnt_per'         => $vat_amnt_per,
            'vat_amnt'             => $vat_amnt,
            'is_unit'              => $isunit,
            'bank'                  => $bank,
            'company_info'         => $this->retrieve_company(),
            'currency'             => $currency_details[0]['currency'],
            'position'             => $currency_details[0]['currency_position'],
            'discount_type'        => $currency_details[0]['discount_type'],
            'logo'                 => $currency_details[0]['invoice_logo'],

            'all_discount'         => number_format($invoice_detail[0]['total_discount'], 0, '.', ','),
            'p_method_list'        => $payment_method_list,
            'terms_list'           => $terms_list,
            'total_vat'            => number_format($invoice_detail[0]['total_vat_amnt'], 2, '.', ','),

        );

        return $data;
    }


    public function product_list()
    {
        $this->db->select('*');
        $this->db->from('product_information');
        $this->db->where('status', 1);
        $this->db->limit(30);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function japasys_print_settingdata()
    {
        $this->db->select('*');
        $this->db->from('print_setting');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    public function allterms_list()
    {
        return $this->db->select('*')
            ->from('seles_termscondi')
            ->get()
            ->result();
    }


    public function create_terms($data = [])
    {
        return $this->db->insert('seles_termscondi', $data);
    }

    public function update_terms($data = [])
    {
        return $this->db->where('id', $data['id'])
            ->update('seles_termscondi', $data);
    }

    public function single_terms_data($id)
    {
        return $this->db->select('*')
            ->from('seles_termscondi')
            ->where('id', $id)
            ->get()
            ->row();
    }

    public function delete_terms($id)
    {
        $this->db->where('id', $id)
            ->delete("seles_termscondi");
        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    }
}
