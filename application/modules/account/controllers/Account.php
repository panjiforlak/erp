<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: japasys Ltd
# Author link: https://www.japasys.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Account extends MX_Controller
{

  public function __construct()
  {
    parent::__construct();
    $timezone = $this->db->select('timezone')->from('web_setting')->get()->row();
    date_default_timezone_set($timezone->timezone);
    $this->load->model(array(
      'account_model'
    ));
    if (!$this->session->userdata('isLogIn'))
      redirect('login');
  }

  //tree view show
  function japasys_chart_of_account()
  {

    $data['title']      = display('c_o_a');
    $data['userList']   = $this->account_model->get_userlist();
    $data['parent']     = $this->account_model->get_parenthead();
    $data['userID']     = set_value('userID');
    $data['module']     = "account";
    $data['page']       = "treeview";

    echo modules::run('template/layout', $data);
    // echo $this->load->view('treeview', $data, TRUE);
  }


  public function japasys_add_opening_balance()
  {
    $this->form_validation->set_rules('headcode', display('account_head'), 'max_length[100]');
    $this->form_validation->set_rules('dtpDate', display('date'), 'required|max_length[30]');
    $this->form_validation->set_rules('amount', display('amount'), 'required|max_length[30]');
    if ($this->form_validation->run()) {
      $createby   = $this->session->userdata('id');
      $createdate = date('Y-m-d H:i:s');
      $postData = array(
        'VNo'            => $this->input->post('txtVNo', true),
        'Vtype'          => 'Opening',
        'VDate'          => $this->input->post('dtpDate', true),
        'COAID'          => $this->input->post('headcode', true),
        'Narration'      => $this->input->post('txtRemarks', true),
        'Debit'          => $this->input->post('amount', true),
        'Credit'         => 0,
        'IsPosted'       => 1,
        'is_opening'     => 1,
        'CreateBy'       => $createby,
        'CreateDate'     => $createdate,
        'IsAppove'       => 1
      );
      if ($this->account_model->create_opening($postData)) {
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect('opening_balance');
      } else {
        $this->session->set_flashdata('exception', display('please_try_again'));
        redirect('opening_balance');
      }
    } else {
      $this->session->set_flashdata('exception', validation_errors());
      redirect('opening_balance');
    }
  }


  public function japasys_opening_balance_form()
  {
    $data['title']      = display('opening_balance');
    $data['headss']     = $this->account_model->get_userlist();
    $data['voucher_no'] = $this->account_model->opeing_voucher();
    $data['module']     = "account";
    $data['page']       = "opening_balance";
    echo modules::run('template/layout', $data);
  }

  public function selectedform($id)
  {

    $role_reult = $this->account_model->treeview_selectform($id);
    if ($role_reult) {
      $html = "";
      $html .= form_open('', 'id="treeview_form" class="form-vertical"');
      $html .= "<div id=\"newData\" class=\"row\">
      <div class=\"col-sm-12\">
      <div class=\"row form-custom\">
        <label class=\"col-sm-3\"><b>Head Code</b></label>
        <div class=\"col-sm-9\"><input type=\"text\" name=\"txtHeadCode\" id=\"txtHeadCode\" class=\"form_input form-control\"  value=\"" . $role_reult->HeadCode . "\" readonly=\"readonly\"/></div>
      </div>
       </div>
  
     <div class=\"col-sm-12\">
      <div class=\"row form-custom\">
        <label class=\"col-sm-3\"><b>Head Name</b></label>
        <div class=\"col-sm-9\"><input type=\"text\" name=\"txtHeadName\" id=\"txtHeadName\" class=\"form_input form-control\" value=\"" . $role_reult->HeadName . "\"/>
<input type=\"hidden\" name=\"HeadName\" id=\"HeadName\" class=\"form_input\" value=\"" . $role_reult->HeadName . "\"/>
        </div>
      </div>
      </div>
     <div class=\"col-sm-12\">
      <div class=\"row form-custom\">
        <label class=\"col-sm-3\"><b>Parent Head</b></label>
        <div class=\"col-sm-9\"><input type=\"text\" name=\"txtPHead\" id=\"txtPHead\" class=\"form_input form-control\" readonly=\"readonly\" value=\"" . $role_reult->PHeadName . "\"/></div>
      </div>
      </div>
       <div class=\"col-sm-12\">
      <div class=\"row form-custom\">

        <label class=\"col-sm-3\"><b>Head Level</b></label>
        <div class=\"col-sm-9\"><input type=\"text\" name=\"txtHeadLevel\" id=\"txtHeadLevel\" class=\"form_input form-control\" readonly=\"readonly\" value=\"" . $role_reult->HeadLevel . "\"/></div>
      </div>
      </div>
       <div class=\"col-sm-12\">
      <div class=\"row form-custom\">
        <label class=\"col-sm-3\"><b>Head Type</b></label>
        <div class=\"col-sm-9\"><input type=\"text\" name=\"txtHeadType\" id=\"txtHeadType\" class=\"form_input form-control\" readonly=\"readonly\" value=\"" . $role_reult->HeadType . "\"/></div>
      </div>
      </div>

       <div class=\"col-sm-12\">
      <div class=\"row form-custom\">
         <div class=\"col-sm-9 col-sm-offset-3\">
         <div class=\"align-center\">
           <div class=\"mr-15\">
           <input type=\"checkbox\" name=\"IsTransaction\" value=\"1\" class=\"mr-5\" id=\"IsTransaction\" size=\"28\"  onchange=\"IsTransaction_change()\"";
      if ($role_reult->IsTransaction == 1) {
        $html .= "checked";
      }
      $html .= "/><label for=\"IsTransaction\"> IsTransaction</label>
            </div>

            <div class=\"mr-15\">
           <input type=\"checkbox\" value=\"1\" name=\"IsActive\" class=\"mr-5\" id=\"IsActive\" size=\"28\"";
      if ($role_reult->IsActive == 1) {
        $html .= "checked";
      }
      $html .= "/><label for=\"IsActive\"> IsActive</label>
            </div>

            <div class=\"mr-15\">
           <input type=\"checkbox\" value=\"1\" name=\"IsGL\" class=\"mr-5\" id=\"IsGL\" size=\"28\" onchange=\"IsGL_change();\"";
      if ($role_reult->IsGL == 1) {
        $html .= "checked";
      }
      $html .= "/><label for=\"IsGL\"> IsGL</label>
            </div>
          </div>

        </div>";
      $html .= "</div>
      </div>
       <div class=\"col-sm-12\">
       <div class=\"row mx-0\">
                    <div class=\"col-sm-9 col-sm-offset-3\">";
      $html .= "<input type=\"button\" name=\"btnNew\" id=\"btnNew\" value=\"New\" onClick=\"newHeaddata(" . $role_reult->HeadCode . ")\" class=\"btn btn-sub btn-info\"/>
                      <input type=\"btn\" name=\"btnSave\" id=\"btnSave\" value=\"Save\" disabled=\"disabled\" class=\"btn btn-sub btn-success\" onclick=\"treeSubmit()\"/>";

      $html .= " <input type=\"button\" name=\"btnUpdate\" id=\"btnUpdate\" value=\"Update\" onclick=\"treeSubmit()\" class=\"btn btn-sub btn-primary\"/>  <button type=\"button\" class=\"btn btn-sub btn-danger\" data-dismiss=\"modal\">Close</button></div>";
      $html .= "</div></div>
 </form>
            ";
    }

    echo json_encode($html);
  }

  public function insert_coa()
  {
    $headcode    = $this->input->post('txtHeadCode', TRUE);
    $HeadName    = $this->input->post('txtHeadName', TRUE);
    $PHeadName   = $this->input->post('txtPHead', TRUE);
    $HeadLevel   = $this->input->post('txtHeadLevel', TRUE);
    $txtHeadType = $this->input->post('txtHeadType', TRUE);
    $isact       = $this->input->post('IsActive', TRUE);
    $IsActive    = (!empty($isact) ? $isact : 0);
    $trns        = $this->input->post('IsTransaction', TRUE);
    $IsTransaction = (!empty($trns) ? $trns : 0);
    $isgl        = $this->input->post('IsGL', TRUE);
    $IsGL       = (!empty($isgl) ? $isgl : 0);
    $createby    = $this->session->userdata('id');
    $createdate  = date('Y-m-d H:i:s');
    $postData = array(
      'HeadCode'       =>  $headcode,
      'HeadName'       =>  $HeadName,
      'PHeadName'      =>  $PHeadName,
      'HeadLevel'      =>  $HeadLevel,
      'IsActive'       =>  $IsActive,
      'IsTransaction'  =>  $IsTransaction,
      'IsGL'           =>  $IsGL,
      'HeadType'       => $txtHeadType,
      'IsBudget'       => 0,
      'CreateBy'       => $createby,
      'CreateDate'     => $createdate,
    );
    $upinfo = $this->db->select('*')
      ->from('acc_coa')
      ->where('HeadCode', $headcode)
      ->get()
      ->row();
    if (empty($upinfo)) {
      $this->db->insert('acc_coa', $postData);
      $data['status']  = true;
      $data['message'] = 'Successfully Saved';
    } else {

      $hname = $this->input->post('HeadName', TRUE);
      $updata = array(
        'PHeadName'      =>  $HeadName,
      );


      $this->db->where('HeadCode', $headcode)
        ->update('acc_coa', $postData);
      $this->db->where('PHeadName', $hname)
        ->update('acc_coa', $updata);

      $data['status']  = true;
      $data['message'] = 'Successfully Updated';
    }



    echo json_encode($data);
  }

  public function newform($id)
  {

    $newdata = $this->db->select('*')
      ->from('acc_coa')
      ->where('HeadCode', $id)
      ->get()
      ->row();


    $newidsinfo = $this->db->select('*,max(HeadCode) as hc')
      ->from('acc_coa')
      ->where('PHeadName', $newdata->HeadName)
      ->get()
      ->row();

    $nid  = $newidsinfo->hc;
    if ($nid) {
      $n = $nid + 1;
      $HeadCode = $n;
    } else {
      $HeadCode = $id . '00' . 1;
    }

    $info['headcode']  =  $HeadCode;
    $info['rowdata']   =  $newdata;
    $info['headlabel'] =  $newdata->HeadLevel + 1;
    echo json_encode($info);
  }


  public function japasys_supplier_payment()
  {
    $data['title']          = display('supplier_payment');
    $data['supplier_list']  = $this->account_model->get_supplier();
    $data['voucher_no']     = $this->account_model->Spayment();
    $data['all_pmethod']   = $this->account_model->pmethod_dropdown();
    $data['module']         = "account";
    $data['page']           = "supplier_payment_form";
    echo modules::run('template/layout', $data);
  }

  public function create_supplier_payment()
  {
    $this->form_validation->set_rules('txtCode', display('txtCode'), 'max_length[100]');
    $this->form_validation->set_rules('dueAmount', display('due_amount'), 'required');
    $this->form_validation->set_rules('txtCode', display('code'), 'required|max_length[30]');
    $this->form_validation->set_rules('voucher_no', display('voucher_no'), 'required');
    $this->form_validation->set_rules('txtAmount', display('amount'), 'required|max_length[30]');
    if ($this->form_validation->run()) {
      if ($this->account_model->supplier_payment_insert()) {

        $info['supplier_info']       = $this->account_model->supplierinfo($this->input->post('supplier_id', TRUE));
        $info['payment_info']        = $this->account_model->supplierpaymentinfo($this->input->post('voucher_no', TRUE), $this->input->post('txtCode', TRUE));
        $info['company_info']       = $this->account_model->retrieve_company();
        $info['message']            = display('save_successfully');
        $info['details']           = $this->load->view('account/supplier_payment_receipt', $info, true);
        $info['status']             = true;
        echo json_encode($info);
        exit;
      } else {
        $info['exception']            = display('please_try_again');
        $info['status']             = false;
        echo json_encode($info);
        exit;
      }
    } else {
      $info['exception']            = validation_errors();
      $info['status']             = false;
      echo json_encode($info);
      exit;
    }
  }

  public function supplier_paymentreceipt($supplier_id, $voucher_no, $coaid)
  {
    $supplier_id           = $this->uri->segment(2);
    $voucher_no            = $this->uri->segment(3);
    $coaid                 = $this->uri->segment(4);
    $data['supplier_info'] = $this->account_model->supplierinfo($supplier_id);
    $data['payment_info']  = $this->account_model->supplierpaymentinfo($voucher_no, $coaid);
    $data['title']         = display('supplier_payment_receipt');
    $data['module']        = "account";
    $data['page']          = "supplier_payment_receipt";
    echo modules::run('template/layout', $data);
  }

  public function supplier_headcode()
  {
    $id            = $this->input->post('supplier_id');
    $voucher_info  = $this->account_model->supplier_due_vouchers($id);

    echo  json_encode($voucher_info);
  }

  //Customer Receive
  public function customer_receive()
  {
    $data['customer_list'] = $this->account_model->get_customer();
    $data['voucher_no']    = $this->account_model->Creceive();
    $data['all_pmethod']   = $this->account_model->pmethod_dropdown();
    $data['title']         = display('customer_receive');
    $data['module']        = "account";
    $data['page']          = "customer_receive_form";
    echo modules::run('template/layout', $data);
  }

  public function customer_headcode()
  {
    $id = $this->input->post('customer_id');
    $voucher_info  = $this->account_model->customer_due_vouchers($id);
    echo json_encode($voucher_info);
  }


  public function create_customer_receive()
  {
    $this->form_validation->set_rules('voucher_no', display('voucher_no'), 'required');
    $this->form_validation->set_rules('txtCode', display('txtCode'), 'required|max_length[100]');
    $this->form_validation->set_rules('txtAmount', display('amount'), 'max_length[100]');
    if ($this->form_validation->run()) {
      if ($this->account_model->customer_receive_insert()) {
        $info['company_info']       = $this->account_model->retrieve_company();
        $info['customer_info']      = $this->account_model->custoinfo($this->input->post('customer_id', TRUE));
        $info['payment_info']       = $this->account_model->customerreceiptinfo($this->input->post('voucher_no', TRUE), $this->input->post('txtCode', TRUE));
        $info['message']            = display('save_successfully');
        $info['details']            = $this->load->view('account/customer_payment_receipt', $info, true);
        $info['status']             = true;
        echo json_encode($info);
        exit;
      } else {

        $info['exception']            = display('please_try_again');
        $info['status']             = false;
        echo json_encode($info);
        exit;
      }
    } else {
      $info['exception']            = validation_errors();
      $info['status']             = false;
      echo json_encode($info);
      exit;
    }
  }
  public function create_service_payment()
  {
    $this->form_validation->set_rules('voucher_no', display('voucher_no'), 'required');
    $this->form_validation->set_rules('txtCode', display('txtCode'), 'required|max_length[100]');
    $this->form_validation->set_rules('txtAmount', display('amount'), 'max_length[100]');
    if ($this->form_validation->run()) {
      if ($this->account_model->customer_service_payment_insert()) {
        $info['company_info']       = $this->account_model->retrieve_company();
        $info['customer_info']      = $this->account_model->custoinfo($this->input->post('customer_id', TRUE));
        $info['payment_info']       = $this->account_model->customerservicereceiptinfo($this->input->post('voucher_no', TRUE), $this->input->post('txtCode', TRUE));
        $info['message']            = display('save_successfully');
        $info['details']            = $this->load->view('account/service_payment_receipt', $info, true);
        $info['status']             = true;
        echo json_encode($info);
        exit;
      } else {

        $info['exception']            = display('please_try_again');
        $info['status']             = false;
        echo json_encode($info);
        exit;
      }
    } else {
      $info['exception']            = validation_errors();
      $info['status']             = false;
      echo json_encode($info);
      exit;
    }
  }


  public function customer_receipt($customer_id, $voucher_no, $coaid)
  {
    $customer_id           = $this->uri->segment(2);
    $voucher_no            = $this->uri->segment(3);
    $coaid                 = $this->uri->segment(4);
    $data['customer_info'] = $this->account_model->custoinfo($customer_id);
    $data['payment_info']  = $this->account_model->customerreceiptinfo($voucher_no, $coaid);
    $data['title']         = display('customer_receive');
    $data['module']        = "account";
    $data['page']          = "customer_payment_receipt";
    echo modules::run('template/layout', $data);
  }

  //service Paymwnt
  public function service_payment_view()
  {
    $data['customer_list'] = $this->account_model->get_customer();
    $data['voucher_no']    = $this->account_model->Creceive();
    $data['all_pmethod']   = $this->account_model->pmethod_dropdown();
    $data['title']         = display('service_payment');
    $data['module']        = "account";
    $data['page']          = "service_payment_form";
    echo modules::run('template/layout', $data);
  }

  public function customer_service_headcode()
  {
    $id = $this->input->post('customer_id');
    $voucher_info  = $this->account_model->customer_service_due_vouchers($id);
    echo json_encode($voucher_info);
  }




  public function japasys_cash_adjustment()
  {
    $data['title']      = display('cash_adjustment');
    $data['voucher_no'] = $this->account_model->Cashvoucher();
    $data['module']     = "account";
    $data['page']       = "cash_adjustment";
    echo modules::run('template/layout', $data);
  }

  public function japasys_create_cash_adjustment()
  {
    $this->form_validation->set_rules('txtAmount', display('amount'), 'required|max_length[100]');
    $this->form_validation->set_rules('type', display('adjustment_type'), 'required|max_length[10]');
    if ($this->form_validation->run()) {
      if ($this->account_model->insert_cashadjustment()) {
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect('cash_adjustment');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("cash_adjustment");
    } else {
      $this->session->set_flashdata('exception',  validation_errors());
      redirect("cash_adjustment");
    }
  }


  public function japasys_debit_voucher()
  {
    $data['title']      = display('debit_voucher');
    $data['acc']        = $this->account_model->Transacc();
    $data['voucher_no'] = $this->account_model->voNO();
    $data['crcc']       = $this->account_model->Cracc();
    $data['listkas']       = $this->account_model->listCash(); //perubahan : penambahan << variable listcash
    $data['module']     = "account";
    $data['page']       = "debit_voucher";
    echo modules::run('template/layout', $data);
  }

  public function debtvouchercode($id)
  {

    $debitvcode = $this->db->select('*')
      ->from('acc_coa')
      ->where('HeadCode', $id)
      ->get()
      ->row();
    $code = $debitvcode->HeadCode;
    echo json_encode($code);
  }

  public function japasys_create_debit_voucher()
  {
    $this->form_validation->set_rules('cmbDebit', display('credit_account_head'), 'required|max_length[100]');
    $this->form_validation->set_rules('dtpDate', display('date'), 'required|max_length[100]');
    $this->form_validation->set_rules('cmbCode[]', display('account_name'), 'required|max_length[100]');
    $this->form_validation->set_rules('txtAmount[]', display('amount'), 'required|max_length[100]');
    $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
    if ($this->form_validation->run()) {
      if ($this->account_model->insert_debitvoucher()) {
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect('debit_voucher');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("debit_voucher");
    } else {
      $this->session->set_flashdata('exception',  validation_errors());
      redirect("debit_voucher");
    }
  }



  //Credit voucher 
  public function japasys_credit_voucher()
  {
    $data['title']      = display('credit_voucher');
    $data['acc']        = $this->account_model->Transacc();
    $data['voucher_no'] = $this->account_model->crVno();
    $data['crcc']       = $this->account_model->Cracc();
    $data['listkas']    = $this->account_model->listCash(); //perubahan : penambahan listkas
    $data['module']     = "account";
    $data['page']       = "credit_voucher";
    echo modules::run('template/layout', $data);
  }

  //Create Credit Voucher
  public function japasys_create_credit_voucher()
  {
    $this->form_validation->set_rules('cmbDebit', display('credit_account_head'), 'required|max_length[100]');
    $this->form_validation->set_rules('dtpDate', display('date'), 'required|max_length[100]');
    $this->form_validation->set_rules('cmbCode[]', display('account_name'), 'required|max_length[100]');
    $this->form_validation->set_rules('txtAmount[]', display('amount'), 'required|max_length[100]');
    $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
    if ($this->form_validation->run()) {
      if ($this->account_model->insert_creditvoucher()) {
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect('credit_voucher');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("credit_voucher");
    } else {
      $this->session->set_flashdata('exception',  display('please_try_again'));
      redirect("credit_voucher");
    }
  }

  // Contra Voucher form
  public function japasys_contra_voucher()
  {
    $data['title']      = display('contra_voucher');
    $data['acc']        = $this->account_model->Transacc();
    $data['voucher_no'] = $this->account_model->contra();
    $data['module']     = "account";
    $data['page']       = "contra_voucher";
    echo modules::run('template/layout', $data);
  }


  public function japasys_create_contra_voucher()
  {
    $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
    if ($this->form_validation->run()) {
      if ($this->account_model->insert_contravoucher()) {
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect('contra_voucher');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("contra_voucher");
    } else {
      $this->session->set_flashdata('exception',  validation_errors());
      redirect("contra_voucher");
    }
  }

  // Journal voucher
  public function japasys_journal_voucher()
  {
    $data['title']      = display('journal_voucher');
    $data['acc']        = $this->account_model->Transacc();
    $data['voucher_no'] = $this->account_model->journal();
    $data['module']     = "account";
    $data['page']       = "journal_voucher";
    echo modules::run('template/layout', $data);
  }


  //Create Journal Voucher
  public function japasys_create_journal_voucher()
  {
    $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
    if ($this->form_validation->run()) {
      if ($this->account_model->insert_journalvoucher()) {
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect('journal_voucher');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("journal_voucher");
    } else {
      $this->session->set_flashdata('exception',  validation_errors());
      redirect("journal_voucher");
    }
  }

  //Aprove voucher
  public function japasys_voucher_list()
  {
    $data['title']   = display('voucher_approval');
    $data['aprrove'] = $this->account_model->approve_voucher();
    $data['module']  = "account";
    $data['page']    = "voucher_approve";
    echo modules::run('template/layout', $data);
  }

  // isApprove
  public function isactive($id = null, $action = null)
  {
    $action   = ($action == 'active' ? 1 : 0);
    $postData = array(
      'VNo'      => $id,
      'IsAppove' => $action
    );

    if ($this->account_model->approved($postData)) {
      $this->session->set_flashdata('message', display('successfully_approved'));
    } else {
      $this->session->set_flashdata('exception', display('please_try_again'));
    }

    redirect($_SERVER['HTTP_REFERER']);
  }



  //Update voucher 
  public function voucher_update($id = null)
  {
    $vtype = $this->db->select('*')
      ->from('acc_transaction')
      ->where('VNo', $id)
      ->get()
      ->result_array();

    if ($vtype[0]['Vtype'] == "DV") {
      $data['title']          = display('update_debit_voucher');
      $data['dbvoucher_info'] = $this->account_model->dbvoucher_updata($id);
      $data['credit_info']    = $this->account_model->crvoucher_updata($id);
      $data['page']           = "update_dbt_crtvoucher";
    }

    if ($vtype[0]['Vtype'] == "JV") {
      $data['title']        = 'Update' . ' ' . display('journal_voucher');
      $data['acc']          = $this->account_model->Transacc();
      $data['voucher_info'] = $this->account_model->journal_updata($id);
      $data['page']         = "update_journal_voucher";
    }


    if ($vtype[0]['Vtype'] == "Contra") {
      $data['title']         = 'Update' . ' ' . display('contra_voucher');
      $data['acc']           = $this->account_model->Transacc();
      $data['voucher_info']  = $this->account_model->journal_updata($id);
      $data['page']         = "update_contra_voucher";
    }

    if ($vtype[0]['Vtype'] == "CV") {
      $data['title']          = display('update_credit_voucher');
      $data['crvoucher_info'] = $this->account_model->crdtvoucher_updata($id);
      $data['debit_info']     = $this->account_model->debitvoucher_updata($id);
      $data['page']           = "update_credit_bdtvoucher";
    }
    $data['crcc']           = $this->account_model->Cracc();
    $data['listkas']    = $this->account_model->listCash(); //perubahan : penambahan listkas
    $data['acc']            = $this->account_model->Transacc();
    $data['module']         = "account";
    echo modules::run('template/layout', $data);
  }

  /*updates part*/
  public function update_contra_voucher()
  {
    $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
    if ($this->form_validation->run()) {
      if ($this->account_model->update_contravoucher()) {
        $this->session->set_flashdata('message', display('successfully_updated'));
        redirect('voucher_list');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("voucher_list");
    } else {
      $this->session->set_flashdata('exception',  validation_errors());
      redirect("voucher_list");
    }
  }

  public function update_credit_voucher()
  {
    $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
    if ($this->form_validation->run()) {
      if ($this->account_model->update_creditvoucher()) {
        $this->session->set_flashdata('message', display('save_successfully'));
        redirect('voucher_list/');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("voucher_list");
    } else {
      $this->session->set_flashdata('exception',  validation_errors());
      redirect("voucher_list");
    }
  }

  // Update Debit voucher 
  public function update_debit_voucher()
  {
    $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
    if ($this->form_validation->run()) {
      if ($this->account_model->update_debitvoucher()) {
        $this->session->set_flashdata('message', display('update_successfully'));
        redirect('voucher_list');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("voucher_list");
    } else {
      $this->session->set_flashdata('exception',  validation_errors());
      redirect("accounts/voucher_list");
    }
  }

  public function update_journal_voucher()
  {
    $this->form_validation->set_rules('cmbDebit', display('cmbDebit'), 'max_length[100]');
    if ($this->form_validation->run()) {
      if ($this->account_model->update_journalvoucher()) {
        $this->session->set_flashdata('message', display('successfully_updated'));
        redirect('voucher_list');
      } else {
        $this->session->set_flashdata('exception',  display('please_try_again'));
      }
      redirect("voucher_list");
    } else {
      $this->session->set_flashdata('exception',  validation_errors());
      redirect("voucher_list");
    }
  }

  public function voucher_delete($voucher)
  {
    if ($this->account_model->delete_voucher($voucher)) {
      $this->session->set_flashdata('message', display('successfully_delete'));
    } else {
      $this->session->set_flashdata('exception', display('please_try_again'));
    }

    redirect($_SERVER['HTTP_REFERER']);
  }

  public function japasys_cash_book()
  {
    $data['title']   = display('cash_book');
    $data['module']  = "account";
    $data['page']    = "cash_book";
    echo modules::run('template/layout', $data);
  }


  // Inventory Report
  public function japasys_inventory_ledger()
  {
    $data['title']   = display('Inventory_ledger');
    $data['module']  = "account";
    $data['page']    = "inventory_ledger";
    echo modules::run('template/layout', $data);
  }

  public function japasys_bank_book()
  {
    $data['title']   = display('bank_book');
    $data['module']  = "account";
    $data['page']    = "bank_book";
    echo modules::run('template/layout', $data);
  }

  public function japasys_general_ledger()
  {
    $data['title']          = display('general_ledger');
    $data['general_ledger'] = $this->account_model->get_general_ledger();
    $data['module']         = "account";
    $data['page']           = "general_ledger";
    echo modules::run('template/layout', $data);
  }


  public function general_led($Headid = NULL)
  {
    $Headid   = $this->input->post('Headid', TRUE);
    $HeadName = $this->account_model->general_led_get($Headid);
    echo  "<option>Transaction Head</option>";
    $html = "";
    foreach ($HeadName as $data) {
      $html .= "<option value='$data->HeadCode'>$data->HeadName</option>";
    }
    echo $html;
  }

  //general ledger working
  public function accounts_report_search()
  {
    $cmbGLCode       = $this->input->post('cmbGLCode', TRUE);
    $cmbCode         = $this->input->post('cmbCode', TRUE);
    $dtpFromDate     = $this->input->post('dtpFromDate', TRUE);
    $dtpToDate       = $this->input->post('dtpToDate', TRUE);
    $chkIsTransction = $this->input->post('chkIsTransction', TRUE);
    $HeadName        = $this->account_model->general_led_report_headname($cmbGLCode);
    $HeadName2       = $this->account_model->general_led_report_headname2($cmbGLCode, $cmbCode, $dtpFromDate, $dtpToDate, $chkIsTransction);
    $pre_balance     = $this->account_model->general_led_report_prebalance($cmbCode, $dtpFromDate);

    $data = array(
      'title'          => display('general_ledger_report'),
      'dtpFromDate'    => $dtpFromDate,
      'dtpToDate'      => $dtpToDate,
      'HeadName'       => $HeadName,
      'HeadName2'      => $HeadName2,
      'prebalance'     =>  $pre_balance,
      'chkIsTransction' => $chkIsTransction,

    );

    $data['ledger']  = $this->db->select('*')->from('acc_coa')->where('HeadCode', $cmbCode)->get()->result_array();
    $data['module']  = "account";
    $data['page']    = "general_ledger_report";
    echo modules::run('template/layout', $data);
  }

  //Trial Balannce
  public function japasys_trial_balance_form()
  {
    $data['title']   = display('trial_balance');
    $data['module']  = "account";
    $data['page']    = "trial_balance";
    echo modules::run('template/layout', $data);
  }

  //Trial Balance Report
  public function japasys_trial_balance_report()
  {
    $dtpFromDate     = $this->input->post('dtpFromDate', TRUE);
    $dtpToDate       = $this->input->post('dtpToDate', TRUE);
    $chkWithOpening  = $this->input->post('chkWithOpening', TRUE);
    $results         = $this->account_model->trial_balance_report($dtpFromDate, $dtpToDate, $chkWithOpening);


    if ($results['WithOpening'] == 1) {
      $data['oResultTr']    = $results['oResultTr'];
      $data['oResultInEx']  = $results['oResultInEx'];
      $data['dtpFromDate']  = $dtpFromDate;
      $data['dtpToDate']    = $dtpToDate;
      $data['title']        = display('trial_balance');
      $data['module']       = "account";
      $data['page']         = "trial_balance_with_opening";
      echo modules::run('template/layout', $data);
    } else {

      $data['oResultTr']    = $results['oResultTr'];
      $data['oResultInEx']  = $results['oResultInEx'];
      $data['dtpFromDate']  = $dtpFromDate;
      $data['dtpToDate']    = $dtpToDate;
      $data['title']        = display('trial_balance');
      $data['module']       = "account";
      $data['page']         = "trial_balance_without_opening";
      echo modules::run('template/layout', $data);
    }
  }


  //Profit loss report page
  public function japasys_profit_loss_report_form()
  {
    $data['title']   = display('profit_loss');
    $data['module']  = "account";
    $data['page']    = "profit_loss_report";
    echo modules::run('template/layout', $data);
  }

  //Profit loss serch result
  public function japasys_profit_loss_report_search()
  {
    $dtpFromDate              = $this->input->post('dtpFromDate', TRUE);
    $dtpToDate                = $this->input->post('dtpToDate', TRUE);
    $get_profit               = $this->account_model->profit_loss_serach();
    $data['oResultAsset']     = $get_profit['oResultAsset'];
    $data['oResultLiability'] = $get_profit['oResultLiability'];
    $data['dtpFromDate']      = $dtpFromDate;
    $data['dtpToDate']        = $dtpToDate;
    $data['pdf']              = 'assets/data/pdf/Statement of Comprehensive Income From ' . $dtpFromDate . ' To ' . $dtpToDate . '.pdf';
    $data['title']            = display('profit_loss');
    $data['module']           = "account";
    $data['page']             = "profit_loss_report_search";
    echo modules::run('template/layout', $data);
  }

  //Cash flow page
  public function japasys_cash_flow_form()
  {
    $data['title']  = display('cash_flow_report');
    $data['module'] = "account";
    $data['page']   = "cash_flow_report";
    echo modules::run('template/layout', $data);
  }

  //Cash flow report search
  public function cash_flow_report_search()
  {
    $dtpFromDate          = $this->input->post('dtpFromDate', TRUE);
    $dtpToDate            = $this->input->post('dtpToDate', TRUE);
    $data['dtpFromDate']  = $dtpFromDate;
    $data['dtpToDate']    = $dtpToDate;
    $data['title']        = display('cash_flow_report');
    $data['module']       = "account";
    $data['page']         = "cash_flow_report_search";
    echo modules::run('template/layout', $data);
  }

  public function japasys_coa_print()
  {
    $data['title']        = display('coa_print');
    $data['module']       = "account";
    $data['page']         = "coa_print";
    echo modules::run('template/layout', $data);
  }

  public function japasys_balance_sheet()
  {
    $data['title']       = display('balance_sheet');
    $from_date           = (!empty($this->input->post('dtpFromDate')) ? $this->input->post('dtpFromDate') : date('Y-m-d'));
    $to_date             = (!empty($this->input->post('dtpToDate')) ? $this->input->post('dtpToDate') : date('Y-m-d'));
    $data['from_date']   = $from_date;
    $data['to_date']     = $to_date;
    $data['fixed_assets'] = $this->account_model->fixed_assets();
    $data['liabilities'] = $this->account_model->liabilities_data();
    $data['equity']     = $this->account_model->equity_data();
    $data['incomes']     = $this->account_model->income_fields();
    $data['expenses']    = $this->account_model->expense_fields();
    $data['module']      = "account";
    $data['page']        = "balance_sheet";
    echo modules::run('template/layout', $data);
  }


  public function japasys_payment_method_form($id = null)
  {
    $data['title'] = display('add_payment_method');
    #-------------------------------#
    $this->form_validation->set_rules('HeadName', display('HeadName'), 'required|max_length[200]');



    $data['pmethod'] = (object)$postData = [
      'HeadName'     => $this->input->post('HeadName', TRUE),
      'HeadCode'     => $this->input->post('HeadCode', TRUE),
    ];

    $coa = $this->payemntheadcode();
    if ($coa->HeadCode != NULL) {
      $headcode = $coa->HeadCode + 1;
    } else {
      $headcode = "111000001";
    }

    $createby   = $this->session->userdata('id');
    $createdate = date('Y-m-d H:i:s');


    $bank_coa = [
      'HeadCode'         => $headcode,
      'HeadName'         => $this->input->post('HeadName', TRUE),
      'PHeadName'        => 'Cash In Boxes',
      'HeadLevel'        => '3',
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
    #-------------------------------#
    if ($this->form_validation->run() === true) {
      if (empty($id)) {
        $check_exist_bank = $this->db->select('*')->from('acc_coa')->where('HeadName', $this->input->post('HeadName', TRUE))->get()->num_rows();
        if (empty($id)) {
          if ($check_exist_bank > 0) {
            $this->session->set_flashdata('exception', display('already_exist'));
            redirect("add_payment_method");
          }
        }

        if ($this->account_model->create_method($bank_coa)) {

          $this->session->set_flashdata('message', display('save_successfully'));
        } else {
          $this->session->set_flashdata('exception', display('please_try_again'));
        }
        redirect("payment_method_list");
      } else {

        $up_coa = array(
          'HeadName' => $this->input->post('HeadName', TRUE),
        );
        $this->db->where('HeadName', $this->input->post('old_name', TRUE))
          ->update('acc_coa', $up_coa);

        $this->session->set_flashdata('message', display('update_successfully'));


        redirect("payment_method_list");
      }
    } else {
      if (!empty($id)) {
        $data['title']    = display('edit_payment_method');
        $data['pmethod']  = $this->account_model->payment_methoddata($id);
      }
      $data['module']   = "account";
      $data['page']     = "payment_method_form";
      echo Modules::run('template/layout', $data);
    }
  }

  public function payemntheadcode()
  {

    $query = $this->db->query("SELECT MAX(HeadCode) as HeadCode FROM acc_coa WHERE PHeadName='Cash In Boxes' ");
    return $query->row();
  }

  public function payment_method_list()
  {
    $data['title']      = display('payment_method_list');
    $data['pmethods']   = $this->account_model->payment_methodlist();
    $data['module']     = "account";
    $data['page']       = "payment_method_list";
    echo Modules::run('template/layout', $data);
  }

  public function delete_payment_method($id)
  {
    if ($this->account_model->delete_payment_method($id)) {
      $this->session->set_flashdata('message', display('successfully_delete'));
    } else {
      $this->session->set_flashdata('exception', display('please_try_again'));
    }

    redirect($_SERVER['HTTP_REFERER']);
  }

  public function voucher_due_amount()
  {
    $purchase_id = $this->input->post('purchase_id');
    $due_data = $this->db->select('due_amount')->from('product_purchase')->where('purchase_id', $purchase_id)->get()->row();

    echo ($due_data->due_amount ? $due_data->due_amount : 0);
  }

  public function japasys_showpaymentmodal()
  {
    $data['all_pmethod'] = $this->account_model->pmethod_dropdown();
    $this->load->view('account/newpaymentveiw', $data);
  }

  public function customer_voucher_due_amount()
  {
    $invoice_id = $this->input->post('invoice_id');
    $due_data = $this->db->select('due_amount')->from('invoice')->where('invoice_id', $invoice_id)->get()->row();

    echo ($due_data->due_amount ? $due_data->due_amount : 0);
  }
  public function customer_service_voucher_due_amount()
  {
    $voucher_no = $this->input->post('invoice_id');
    $due_data = $this->db->select('due_amount')->from('service_invoice')->where('voucher_no', $voucher_no)->get()->row();

    echo ($due_data->due_amount ? $due_data->due_amount : 0);
  }
}
