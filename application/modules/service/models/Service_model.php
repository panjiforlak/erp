<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: japasys Ltd
# Author link: https://www.japasys.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Service_model extends CI_Model
{

    public function tax_fields()
    {
        return $result = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();
    }

    public function service_entry($data)
    {
        $this->db->select('*');
        $this->db->from('product_service');
        $this->db->where('service_name', $data['service_name']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return FALSE;
        } else {
            $this->db->insert('product_service', $data);
            $s_id = $this->db->insert_id();
            $CreateBy = $this->session->userdata('id');
            $createdate = date('Y-m-d H:i:s');

            $coa = $this->headcode();

            if ($coa->HeadCode != NULL) {
                $headcode = $coa->HeadCode + 1;
            } else {
                $headcode = "122000001";
            }

            // coa head create   
            $acc_service = [
                'HeadCode'         => $headcode,
                'HeadName'         => $this->input->post('service_name', true) . '-' . $s_id,
                'PHeadName'        => 'Service Receive',
                'HeadLevel'        => '3',
                'IsActive'         => '1',
                'IsTransaction'    => '1',
                'IsGL'             => '0',
                'HeadType'         => 'A',
                'IsBudget'         => '1',
                'service_id'       => $s_id,
                'IsDepreciation'   => '1',
                'DepreciationRate' => '1',
                'CreateBy'         => $CreateBy,
                'CreateDate'       => $createdate,
            ];

            $this->db->insert('acc_coa', $acc_service);

            $this->db->select('*');
            $this->db->from('product_service');
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $json_service[] = array('label' => $row->service_name, 'value' => $row->service_id);
            }
            $cache_file = './my-assets/js/admin_js/json/service.json';
            $serviceList = json_encode($json_service);
            file_put_contents($cache_file, $serviceList);
            return TRUE;
        }
    }

    public function vat_tax_setting()
    {
        $this->db->select('*');
        $this->db->from('vat_tax_setting');
        $query   = $this->db->get();
        return $query->row();
    }

    public function pmethod_dropdown()
    {
        $data = $this->db->select('*')
            ->from('acc_coa')
            ->where('PHeadName', 'Cash In Boxes')
            ->get()
            ->result();

        $list[''] = 'Select Method';
        if (!empty($data)) {
            $list[0] = 'Credit'; //perubahaan - before Credit Sale
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


    public function web_setting()
    {
        return $result = $this->db->select('*')
            ->from('web_setting')
            ->get()
            ->result_array();
    }

    public function company_info()
    {
        return $result = $this->db->select('*')
            ->from('company_information')
            ->get()
            ->result_array();
    }



    public function service_list()
    {
        $this->db->select('*');
        $this->db->from('product_service');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function retrieve_service_editdata($service_id)
    {
        $this->db->select('*');
        $this->db->from('product_service');
        $this->db->where('service_id', $service_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function update_service($data = [], $service_id)
    {
        $this->db->where('service_id', $service_id);
        $this->db->update('product_service', $data);
        $acc_service = [
            'HeadName'         => $data['service_name'] . '-' . $service_id,
        ];
        $this->db->where('service_id', $service_id);
        $this->db->update('acc_coa', $acc_service);
        $this->db->select('*');
        $this->db->from('product_service');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $json_service[] = array('label' => $row->service_name, 'value' => $row->service_id);
        }
        $cache_file = './my-assets/js/admin_js/json/service.json';
        $serviceList = json_encode($json_service);
        file_put_contents($cache_file, $serviceList);
        return true;
    }


    public function delete_service($service_id)
    {
        $this->db->where('service_id', $service_id);
        $this->db->delete('product_service');
        $this->db->select('*');
        $this->db->from('product_service');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $json_service[] = array('label' => $row->service_name, 'value' => $row->service_id);
        }
        $cache_file = './my-assets/js/admin_js/json/service.json';
        $serviceList = json_encode($json_service);
        file_put_contents($cache_file, $serviceList);
        return true;
    }

    public function employee_list()
    {
        return $list = $this->db->select('*')->from('employee_history')->get()->result_array();
    }

    //autocomplete part
    public function customer_search($customer_id)
    {
        $query = $this->db->select('*')->from('customer_information')
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



    public function get_total_service_invoic($service_id)
    {

        $this->db->select('*');
        $this->db->from('product_service');
        $this->db->where(array('service_id' => $service_id));
        $serviceinfo = $this->db->get()->row();
        $tablecolumn = $this->db->list_fields('product_service');
        $num_column = count($tablecolumn) - 7;
        $taxfield = '';
        $taxvar = [];
        for ($i = 0; $i < $num_column; $i++) {
            $taxfield = 'tax' . $i;
            $data2[$taxfield] = $serviceinfo->$taxfield;
            $taxvar[$i] = $serviceinfo->$taxfield;
            $data2['taxdta'] = $taxvar;
        }
        $data2['txnmber'] = $num_column;
        $data2['price'] = $serviceinfo->charge;
        $data2['service_vat'] = $serviceinfo->service_vat;

        return $data2;
    }

    public function voucher_no_generator()
    {
        $pieces = null;
        $data = $this->db->select('max(id) as voucher_sl')->from('service_invoice')->get()->row();
        if (!empty($data->voucher_sl)) {
            $invoice_no = $data->voucher_sl + 1;
        } else {
            $invoice_no = 1;
        }
        return 'serv-' . $invoice_no;
    }

    public function invoice_entry()
    {

        $currency_details = $this->db->select('*')->from('web_setting')->get()->result_array();
        $tablecolumn      = $this->db->list_fields('tax_collection');
        $num_column       = count($tablecolumn) - 4;
        $employee         = $this->input->post('employee_id');
        $employee_id      = implode(',', $employee);
        $invoice_id      = $this->voucher_no_generator();
        $createby         = $this->session->userdata('id');
        $createdate       = date('Y-m-d H:i:s');
        $multipayamount = $this->input->post('pamount_by_method', TRUE);
        $multipaytype = $this->input->post('multipaytype', TRUE);

        if ($this->input->post('employee_id') == null) {
            $this->session->set_userdata(array('exception' => display('please_select_employee')));
            redirect(base_url() . 'add_service_invoice');
        }
        $customer_id = $this->input->post('customer_id', true);

        if ($customer_id == '') {
            $this->session->set_userdata(array('exception' => 'Please Select Customer'));
            redirect(base_url() . 'add_service_invoice');
        }
        //Full or partial Payment record.
        $paid_amount = $this->input->post('paid_amount', true);

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
            $paid_tax = $this->input->post('total_tax_amount', TRUE);
        }
        //Data inserting into invoice table
        $datainv = array(
            'employee_id'     => $employee_id,
            'customer_id'     => $customer_id,
            'date'            => (!empty($this->input->post('invoice_date', true)) ? $this->input->post('invoice_date', true) : date('Y-m-d')),
            'total_amount'    => $this->input->post('grand_total_price', true),
            'total_tax'       => $this->input->post('total_tax_amount', true),
            'voucher_no'      => $invoice_id,
            'details'         => (!empty($this->input->post('inva_details', true)) ? $this->input->post('inva_details', true) : 'Service Invoice'),
            'invoice_discount' => $this->input->post('invoice_discount', true),
            'total_vat_amnt'  => $this->input->post('total_vat_amnt', true),
            'total_discount'  => $this->input->post('total_discount', true),
            'shipping_cost'   => $this->input->post('shipping_cost', true),
            'paid_amount'     => $this->input->post('paid_amount', true),
            'due_amount'      => $this->input->post('due_amount', true),
            'previous'        => $this->input->post('previous', true),
            'sales_by'        => $this->session->userdata('id'),
            'is_credit'       =>  $is_credit,
            'is_fixed'        =>  $is_fixed,
            'is_dynamic'      =>  $is_dynamic,

        );
        $this->db->insert('service_invoice', $datainv);


        $cusifo = $this->db->select('*')->from('customer_information')->where('customer_id', $customer_id)->get()->row();
        $headn = $customer_id . '-' . $cusifo->customer_name;
        $coainfo = $this->db->select('*')->from('acc_coa')->where('HeadName', $headn)->get()->row();
        $customer_headcode = $coainfo->HeadCode;

        $i = 0;
        if ($multipaytype[0] != 0) {
            foreach ($multipaytype  as $multipaytype) {

                $paymethod = array(
                    'VNo'            =>  $invoice_id,
                    'Vtype'          =>  'SERVICEPayment',
                    'VDate'          =>  $createdate,
                    'COAID'          =>  $multipaytype,
                    'Narration'      =>  'Paid amount for SERVICE No ' . $invoice_id,
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

        //service income
        $service_income = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'SERVICE',
            'VDate'          =>  $createdate,
            'COAID'          =>  511002,
            'Narration'      =>  'Service Revenue For SERVICE No ' . $invoice_id,
            'Debit'          =>  0,
            'Credit'         =>  $this->input->post('grand_total_price', true) - (!empty($this->input->post('total_tax_amount', true)) ? $this->input->post('total_tax_amount', true) : 0),
            'IsPosted'       =>  1,
            'CreateBy'       =>  $createby,
            'CreateDate'     =>  $createdate,
            'IsAppove'       =>  1
        );
        $this->db->insert('acc_transaction', $service_income);


        $tax_info = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'SERVICE',
            'VDate'          =>  $createdate,
            'COAID'          =>  2114,
            'Narration'      =>  'Value added TAX For Service',
            'Debit'          =>  $paid_tax,
            'Credit'         =>  0,
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $tax_info);


        //Customer debit for service Value
        $cosdr = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'SERVICE',
            'VDate'          =>  $createdate,
            'COAID'          =>  $customer_headcode,
            'Narration'      =>  'Customer debit For service No ' . $invoice_id,
            'Debit'          =>  $this->input->post('grand_total_price', true),
            'Credit'         =>  0,
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $cosdr);

        //Customer credit for Paid Amount
        $coscr = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'SERVICE',
            'VDate'          =>  $createdate,
            'COAID'          =>  $customer_headcode,
            'Narration'      =>  'Customer credit for Paid Amount For Service No ' . $invoice_id,
            'Debit'          =>  0,
            'Credit'         =>  $this->input->post('paid_amount', true),
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );

        if ($multipaytype[0] != 0) {
            if (!empty($this->input->post('paid_amount', true))) {
                $this->db->insert('acc_transaction', $coscr);
            }
        }

        $quantity            = $this->input->post('product_quantity', true);
        $rate                = $this->input->post('product_rate', true);
        $serv_id             = $this->input->post('service_id', true);
        $total_amount        = $this->input->post('total_price', true);
        $discount_rate       = $this->input->post('discountvalue', true);
        $discount_per        = $this->input->post('discount', true);
        $vat_per             = $this->input->post('vatpercent', true);
        $vat_rate            = $this->input->post('vatvalue', true);
        $tax_amount          = $this->input->post('tax', true);
        $invoice_description = $this->input->post('desc', true);

        for ($i = 0, $n   = count($serv_id); $i < $n; $i++) {
            $service_qty  = $quantity[$i];
            $product_rate = $rate[$i];
            $service_id   = $serv_id[$i];
            $total_price  = $total_amount[$i];
            $disper       = $discount_per[$i];
            $disamnt      = $discount_rate[$i];
            $vatper       = $vat_per[$i];
            $vatamnt      = $vat_rate[$i];
            $coa_info     = $this->db->select('HeadCode')->from('acc_coa')->where('service_id', $service_id)->get()->row();

            $service_details = array(
                'service_inv_id'     => $invoice_id,
                'service_id'         => $service_id,
                'qty'                => $service_qty,
                'charge'             => $product_rate,
                'discount'           => $disper,
                'discount_amount'    => $disamnt,
                'vat'                => $vatper,
                'vat_amnt'           => $vatamnt,
                'total'              => $total_price,
            );
            $service_amount = array(
                'VNo'            =>  $invoice_id,
                'Vtype'          =>  'service',
                'VDate'          =>  $this->input->post('invoice_date', true),
                'COAID'          =>  $coa_info->HeadCode,
                'Narration'      => (!empty($this->input->post('inva_details', true)) ? $this->input->post('inva_details', true) : 'Service Invoice'),
                'Debit'          =>  $total_price,
                'Credit'         =>  0,
                'IsPosted'       =>  1,
                'CreateBy'       =>  $createby,
                'CreateDate'     =>  $createdate,
                'IsAppove'       =>  1
            );
            if (!empty($quantity)) {
                $this->db->insert('service_invoice_details', $service_details);
                $this->db->insert('acc_transaction', $service_amount);
            }
        }
        for ($j = 0; $j < $num_column; $j++) {
            $taxfield = 'tax' . $j;
            $taxvalue = 'total_tax' . $j;
            $taxdata[$taxfield] = $this->input->post($taxvalue);
        }
        $taxdata['customer_id'] = $customer_id;
        $taxdata['date']        = (!empty($this->input->post('invoice_date', true)) ? $this->input->post('invoice_date', true) : date('Y-m-d'));
        $taxdata['relation_id'] = $invoice_id;
        $this->db->insert('tax_collection', $taxdata);

        $message = 'Mr.' . $cusifo->customer_name . ',
        ' . 'Your Service Charge ' . $this->input->post('grand_total_price', true) . ' ' . $currency_details[0]['currency'] . ' You have paid .' . $this->input->post('paid_amount') . ' ' . $currency_details[0]['currency'];
        $config_data = $this->db->select('*')->from('sms_settings')->get()->row();
        if ($config_data->isservice == 1) {
            $this->smsgateway->send([
                'apiProvider' => 'nexmo',
                'username'    => $config_data->api_key,
                'password'    => $config_data->api_secret,
                'from'        => $config_data->from,
                'to'          => $cusifo->customer_mobile,
                'message'     => $message
            ]);
        }
        return $invoice_id;
    }

    public function user_invoice_data($user_id)
    {
        return  $this->db->select('*')->from('users')->where('user_id', $user_id)->get()->row();
    }

    // Service Invoice Update Information
    public function service_invoice_updata($invoice_id)
    {
        return $this->db->select('a.*,b.*,c.service_name')
            ->from('service_invoice a')
            ->join('service_invoice_details b', 'b.service_inv_id=a.voucher_no', 'left')
            ->join('product_service c', 'c.service_id=b.service_id', 'left')
            ->where('a.voucher_no', $invoice_id)
            ->order_by('b.id', 'asc')
            ->get()
            ->result_array();
    }


    public function service_invoice_list($limit = null, $start = null)
    {
        return $this->db->select('a.*,b.customer_name')
            ->from('service_invoice a')
            ->join('customer_information b', 'b.customer_id=a.customer_id', 'left')
            ->order_by('a.id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result_array();
    }

    // customer information 
    public function customer_info($customer_id)
    {
        return $this->db->select('*')
            ->from('customer_information')
            ->where('customer_id', $customer_id)
            ->get()
            ->row();
    }

    // tax for service information
    public function service_invoice_taxinfo($invoice_id)
    {
        return $this->db->select('*')
            ->from('tax_collection')
            ->where('relation_id', $invoice_id)
            ->get()
            ->result_array();
    }



    public function invoice_update()
    {


        $tablecolumn = $this->db->list_fields('tax_collection');
        $num_column = count($tablecolumn) - 4;
        $invoice_id = $this->input->post('invoice_id', true);
        $employee = $this->input->post('employee_id', true);
        $employee_id = implode(',', $employee);
        $createby = $this->session->userdata('user_id');
        $createdate = date('Y-m-d H:i:s');
        $multipayamount = $this->input->post('pamount_by_method', TRUE);
        $multipaytype = $this->input->post('multipaytype', TRUE);

        if (($this->input->post('customer_name_others', true) == null) && ($this->input->post('customer_id', true) == null) && ($this->input->post('customer_name', true) == null)) {
            $this->session->set_flashdata(array('exception' => display('please_select_customer')));
            redirect(base_url("edit_service_invoice/" . $invoice_id));
        }
        if ($this->input->post('employee_id') == null) {
            $this->session->set_flashdata(array('exception' => display('please_select_employee')));
            redirect(base_url("edit_service_invoice/" . $invoice_id));
        }

        if (!empty($invoice_id)) {
            //Customer Ledger
            // Account Transaction
            $this->db->where('VNo', $invoice_id);
            $this->db->delete('acc_transaction');
            //Service Invoice Details

            $this->db->where('service_inv_id', $invoice_id);
            $this->db->delete('service_invoice_details');
            //tax_collection
            $this->db->where('relation_id', $invoice_id);
            $this->db->delete('tax_collection');
        }

        $fixordyn = $this->db->select('*')->from('vat_tax_setting')->get()->row();

        if ($fixordyn->fixed_tax == 1) {

            $paid_tax = $this->input->post('total_vat_amnt', TRUE);
        } elseif ($fixordyn->dynamic_tax == 1) {

            $paid_tax = $this->input->post('total_tax_amount', TRUE);
        }


        if (($this->input->post('customer_id') == null) && ($this->input->post('customer_name') == null)) {
            $customer_id = $this->auth->generator(15);
            //Customer  basic information adding.
            $coa = $this->headcode();
            if ($coa->HeadCode != NULL) {
                $headcode = $coa->HeadCode + 1;
            } else {
                $headcode = "102030101";
            }
            $c_acc = $customer_id . '-' . $this->input->post('customer_name_others', true);
            $createby = $this->session->userdata('id');
            $createdate = date('Y-m-d H:i:s');
            $data = array(
                'customer_id'      => $customer_id,
                'customer_name'    => $this->input->post('customer_name_others', true),
                'customer_address' => $this->input->post('customer_name_others_address', true),
                'customer_mobile'  => $this->input->post('customer_mobile', true),
                'customer_email'   => "",
                'status'           => 2
            );
            $customer_coa = [
                'HeadCode'         => $headcode,
                'HeadName'         => $c_acc,
                'PHeadName'        => 'Customer Receivable',
                'HeadLevel'        => '4',
                'IsActive'         => '1',
                'IsTransaction'    => '1',
                'IsGL'             => '0',
                'HeadType'         => 'A',
                'IsBudget'         => '0',
                'IsDepreciation'   => '0',
                'DepreciationRate' => '0',
                'CreateBy'         => $createby,
                'CreateDate'       => $createdate,
            ];

            $this->db->insert('customer_information', $data);
            $this->db->insert('acc_coa', $customer_coa);
            $this->db->select('*');
            $this->db->from('customer_information');
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $json_customer[] = array('label' => $row->customer_name, 'value' => $row->customer_id);
            }
            $cache_file = './my-assets/js/admin_js/json/customer.json';
            $customerList = json_encode($json_customer);
            file_put_contents($cache_file, $customerList);


            //Previous balance adding -> Sending to customer model to adjust the data.
            $this->Customers->previous_balance_add(0, $customer_id);
        } else {
            $customer_id = $this->input->post('customer_id');
        }


        //Full or partial Payment record.
        $paid_amount = $this->input->post('paid_amount', true);
        if ($this->input->post('paid_amount', true) >= 0) {

            $this->db->set('status', '1');
            $this->db->where('customer_id', $customer_id);

            $this->db->update('customer_information');

            $transection_id = $this->occational->generator(15);





            // Inserting for Accounts adjustment.
            ############ default table :: customer_payment :: inflow_92mizdldrv #################
        }
        if ($multipaytype[0] == 0) {
            $is_credit = 1;
        } else {
            $is_credit = '';
        }
        //Data inserting into invoice table
        $datainv = array(
            'employee_id'     => $employee_id,
            'customer_id'     => $customer_id,
            'date'            => (!empty($this->input->post('invoice_date', true)) ? $this->input->post('invoice_date', true) : date('Y-m-d')),
            'total_amount'    => $this->input->post('grand_total_price', true),
            'total_tax'       => $this->input->post('total_tax_amount', true),
            'voucher_no'      => $invoice_id,
            'details'         => (!empty($this->input->post('inva_details', true)) ? $this->input->post('inva_details', true) : 'Service Invoice'),
            'invoice_discount' => $this->input->post('invoice_discount', true),
            'total_discount'  => $this->input->post('total_discount', true),
            'total_vat_amnt'  => $this->input->post('total_vat_amnt', true),
            'shipping_cost'   => $this->input->post('shipping_cost', true),
            'paid_amount'     => $this->input->post('paid_amount', true),
            'due_amount'      => $this->input->post('due_amount', true),
            'previous'        => $this->input->post('previous', true),
            'sales_by'        => $this->session->userdata('id'),
            'is_credit'       =>  $is_credit,

        );

        $this->db->where('voucher_no', $invoice_id);
        $this->db->update('service_invoice', $datainv);


        $cusifo = $this->db->select('*')->from('customer_information')->where('customer_id', $customer_id)->get()->row();
        $headn = $customer_id . '-' . $cusifo->customer_name;
        $coainfo = $this->db->select('*')->from('acc_coa')->where('HeadName', $headn)->get()->row();
        $customer_headcode = $coainfo->HeadCode;
        // Cash in Hand debit


        $i = 0;
        if ($multipaytype[0] != 0) {
            foreach ($multipaytype  as $multipaytype) {

                $paymethod = array(
                    'VNo'            =>  $invoice_id,
                    'Vtype'          =>  'SERVICEPayment',
                    'VDate'          =>  $createdate,
                    'COAID'          =>  $multipaytype,
                    'Narration'      =>  'Paid amount for SERVICE No ' . $invoice_id,
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

        //service income

        $service_income = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'SERVICE',
            'VDate'          =>  $createdate,
            'COAID'          =>  511002,
            'Narration'      =>  'Service Revenue For SERVICE No ' . $invoice_id,
            'Debit'          =>  0,
            'Credit'         =>  $this->input->post('grand_total_price', true) - (!empty($this->input->post('total_tax_amount', true)) ? $this->input->post('total_tax_amount', true) : 0),
            'IsPosted'       =>  1,
            'CreateBy'       =>  $createby,
            'CreateDate'     =>  $createdate,
            'IsAppove'       =>  1
        );
        $this->db->insert('acc_transaction', $service_income);
        $tax_info = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'SERVICE',
            'VDate'          =>  $createdate,
            'COAID'          =>  2114,
            'Narration'      =>  'Value added TAX For Service',
            'Debit'          =>  $paid_tax,
            'Credit'         =>  0,
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $tax_info);



        //Customer debit for service Value
        $cosdr = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'SERVICE',
            'VDate'          =>  $createdate,
            'COAID'          =>  $customer_headcode,
            'Narration'      =>  'Customer debit For service No' . $invoice_id,
            'Debit'          =>  $this->input->post('grand_total_price', true),
            'Credit'         =>  0,
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );
        $this->db->insert('acc_transaction', $cosdr);

        ///Customer credit for Paid Amount
        $coscr = array(
            'VNo'            =>  $invoice_id,
            'Vtype'          =>  'SERVICE',
            'VDate'          =>  $createdate,
            'COAID'          =>  $customer_headcode,
            'Narration'      =>  'Customer credit for Paid Amount For Service No' . $invoice_id,
            'Debit'          =>  0,
            'Credit'         =>  $this->input->post('paid_amount', true),
            'IsPosted'       => 1,
            'CreateBy'       => $createby,
            'CreateDate'     => $createdate,
            'IsAppove'       => 1
        );

        if ($multipaytype[0] != 0) {
            if (!empty($this->input->post('paid_amount', true))) {
                $this->db->insert('acc_transaction', $coscr);
            }
        }

        $quantity            = $this->input->post('product_quantity', true);
        $rate                = $this->input->post('product_rate', true);
        $serv_id             = $this->input->post('service_id', true);
        $total_amount        = $this->input->post('total_price', true);
        $discount_rate       = $this->input->post('discountvalue', true);
        $discount_per        = $this->input->post('discount', true);
        $vat_per             = $this->input->post('vatpercent', true);
        $vat_rate            = $this->input->post('vatvalue', true);
        $tax_amount          = $this->input->post('tax', true);
        $invoice_description = $this->input->post('desc', true);

        for ($i = 0, $n   = count($serv_id); $i < $n; $i++) {
            $service_qty  = $quantity[$i];
            $product_rate = $rate[$i];
            $service_id   = $serv_id[$i];
            $total_price  = $total_amount[$i];
            $disper       = $discount_per[$i];
            $disamnt      = $discount_rate[$i];
            $vatper       = $vat_per[$i];
            $vatamnt      = $vat_rate[$i];
            $coa_info     = $this->db->select('HeadCode')->from('acc_coa')->where('service_id', $service_id)->get()->row();

            $service_details = array(
                'service_inv_id'     => $invoice_id,
                'service_id'         => $service_id,
                'qty'                => $service_qty,
                'charge'             => $product_rate,
                'discount'           => $disper,
                'discount_amount'    => $disamnt,
                'vat'                => $vatper,
                'vat_amnt'           => $vatamnt,
                'total'              => $total_price,
            );

            $service_amount = array(
                'VNo'            =>  $invoice_id,
                'Vtype'          =>  'service',
                'VDate'          =>  $this->input->post('invoice_date', true),
                'COAID'          =>  $coa_info->HeadCode,
                'Narration'      => (!empty($this->input->post('inva_details', true)) ? $this->input->post('inva_details', true) : 'Service Invoice'),
                'Debit'          =>  $total_price,
                'Credit'         =>  0,
                'IsPosted'       =>  1,
                'CreateBy'       =>  $createby,
                'CreateDate'     =>  $createdate,
                'IsAppove'       =>  1
            );
            if (!empty($quantity)) {
                $this->db->insert('service_invoice_details', $service_details);
                $this->db->insert('acc_transaction', $service_amount);
            }
        }
        for ($j = 0; $j < $num_column; $j++) {
            $taxfield = 'tax' . $j;
            $taxvalue = 'total_tax' . $j;
            $taxdata[$taxfield] = $this->input->post($taxvalue);
        }
        $taxdata['customer_id'] = $customer_id;
        $taxdata['date']        = (!empty($this->input->post('invoice_date')) ? $this->input->post('invoice_date') : date('Y-m-d'));
        $taxdata['relation_id'] = $invoice_id;
        $this->db->insert('tax_collection', $taxdata);

        $message = 'Mr.' . $cusifo->customer_name . ',
        ' . 'Your Service Charge ' . $this->input->post('grand_total_price') . ' You have paid .' . $this->input->post('paid_amount');
        if ($config_data->isservice == 1) {
            $this->smsgateway->send([
                'apiProvider' => 'nexmo',
                'username'    => $config_data->api_key,
                'password'    => $config_data->api_secret,
                'from'        => $config_data->from,
                'to'          => $cusifo->customer_mobile,
                'message'     => $message
            ]);
        }
        return $invoice_id;
    }


    public function delete_service_invoice($invoice_id)
    {
        //service invoice delete
        $this->db->where('voucher_no', $invoice_id);
        $this->db->delete('service_invoice');
        //service invoice details delete
        $this->db->where('service_inv_id', $invoice_id);
        $this->db->delete('service_invoice_details');
        //acc transaction delete
        $this->db->where('VNo', $invoice_id);
        $this->db->delete('acc_transaction');
        return true;
    }

    public function headcode()
    {
        $query = $this->db->query("SELECT MAX(HeadCode) as HeadCode FROM acc_coa WHERE HeadLevel='3' And HeadCode LIKE '12200%' ORDER BY CreateDate DESC");
        return $query->row();
    }
}
