<?php
defined('BASEPATH') or exit('No direct script access allowed');



$route['add_purchase']         = "purchase/purchase/japasys_purchase_form";
$route['purchase_list']        = "purchase/purchase/japasys_purchase_list";
$route['purchase_details/(:num)'] = 'purchase/purchase/japasys_purchase_details/$1';
$route['purchase_edit/(:num)'] = 'purchase/purchase/japasys_purchase_edit_form/$1';
