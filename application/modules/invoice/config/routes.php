<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['add_invoice']         = "invoice/invoice/japasys_invoice_form";
$route['pos_invoice']         = "invoice/invoice/japasys_pos_invoice";
$route['gui_pos']             = "invoice/invoice/japasys_gui_pos";
$route['invoice_list']        = "invoice/invoice/japasys_invoice_list";
$route['invoice_details/(:num)'] = 'invoice/invoice/japasys_invoice_details/$1';
$route['invoice_pad_print/(:num)'] = 'invoice/invoice/japasys_invoice_pad_print/$1';
$route['inv_delete/(:num)/(:num)']    = 'invoice/invoice/japasys_delete_invoice/$1/$2';
$route['pos_print/(:num)']    = 'invoice/invoice/japasys_invoice_pos_print/$1';
$route['invoice_pos_print']    = 'invoice/invoice/japasys_pos_print_direct';
$route['download_invoice/(:num)']  = 'invoice/invoice/japasys_download_invoice/$1';
$route['invoice_edit/(:num)'] = 'invoice/invoice/japasys_edit_invoice/$1';
$route['invoice_print'] = 'invoice/invoice/invoice_inserted_data_manual';

$route['terms_list'] = 'invoice/invoice/japasys_terms_list';
$route['terms_add'] = 'invoice/invoice/japasys_terms_form';
$route['terms_add/(:num)'] = 'invoice/invoice/japasys_terms_form/$1';

$route['target_invoice']         = "invoice/invoice/japasys_target_form";
$route['target_product/(:num)']         = "invoice/invoice/japasys_target_sales_form/$1";
$route['target_amount/(:num)']         = "invoice/invoice/japasys_target_amount_form/$1";
$route['target_delete/(:num)/(:any)']         = "invoice/invoice/japasys_target_product_delete/$1/$2";
$route['target_period_delete/(:num)']         = "invoice/invoice/japasys_target_delete/$1";
$route['target_amount_delete/(:num)']         = "invoice/invoice/japasys_target_amount_delete/$1";
