<!-- Invoice js -->
<script src="<?php echo base_url() ?>my-assets/js/admin_js/invoice.js" type="text/javascript"></script>



<!--Add Invoice -->
<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Target Sales</span>

                </div>
            </div>

            <div class="panel-body">
                <?php echo form_open_multipart('invoice/invoice/japasys_target_period_insert', array('class' => 'form-inline')) ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <form>
                                    <div class="form-group row">
                                        <label for="product_name_1" class="col-sm-4 col-form-label" style="margin-top: 5px;">Product</label>
                                        <div class="col-sm-8">
                                            <input type="text" required name="product_name" onkeypress="invoice_productList(1)" id="product_name_1" class="form-control productSelection" placeholder="<?php echo display('product_name') ?>" tabindex="5">
                                            <input type="hidden" class="autocomplete_hidden_value product_id_1" name="product_id[]" id="SchoolHiddenId" />
                                            <input type="hidden" class="baseUrl" value="<?php echo base_url(); ?>" />
                                        </div>
                                    </div>

                                    <hr>
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Salesman</th>
                                                <th>Target</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1;
                                            foreach ($get_sales as $gs) : ?>
                                                <tr>
                                                    <td><?php echo $no++; ?>.</td>
                                                    <td><input type="hidden" name="sales_id[]" value="<?php echo $gs['user_id']; ?>"><?php echo $gs['first_name'] . ' ' . $gs['last_name']; ?></td>
                                                    <td><input type="number" name="target[]" class="form-control"></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>


                                </form>


                            </div>
                        </div>
                    </div>
                </div>


                <center>
                    <input type="submit" class="btn btn-success" value="Set Target" tabindex="17" />
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
                    <span>Periode <?php echo $get_periode[0]['period'] . ' ' . date('Y', strtotime($get_periode[0]['start_date'])); ?></span>
                    <hr>
                    <table class="table table-sm table-hover table-striped" style="font-size: 10pt;" id="dataTableExample2" cellspacing="0" width="100%">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th>Tools</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($get_periode as $gp) : ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php
                                        $date = strtotime($gp['start_date']);
                                        $dat = date('Y', $date);
                                        echo $dat;
                                        ?></td>
                                    <td><?php echo $gp['period']; ?></td>
                                    <td>
                                        <a href="<?php echo base_url() . 'target_invoice/' . $gp['id']; ?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="<?php echo display('update') ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        <button class="btn btn-sm btn-primary">Atur Target</button>
                                        <button style="margin-left: 2px;" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>