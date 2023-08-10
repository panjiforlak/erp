<?php
defined('BASEPATH') or exit('No direct script access allowed');



$route['add_customer']         = "customer/customer/japasys_form";
$route['customer_list']        = "customer/customer/index";
$route['edit_customer/(:num)'] = 'customer/customer/japasys_form/$1';
$route['credit_customer']      = "customer/customer/japasys_credit_customer";
$route['paid_customer']        = "customer/customer/japasys_paid_customer";
$route['customer_ledger']      = "customer/customer/japasys_customer_ledger";
$route['customer_ledger/(:num)']      = "customer/customer/japasys_customer_ledger/$1";
$route['customer_ledgerdata']  = "customer/customer/japasys_customer_ledgerData";
$route['customer_advance']     = "customer/customer/japasys_customer_advance";
$route['advance_receipt/(:any)/(:num)'] = "customer/customer/customer_advancercpt/$1/$1";
