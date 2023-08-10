<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['stock']          = "report/report/japasys_stock_report";
$route['reports/(:num)'] = 'report/report/japasys_purchase_edit_form/$1';
$route['closing_form']   = "report/report/japasys_cash_closing";
$route['closing_report'] = "report/report/japasys_closing_report";
$route['closing_report_search'] = "report/report/japasys_closing_report_search";
$route['todays_report']  = "report/report/japasys_todays_report";
$route['todays_customer_received']  = "report/report/japasys_todays_customer_received";
$route['todays_customerwise_received']  = "report/report/japasys_customerwise_received";
$route['sales_report']  = "report/report/japasys_todays_sales_report";
$route['datewise_sales_report']  = "report/report/japasys_datewise_sales_report";
$route['userwise_sales_report']  = "report/report/japasys_userwise_sales_report";
$route['invoice_wise_due_report'] = "report/report/japasys_invoice_wise_due_report";
$route['shipping_cost_report'] = "report/report/japasys_shippingcost_report";
$route['purchase_report']     = "report/report/japasys_purchase_report";
$route['purchase_report_categorywise'] = "report/report/japasys_purchase_report_category_wise";
$route['product_wise_sales_report'] = "report/report/japasys_sale_report_productwise";
$route['category_sales_report'] = "report/report/japasys_categorywise_sales_report";
$route['sales_return']         = "report/report/japasys_sales_return";
$route['supplier_returns']      = "report/report/japasys_supplier_return";
$route['tax_report']           = "report/report/japasys_tax_report";
$route['profit_report']        = "report/report/japasys_profit_report";
