<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['bank_form']               = "bank/bank/japasys_bank_form";
$route['bank_form/(:num)']        = "bank/bank/japasys_bank_form/$1";
$route['bank_list']               = "bank/bank/japasys_bank_list";
$route['bank_transaction']        = "bank/bank/japasys_bank_transaction";
$route['bank_ledger']             = "bank/bank/bank_ledger";
