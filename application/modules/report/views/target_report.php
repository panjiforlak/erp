<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo form_open('target_report', array('class' => 'form-inline', 'method' => 'get')) ?>
                <?php
                $stoday = date('Y-m-01');
                $today = date('Y-m-t');
                ?>
                <div class="form-group">
                    <label class="" for="from_date"><?php echo display('start_date') ?></label>
                    <input type="text" name="from_date" class="form-control datepicker" id="from_date" placeholder="<?php echo display('start_date') ?>" value="<?php echo $from_date ? $from_date : $stoday ?>">
                </div>

                <div class="form-group">
                    <label class="" for="to_date"><?php echo display('end_date') ?></label>
                    <input type="text" name="to_date" class="form-control datepicker" id="to_date" placeholder="<?php echo display('end_date') ?>" value="<?php echo $to_date ? $to_date : $today; ?>">
                </div>

                <button style="margin-top: 23px;margin-left:30px" type="submit" class="btn btn-success"><?php echo display('search') ?></button>

                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    Laporan Target Penjualan Produk
                    <span style="float: right;">
                        <a class="btn btn-sm btn-warning" href="#" onclick="printDiv('printableArea')"><?php echo display('print') ?></a>
                    </span>
                </div>
            </div>
            <div class="panel-body">
                <div id="printableArea">
                    <div class="paddin5ps">
                        <table class="print-table" width="100%">

                            <tr>

                                <td align="left" class="print-cominfo">
                                    <span style="font-size: 12pt;font-weight:bold">
                                        <?php echo $company_info[0]['company_name']; ?>

                                    </span><br>


                                </td>

                                <td align="right" class="print-table-tr">
                                    <br>

                                    <strong>Laporan Target Penjualan Produk <br>Periode <?php $per = date('F', strtotime($from_date));
                                                                                        echo $from_date ? $from_date == $to_date ? $per . ' ( ' . date('Y-m-d', strtotime($from_date)) . ' )' : $per . ' ( ' . date('Y-m-d', strtotime($from_date)) . ' s/d ' . date('Y-m-d', strtotime($to_date)) . ' )' : $period_name . ' ( ' . $stoday . ' s/d ' . $today . ' )'; ?> </strong>
                                </td>
                            </tr>

                        </table>
                    </div>
                    <div class="table-responsive paddin5ps">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center" style="padding-bottom: 15px;">No</th>
                                    <th rowspan="2" width='400' style="padding-bottom: 15px;" class="text-center">Nama Produk</th>
                                    <?php foreach ($get_sales as $key => $gs) : ?>
                                        <th colspan="2" class="text-center"><?php echo $gs['first_name'] ?></th>
                                    <?php endforeach; ?>

                                </tr>
                                <tr>
                                    <?php foreach ($get_sales as $key => $gs) : ?>
                                        <th class="text-center bg-primary">Target</th>
                                        <th class="text-center bg-success">Realisasi</th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($get_target_product_group) : ?>
                                    <?php $no = 1;
                                    foreach ($get_target_product_group as $key => $val) : ?>
                                        <tr>
                                            <td><?php echo $no++ . '.'; ?></td>
                                            <td><?php
                                                $getprod = $this->report_model->get_product_by_sku($val['product_sku']);
                                                echo  $getprod->product_id . ' - <b>' . $getprod->product_name . '</b>';
                                                ?>
                                            </td>
                                            <?php foreach ($get_sales as $gs) : ?>
                                                <td class="text-center bg-info">
                                                    <?php
                                                    $get = $this->report_model->get_target_product_bysku_bysalesid($val['product_sku'], $gs['user_id'], $period_id);
                                                    echo $get->qty; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    $ymonth = date('Y-m');
                                                    $getRealisasi = $this->report_model->get_invoice_realisasi($ymonth, $val['product_sku'], $gs['user_id'], $from_date, $to_date);
                                                    echo $getRealisasi->tot_quantity ? '<b class="text-success">' . number_format($getRealisasi->tot_quantity, 0, ',', '') . '</b>' : '<b><span class="text-danger">0</span></b>';
                                                    ?>
                                                </td>

                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <td colspan="100%" class="text-center text-danger" width=100%>Target produk tidak ditemukan pada periode <?php echo ' <b> ' . date('Y-m-d', strtotime($from_date)) . ' s/d ' . date('Y-m-d', strtotime($to_date)) . '</b> '; ?></td>
                                <?php endif; ?>
                            </tbody>


                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    Laporan Target Pendapatan Penjualan
                    <span style="float: right;">
                        <a class="btn btn-sm btn-warning" href="#" onclick="printDiv('printableArea2')"><?php echo display('print') ?></a>
                    </span>
                </div>
            </div>
            <div class="panel-body">
                <div id="printableArea2">
                    <div class="paddin5ps">
                        <table class="print-table" width="100%">

                            <tr>

                                <td align="left" class="print-cominfo">
                                    <span style="font-size: 12pt;font-weight:bold">
                                        <?php echo $company_info[0]['company_name']; ?>

                                    </span><br>


                                </td>

                                <td align="right" class="print-table-tr">
                                    <br>

                                    <strong>Laporan Target Pendapatan Penjualan <br>Periode <?php $per = date('F', strtotime($from_date));
                                                                                            echo $from_date ? $from_date == $to_date ? $per . ' ( ' . date('Y-m-d', strtotime($from_date)) . ' )' : $per . ' ( ' . date('Y-m-d', strtotime($from_date)) . ' s/d ' . date('Y-m-d', strtotime($to_date)) . ' )' : $period_name . ' ( ' . $stoday . ' s/d ' . $today . ' )'; ?> </strong>
                                </td>
                            </tr>

                        </table>
                        <div class="table-responsive paddin5ps">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center" style="padding-bottom: 15px;">No</th>
                                        <th rowspan="2" width='200' style="padding-bottom: 15px;" class="text-center">Pelanggan</th>

                                        <?php foreach ($get_sales as $key => $gs) : ?>
                                            <th colspan="4" class="text-center"><?php echo $gs['first_name'] ?></th>
                                        <?php endforeach; ?>

                                    </tr>
                                    <tr>
                                        <?php foreach ($get_sales as $key => $gs) : ?>
                                            <th class="text-center bg-info">Invoice</th>
                                            <th class="text-center bg-warning">Penjualan</th>
                                            <th class="text-center bg-danger">Retur</th>
                                            <th class="text-center bg-success">Realisasi</th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($get_customer_invoice_group as $key => $val) : ?>
                                        <tr>
                                            <td><?php echo $no++ . '.'; ?></td>
                                            <td><?php echo $val['customer_name']; ?></td>
                                            <?php foreach ($get_sales as $gs) : ?>

                                                <?php
                                                $get = $this->report_model->get_invoice_bysalesid($val['customer_id'], $gs['user_id']);
                                                $ret = $this->report_model->get_invoice_retur($get->invoice_id);
                                                ?>
                                                <td>
                                                    <?php echo $get->invoice; ?></td>
                                                <td class="text-right"><span style="float: left;">Rp.</span><?php echo number_format($get->total_amount, 0, ',', '.'); ?></td>
                                                <td class="text-right"><span style="float: left;">Rp.</span><?php echo number_format($ret->net_total_amount, 0, ',', '.'); ?></td>
                                                <td class="text-right"><span style="float: left;">Rp.</span><?php echo number_format($get->paid_amount, 0, ',', '.'); ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>