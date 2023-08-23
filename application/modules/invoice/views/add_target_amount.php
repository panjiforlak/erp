<!-- Invoice js -->
<script src="<?php echo base_url() ?>my-assets/js/admin_js/invoice.js" type="text/javascript"></script>



<!--Add Invoice -->
<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Target Pendapatan Penjualan - <span class="text-danger"> <?php echo strtoupper($get_periode[0]['period']) . ' (' . date('d/m/Y', strtotime($get_periode[0]['start_date'])) . ' - ' . date('d/m/Y', strtotime($get_periode[0]['end_date'])) . ')'; ?></span></span>
                </div>
            </div>

            <div class="panel-body">
                <?php echo form_open_multipart('invoice/invoice/japasys_target_amount_insert', array('class' => 'form-inline')) ?>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Salesman</th>
                                            <th class="text-center">Target Pendapatan (Rp.)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        foreach ($get_sales as $gs) : ?>
                                            <tr>
                                                <td><?php echo $no++; ?>.</td>
                                                <input type="hidden" name="period_id" id="" value="<?php echo $get_periode[0]['id']; ?>" />

                                                <td><input type="hidden" name="sales_id[]" value="<?php echo $gs['user_id']; ?>"><?php echo $gs['first_name'] . ' ' . $gs['last_name']; ?></td>
                                                <td><input type="number" name="amount[]" onkeypress="return event.charCode >= 48 && event.charCode <=57" class="form-control" required></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <center>
                    <input type="submit" class="btn btn-success" value="Simpan" tabindex="17" />
                    <a href="<?php echo base_url('target_invoice'); ?>" class="btn btn-danger" tabindex="17">Back</a>
                </center>

                <?php echo form_close() ?>
            </div>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Target Pendapatan</span>
                    <span style="float: right;"><a href="<?php echo base_url('target_amount_delete/' . $get_periode[0]['id']); ?>" class="btn btn-sm btn-danger"><?php echo display('delete'); ?></a></span>

                    <hr>
                    <table class="table table-sm table-hover table-striped table-bordered " style="font-size: 10pt;" id="dataTableExample2" cellspacing="0" width="100%">
                        <thead class="bg-success">
                            <tr>

                                <th class="text-center">Salesman</th>
                                <th class="text-center">Amount Target</th>


                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($get_target_amount) : ?>
                                <?php foreach ($get_target_amount as $key => $val) : ?>

                                    <tr>

                                        <td class="text-left">
                                            <?php $getSales = $this->invoice_model->get_sales($val['sales_id']);
                                            echo $getSales[0]['first_name'] . ' ' . $getSales[0]['last_name']; ?>
                                        </td>

                                        <td class="text-right">
                                            <?php echo '<span style="float:left ;">Rp.</span>' . number_format($val['amount'], 0, ',', '.'); ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td class="text-center text-danger" colspan="100%" style="font-weight: unset;">Belum ada target penjualan produk!</td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>