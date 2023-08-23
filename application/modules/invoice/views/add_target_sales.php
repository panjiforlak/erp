<!-- Invoice js -->
<script src="<?php echo base_url() ?>my-assets/js/admin_js/invoice.js" type="text/javascript"></script>



<!--Add Invoice -->
<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Target Penjualan Produk - <span class="text-danger"> <?php echo strtoupper($get_periode[0]['period']) . ' (' . date('d/m/Y', strtotime($get_periode[0]['start_date'])) . ' - ' . date('d/m/Y', strtotime($get_periode[0]['end_date'])) . ')'; ?></span></span>

                </div>
            </div>

            <div class="panel-body">
                <?php echo form_open_multipart('invoice/invoice/japasys_target_product_insert', array('class' => 'form-inline')) ?>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <div class="form-group row">
                                    <label for="product_name_1" class="col-sm-4 col-form-label" style="margin-top: 5px;">Product</label>
                                    <div class="col-sm-8">
                                        <input type="text" required name="product_name" onkeypress="invoice_productList(1)" id="product_name_1" class="form-control productSelection" placeholder="<?php echo display('product_name') ?>" tabindex="5">
                                        <input type="hidden" class="autocomplete_hidden_value product_id_1" name="product_id" id="SchoolHiddenId" />
                                        <input type="hidden" name="period_id" id="" value="<?php echo $get_periode[0]['id']; ?>" />
                                        <input type="hidden" class="baseUrl" value="<?php echo base_url(); ?>" />
                                    </div>
                                </div>

                                <hr>
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Salesman</th>
                                            <th class="text-center">Target Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        foreach ($get_sales as $gs) : ?>
                                            <tr>
                                                <td><?php echo $no++; ?>.</td>
                                                <td><input type="hidden" name="sales_id[]" value="<?php echo $gs['user_id']; ?>"><?php echo $gs['first_name'] . ' ' . $gs['last_name']; ?></td>
                                                <td><input type="number" name="target_qty[]" onkeypress="return event.charCode >= 48 && event.charCode <=57" class="form-control" required></td>
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
    <div class="col-sm-8">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Target by Produk</span>
                    <hr>
                    <table class="table table-sm table-hover table-striped table-bordered datatable" style="font-size: 10pt;" id="dataTableExample2" cellspacing="0" width="100%">
                        <thead class="bg-success">
                            <tr>
                                <th>Nama Produk</th>
                                <?php foreach ($get_sales as $gs) : ?>
                                    <th class="text-center"><?php echo $gs['first_name']; ?></th>
                                <?php endforeach; ?>
                                <th class="text-center bg-danger"><?php echo display('delete'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($get_target_product_group) : ?>
                                <?php foreach ($get_target_product_group as $key => $val) : ?>

                                    <tr>
                                        <td><?php
                                            $getprod = $this->invoice_model->get_product_by_sku($val['product_sku']);
                                            echo $getprod->product_name;
                                            ?>
                                        </td>
                                        <?php foreach ($get_sales as $gs) : ?>
                                            <td class="text-center">
                                                <?php
                                                $get = $this->invoice_model->get_target_product_bysku_bysalesid($val['product_sku'], $gs['user_id'], $get_periode[0]['id']);
                                                echo $get->qty; ?>
                                            </td>

                                        <?php endforeach; ?>

                                        <td class="text-center">
                                            <a href="<?php echo base_url() . 'target_delete/' . $get_periode[0]['id'] . '/' . $val['product_sku']; ?>" class="" data-toggle="tooltip" data-placement="left" title="<?php echo display('delete') ?>"><i class="text-danger fa fa-times" style="font-size: 20px;" aria-hidden="true"></i></a>
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