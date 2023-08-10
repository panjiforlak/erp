<link href="<?php echo base_url('application/modules/account/assets/css/accounts_style.css'); ?>" rel="stylesheet" type="text/css" />
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo form_open('balance_sheet', array('class' => 'form-inline', 'method' => 'post')) ?>
                <?php
                $today = date('Y-m-d');
                ?>
                <div class="form-group">
                    <label class="" for="from_date"><?php echo display('start_date') ?></label>
                    <input type="text" name="dtpFromDate" class="form-control datepicker" id="from_date" placeholder="<?php echo display('start_date') ?>" value="<?php echo $from_date ?>">
                </div>

                <div class="form-group">
                    <label class="" for="to_date"><?php echo display('end_date') ?></label>
                    <input type="text" name="dtpToDate" class="form-control datepicker" id="to_date" placeholder="<?php echo display('end_date') ?>" value="<?php echo $to_date ?>">
                </div>

                <button type="submit" class="btn btn-success"><?php echo display('search') ?></button>

                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <div class="text-right" id="print">
                        <input type="button" class="btn btn-warning" name="btnPrint" id="btnPrint" value="Print" onclick="printDiv('printArea');" />
                    </div>
                </div>
            </div>
            <div class="panel-body" id="printArea">
                <div class="paddin5ps">
                    <table class="print-table " width="100%">

                        <tr>
                            <td align="left" class="print-table-tr">
                                <img src="<?php echo base_url() . $setting->logo; ?>" alt="logo">
                            </td>
                            <td align="center" class="print-cominfo">
                                <span class="company-txt">
                                    <?php echo $company_info[0]['company_name']; ?>

                                </span><br>
                                <?php echo $company_info[0]['address']; ?>
                                <br>
                                <?php echo $company_info[0]['email']; ?>
                                <br>
                                <?php echo $company_info[0]['mobile']; ?>

                            </td>

                            <td align="right" class="print-table-tr">
                                <date>
                                    <?php echo display('date') ?>: <?php
                                                                    echo date('d-M-Y');
                                                                    ?>
                                </date>
                            </td>
                        </tr>

                    </table>
                    <table width="100%" class="table_boxnew table-hover " style="border:1px solid;" cellpadding="0" cellspacing="0">
                        <tr class="table_head">
                            <td width="73%" height="35" align="center" class="cashflowparticular">
                                <b><?php echo display('particulars'); ?></b>
                            </td>

                            <td width="30%" align="right" class="cashflowamount"><b><?php echo display('amount'); ?></b>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px;font-weight:bold">Aset</td>
                        </tr>
                        <?php

                        foreach ($fixed_assets as $assets) {
                            $total_assets = 0;
                            $head_data = $this->account_model->assets_info($assets['HeadName']);


                        ?>

                            <tr>

                                <td align="left" style="padding-top: 10px;padding-left:30px;" class="paddingleft10px <?php if ($assets['HeadName'] == 'Current Assets' || $assets['HeadName'] == 'Non-current assets' || $assets['HeadName'] == 'Fixed Assets') {
                                                                                                                            echo 'balancesheet_head';
                                                                                                                        } ?>">
                                    <!-- perubahan -->
                                    <b> <?php echo $assets['account_name_indo'] ?: $assets['HeadName']; ?></b>
                                </td>

                                <td align="right" style="padding-right: 10px;">

                                </td>
                            </tr>
                            <?php

                            foreach ($head_data as $assets_head) {

                                $ass_balance = $this->account_model->assets_balance($assets_head['HeadCode'], $from_date, $to_date); ?>
                                <?php if ($assets_head['PHeadName'] == 'Current Assets') {
                                    $child_head_current = $this->account_model->asset_childs($assets_head['HeadName'], $from_date, $to_date);

                                ?>
                                    <!-- <tr>
                                      
                                        <td align="left" style="padding-left: 20px;"><?php echo $assets_head['account_name_indo'] ?: $assets_head['HeadName']; ?></td>

                                        <td align="right" class="cashflowamnt">

                                        </td>
                                    </tr> -->

                                    <?php foreach ($child_head_current as $cchead) {
                                        $cur_ass_balance = $this->account_model->assets_balance($cchead['HeadCode'], $from_date, $to_date);
                                        $schild_head_current = $this->account_model->asset_childs($cchead['HeadName'], $from_date, $to_date);
                                    ?>
                                        <?php if ($cur_ass_balance[0]['balance'] <> 0) { ?>
                                            <tr>
                                                <!-- perubahan -->
                                                <td align="left" style="padding-left: 60px;">
                                                    <?php if ($cchead['account_name_indo']) {
                                                        if ($cchead['account_name_indo'] == 'Pelanggan') {
                                                            echo 'Piutang Dagang';
                                                        } else {
                                                            echo $cchead['account_name_indo'];
                                                        }
                                                    } else {
                                                        echo $cchead['HeadName'];
                                                    } ?>
                                                </td>

                                                <td align="right" style="padding-right: 10px;">
                                                    <?php echo number_format($cur_ass_balance[0]['balance'], 2);
                                                    $total_assets += $cur_ass_balance[0]['balance'];
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php if ($cchead['HeadName'] == 'Cash in Banks' || $cchead['HeadName'] == 'Account Receivable'  || $cchead['HeadName'] == 'Loan Receivable') {
                                            foreach ($schild_head_current as $scchild) {
                                                $cur_bank_balance = $this->account_model->assets_balance($scchild['HeadCode'], $from_date, $to_date);
                                        ?>
                                                <?php if ($cur_bank_balance[0]['balance'] <> 0) { ?>
                                                    <tr>
                                                        <td align="left" style="padding-left: 60px;"><?php echo $scchild['HeadName']; ?></td>

                                                        <td align="right" style="padding-right: 10px;">
                                                            <?php echo number_format($cur_bank_balance[0]['balance'], 2);
                                                            $total_assets += $cur_bank_balance[0]['balance'];
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                        <?php }
                                        } ?>
                                    <?php } ?>
                                <?php } ?>

                                <?php if ($assets_head['PHeadName'] == 'Non-current assets') { ?>
                                    <tr>
                                        <!-- perubahan -->
                                        <td align="left" style="padding-left: 60px;"><?php echo $assets_head['account_name_indo'] ?: $assets_head['HeadName']; ?></td>

                                        <td align="right" style="padding-right: 10px;">
                                            <?php echo number_format($ass_balance[0]['balance'], 2);
                                            $total_assets += $ass_balance[0]['balance'];
                                            ?>
                                        </td>
                                    </tr>

                                <?php } ?>

                            <?php } ?>

                            <tr>
                                <td class="text-left " style="padding-left: 60px;padding-bottom:10px;"> <b><?php echo display('total') ?>-
                                        <!-- perubahan -->
                                        <?php echo $assets['account_name_indo'] ?: $assets['HeadName']; ?></b>&nbsp;&nbsp;</td>

                                <td align="right" style="padding-right: 10px;font-weight:bold;border-right:1px solid;border-top:1px solid;border-bottom:1px solid">
                                    <b><?php echo number_format($total_assets, 2); ?></b>
                                </td>
                            </tr>

                        <?php
                            $sum_total_aset += $total_assets;
                        } ?>


                        <tr style="border-top:1px solid;border:1px solid;">
                            <td style="padding:10px;font-weight:bold;">Total Aset</td>
                            <td align="right" style="padding-right: 10px;font-weight:bold;">
                                <?php echo number_format($sum_total_aset, 2); ?>
                            </td>
                        </tr>

                        <!-- liabilitas -->
                        <tr>
                            <td style="padding: 10px;font-weight:bold">Kewajiban</td>
                        </tr>
                        <?php

                        foreach ($liabilities as $liability) {

                            $total_liab = 0;
                            $liab_head_data = $this->account_model->liabilities_info($liability['HeadName']);

                        ?>

                            <tr>
                                <td align="left" style="padding-left: 30px;" class="paddingleft10px <?php if ($liability['HeadName'] == 'Current Liabilities' || $liability['HeadName'] == 'Non-Current liabilities') {
                                                                                                        echo 'balancesheet_head';
                                                                                                    } ?>">
                                    <b><?php echo $liability['account_name_indo'] ?: $liability['HeadName']; ?></b>
                                </td>

                                <td align="right" class="">

                                </td>
                            </tr>
                            <?php

                            foreach ($liab_head_data as $liab_head) {

                                if ($liab_head['HeadName'] == 'Tax') {
                                    $child_liability = $this->account_model->liabilities_info_tax($liab_head['HeadName']);
                                } else {
                                    $child_liability = $this->account_model->liabilities_info($liab_head['HeadName']);
                                }
                            ?>
                                <?php if ($liab_head['HeadName'] != 'Tax') { ?>

                                    <!-- <tr>
                                     
                                        <td align="left" class="paddingleft10px"><?php echo $liab_head['account_name_indo'] ?: $liab_head['HeadName']; ?></td>

                                        <td align="right" class="cashflowamnt">
                                            <?php
                                            $total_liab += 0;
                                            ?>
                                        </td>
                                    </tr> -->
                                <?php } ?>

                                <?php

                                foreach ($child_liability as $chliab_head) {
                                    $liab_balance = $this->account_model->liabilities_balance($chliab_head['HeadCode'], $from_date, $to_date);


                                ?>
                                    <?php if ($liab_balance[0]['balance'] <> 0) { ?>
                                        <tr>
                                            <!-- perubahan -->
                                            <td align="left" style="padding-left: 60px;"><?php echo $chliab_head['account_name_indo'] ?: $chliab_head['HeadName']; ?></td>

                                            <td align="right" class="" style="padding-right: 10px;">
                                                <?php

                                                echo  number_format($liab_balance[0]['balance'], 2);
                                                $total_liab += $liab_balance[0]['balance'];
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                <?php } ?>
                            <?php } ?>
                            <tr>
                                <td style="padding-left: 60px;padding-bottom:10px"><b><?php echo display('total') ?>-
                                        <!-- perubahan -->
                                        <?php echo $liability['account_name_indo'] ?: $liability['HeadName']; ?></b>&nbsp;&nbsp;</td>

                                <td align="right" style="padding-right: 10px;font-weight:bold;border-right:1px solid;border-top:1px solid;border-bottom:1px solid">
                                    <b><?php echo number_format($total_liab, 2); ?></b>
                                </td>
                            </tr>
                        <?php $sum_liability += $total_liab;
                        } ?>
                        <!-- Ekuitas -->
                        <tr>
                            <td style="padding: 10px;font-weight:bold">Ekuitas</td>
                        </tr>
                        <?php foreach ($equity as $equi) {
                            $total_equi = 0;
                            $equi_balance = $this->account_model->equity_balance($equi['HeadCode'], $from_date, $to_date);
                        ?>
                            <?php if ($equi_balance[0]['balance'] <> 0) { ?>

                                <tr>
                                    <td align="left" style="padding-left: 30px;">
                                        <?php echo $equi['account_name_indo'] ?: $equi['HeadName']; ?>
                                    </td>

                                    <td align="right" style="padding-right: 10px;">
                                        <?php
                                        echo  number_format($equi_balance[0]['balance'], 2);
                                        $total_equi += number_format($equi_balance[0]['balance'], 2);
                                        ?>
                                    </td>
                                </tr>
                            <?php $sum_total_equitas += $equi_balance[0]['balance'];
                            } ?>
                        <?php } ?>
                        <tr>
                            <td style="padding-left: 30px;">Laba ditahan</td>
                            <td align="right" style="padding-right: 10px;">
                                <?php
                                echo  number_format($sum_total_aset - ($sum_liability + $sum_total_equitas), 2);

                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left " style="padding-left: 30px;padding-bottom:10px"> <b><?php echo display('total') ?>- Ekuitas
                            <td align="right" style="padding-right: 10px;font-weight:bold;border-right:1px solid;border-top:1px solid;border-bottom:1px solid">
                                <b><?php echo number_format($sum_total_equitas + ($sum_total_aset - ($sum_liability + $sum_total_equitas)), 2); ?></b>
                            </td>
                        </tr>
                        <!-- total kewajiban + equitas -->
                        <tr style="border-top:1px solid;border:1px solid;">
                            <td style="padding:10px;font-weight:bold;">Total Kewajiban & Ekuitas</td>
                            <td align="right" style="padding-right: 10px;font-weight:bold;">
                                <?php echo number_format($sum_liability + $sum_total_equitas + ($sum_total_aset - ($sum_liability + $sum_total_equitas)), 2); ?>
                            </td>
                        </tr>

                        <!-- expense & revenue -->
                        <!-- <?php
                                $total_expense = 0;
                                $total_income = 0;
                                foreach ($incomes as $incomelable) {
                                    $income_balance = $this->account_model->income_balance($incomelable['HeadCode'], $from_date, $to_date);
                                ?>
                            <tr>
                                <td align="left" class="paddingleft10px balancesheet_head">
                                 
                                    <?php echo $incomelable['account_name_indo'] ?: $incomelable['HeadName']; ?>
                                </td>

                                <td align="right" class="cashflowamnt">
                                    <?php echo $income_balance[0]['balance'];
                                    $total_income += $income_balance[0]['balance'];
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td class="paddingleft10px text-right"><b><?php echo display('total') ?>
                                    Pendapatan</b>&nbsp;&nbsp;</td>

                            <td align="right" class="cashflowamnt bal-sh-01">
                                <b><?php echo number_format($total_income, 2); ?></b>
                            </td>
                        </tr>
                        <?php

                        foreach ($expenses as $expense) {
                            $expense_balance = $this->account_model->income_balance($expense['HeadCode'], $from_date, $to_date);
                        ?>
                            <tr>
                                <td align="left" class="paddingleft10px balancesheet_head">
                                    <?php echo $expense['account_name_indo'] ?: $expense['HeadName']; ?></td>

                                <td align="right" class="cashflowamnt">
                                    <?php echo $expense_balance[0]['balance'];
                                    $total_expense += $expense_balance[0]['balance'];
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td class="paddingleft10px text-right"><b><?php echo display('total') ?>
                                    <?php echo 'BIAYA'; ?></b>&nbsp;&nbsp;</td>

                            <td align="right" class="cashflowamnt bal-sh-01">
                                <b><?php echo number_format($total_expense, 2); ?></b>
                            </td>
                        </tr> -->

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>