<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
<style>
    *,
    ::after,
    ::before {
        box-sizing: border-box;
    }

    body {
        padding: 0;
        font-family: Lato, "Helvetica Neue", Arial, Helvetica, sans-serif;
    }
</style>



</html>
<div class="row">
    <div class="col-sm-12">

        <!-- print -->

        <head>
            <title>Faktur penjualan</title>
            <style>
                #tabel {
                    font-size: 15px;
                    border-collapse: collapse;
                }

                #tabel td {
                    padding-left: 5px;
                    border: 1px solid black;
                }
            </style>
        </head>


        <body style='font-family:tahoma; font-size:10pt;'>

            <table style='width:750px; font-size:10pt; font-family:calibri; border-collapse: collapse; ' border='0'>
                <tr>
                    <td colspan="3">
                        <center><b style="font-size:18px;">FAKTUR PENJUALAN<b></center>
                        <hr style="margin-bottom: 0px; margin-top:2px">
                    </td>
                </tr>

                <tr>
                    <td width='35%' align='left' style='padding-right:0px; vertical-align:top'>
                        <span style='font-size:11pt'><b><?php echo strtoupper($company_info[0]['company_name']); ?></b></span></br>
                        <?php echo $company_info[0]['address']; ?><br>
                        <?php echo $company_info[0]['mobile']; ?><br>

                    </td>
                    <td width='35%' align='left' style='padding-right:80px; vertical-align:top'>
                        Salesman <span style="margin-left: 12px;">:</span> <?php echo $users_name; ?></br>
                        Pelanggan<span style="margin-left: 11px;">:</span> <b><?php echo $customer_name; ?></b></br>
                        Alamat <span style="margin-left: 12px;">:</span> <b><?php echo $customer_address; ?></b></br>

                    </td>

                    <?php $create_at = $this->db->select('CreateDate')
                        ->from('acc_transaction')
                        ->where('VNo', $invoice_id)
                        ->get()
                        ->row(); ?>
                    <td style='vertical-align:top' width='30%' align='left'>
                        No Faktur <span style="margin-left: 12px;">&nbsp;:</span> <b><?php echo $invoice_no; ?></b></br>
                        Tgl Invoice <span style="margin-left: 4px;">&nbsp;:</span> <?php echo date('d F Y', strtotime($final_date)); ?> <?php echo date("H:i", strtotime($create_at->CreateDate)); ?></th></br>
                        Jatuh Tempo<span style="margin-left: 4px;">:</span> <?php echo date('d F Y', strtotime($due_date)); ?></br>

                    </td>

                </tr>
            </table>



            <table cellspacing='0' style='width:750px; font-size:10pt; font-family:calibri; '>

                <tr align='center'>
                    <td style="border-collapse: collapse; border:1px solid" rowspan="2" width='4%'><b>No.</b></td>
                    <td style="border-collapse: collapse; border:1px solid" rowspan="2" width='30%'><b>Nama Barang</b></td>
                    <td style="border-collapse: collapse; border:1px solid" rowspan="2" width='8%'><b>Qty</b></td>
                    <td style="border-collapse: collapse; border:1px solid" rowspan="2" width='10%'><b>Harga Sat</b></td>
                    <td style="border-collapse: collapse; border:1px solid" colspan="2" width='11%'><b>Disc 1</b></td>
                    <td style="border-collapse: collapse; border:1px solid" colspan="2" width='11%'><b>Disc 2</b></td>
                    <td style="border-collapse: collapse; border:1px solid" colspan="2" width='11%'><b>Disc 3</b></td>
                    <td style="border-collapse: collapse; border:1px solid" rowspan="2" width='15%'><b>Jumlah</b></td>
                </tr>
                <tr align="center" style="border-collapse: collapse; border:1px solid">
                    <td width='5%' style="border-collapse: collapse; border:1px solid"><b>%</b></td>
                    <td width='5%' style="border-collapse: collapse; border:1px solid"><b>Rp</b></td>
                    <td width='5%' style="border-collapse: collapse; border:1px solid"><b>%</b></td>
                    <td width='5%' style="border-collapse: collapse; border:1px solid"><b>Rp</b></td>
                    <td width='5%' style="border-collapse: collapse; border:1px solid"><b>%</b></td>
                    <td width='5%' style="border-collapse: collapse; border:1px solid"><b>Rp</b></td>
                </tr>
                <tbody>
                    <?php $no = 0;
                    $sl = 1;
                    $s_total = 0;
                    $itemrow = 0;
                    $total_price_with_dis = 0;
                    foreach ($invoice_all_data as $invoice_data) : $no++; ?>
                        <tr style="border-collapse: collapse; border-top:1px solid">
                            <td style="border-collapse: collapse; border:1px solid;text-align: center;"><?php echo $no; ?></td>
                            <td style="border-collapse: collapse; border:1px solid"><?php echo $invoice_data['product_name']; ?></td>
                            <td style="border-collapse: collapse; border:1px solid;text-align: center;"><?php echo number_format($invoice_data['quantity'], 0, '.', ',') . ' ' . $invoice_data['unit']; ?></td>
                            <td style="border-collapse: collapse; border:1px solid;text-align: right;"><span style="float: left;margin-left:2px;">Rp. </span><?php echo number_format($invoice_data['rate']); ?></td>
                            <td style="border-collapse: collapse; border:1px solid;text-align: center;"><span style="float: left;margin-left:2px;"></span><?php echo number_format($invoice_data['discount_per'],1); ?></td>
                            <td style="border-collapse: collapse; border:1px solid;text-align: center;"><span style="float: left;margin-left:2px;"></span><?php echo number_format($invoice_data['discount']); ?></td>
                            <td style="border-collapse: collapse; border:1px solid;text-align: center;"><span style="float: left;margin-left:2px;"></span><?php echo number_format($invoice_data['discount_per2'],1); ?></td>
                            <td style="border-collapse: collapse; border:1px solid;text-align: center;"><span style="float: left;margin-left:2px;"></span><?php echo number_format($invoice_data['discount2']); ?></td>
                            <td style="border-collapse: collapse; border:1px solid;text-align: center;"><span style="float: left;margin-left:2px;"></span><?php echo number_format($invoice_data['discount_per3'],1); ?></td>
                            <td style="border-collapse: collapse; border:1px solid;text-align: center;"><span style="float: left;margin-left:2px;"></span><?php echo number_format($invoice_data['discount3']); ?></td>
                            <td style="border-collapse: collapse; border:1px solid;text-align: right;"><span style="float: left;margin-left:2px;">Rp.</span><?php echo number_format($invoice_data['total_price']); ?></td>
                        </tr>
                        <?php
                        $itemrow += $invoice_data['rate'] * $invoice_data['quantity'];
                        $total_price_with_dis += $invoice_data['total_price'];

                        ?>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="4" rowspan="2" style="text-align: left; "><span style="font-style: italic;font-weight:bold"><?php echo $am_inword; ?> Rupiah</span></td>
                        <td colspan="6" style="text-align: left;">Subtotal Sebelum Diskon</td>
                        <td style="text-align: left; font-size:10pt;font-weight:bold">Rp. <span style="float: right;margin-left:2px;"> <?php echo number_format($itemrow, 0, '.', ','); ?></td>
                    </tr>
                    <tr>
                        <!-- <td colspan="4" style="text-align: center;"></td> -->
                        <td colspan="6" style="text-align: left;">Total Diskon</td>
                        <td style="text-align: left; font-size:10pt;font-weight:bold">Rp. <span style="float: right;margin-left:2px;"> (<?php echo $all_discount ?>)</td>
                    </tr>
                    <?php if ($total_tax > 0) { ?>
                        <tr>
                            <td colspan="4" style="text-align: center;"></td>
                            <td colspan="6" style="text-align: left;">PPN</td>
                            <td style="text-align: left; font-size:10pt;font-weight:bold">Rp. <span style="float: right;margin-left:2px;"> <?php echo $total_tax; ?></th>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($due_amount > 0 && $paid_amount == 0) { ?>
                        <tr>
                            <td colspan="4" style="text-align: center;"></td>
                            <td colspan="6" style="text-align: left;">Jatuh Tempo</td>
                            <td style="text-align: left; font-size:10pt;font-weight:bold">Rp. <span style="float: right;margin-left:2px;"> <?php echo $due_amount; ?></th>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($due_amount > 0 && $paid_amount > 0) { ?>
                        <tr>
                            <td colspan="4" style="text-align: center;"></td>
                            <td colspan="6" style="text-align: left;">Jatuh Tempo</td>
                            <td style="text-align: left; font-size:10pt;font-weight:bold">Rp. <span style="float: right;margin-left:2px;"> <?php echo $due_amount; ?></th>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center;"></td>
                            <td colspan="6" style="text-align: left;font-weight:bold">Total Pembayaran</td>
                            <td style="text-align: left; font-size:10pt;font-weight:bold">Rp. <span style="float: right;margin-left:2px;"> <?php echo $paid_amount; ?></th>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($due_amount == 0 && $paid_amount > 0) { ?>
                        <tr>
                            <td colspan="4" style="text-align: center;"></td>
                            <td colspan="6" style="text-align: left;font-weight:bold">Total Pembayaran</td>
                            <td style="text-align: left; font-size:10pt;font-weight:bold">Rp. <span style="float: right;margin-left:2px;"> <?php echo $paid_amount; ?></th>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>

            <table style='width:750;border-collapse: collapse;' border='0'>
                <tr style="margin-left: 250px;">

                    <td style="width: 350px;font-size:8pt">
                        Transfer via : <br>

                    </td>
                    <td style="width:400;font-size:8pt;">
                        Catatan :<br>
                    </td>
                </tr>
                <tr>
                    <td style="width: 350;">
                        <?php foreach ($bank as $b) : ?>
                            <?php echo "<b style='margin-left:3px;font-size:9pt; font-style:italic'>- " . $b['bank_name'] . " " . $b['ac_name'] . " (" . $b['ac_number'] . ")</b>"; ?><br>
                        <?php endforeach; ?>
                    </td>
                    <td style="width:400;border-collapse:collapse;border:1px solid">
                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    </td>
                </tr>
            </table>
            <br>
            <table style='width:200; font-size:7pt; border-collapse: collapse;margin-right:400px' border='1' cellspacing='2'>
                <tr style="text-align: center;">
                    <td style="width:100">Penerima</td>
                    <td style="width:100">Pengirim</td>

                    <td style="width:100">Hormat Kami</td>
                </tr>
                <tr style="height: 30px;">
                    <td></td>
                    <td></td>

                    <td></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td></td>

                    <td><?php echo $admin_by; ?></td>
                </tr>
            </table>
            <table style='width:750;border-collapse: collapse;' border='0'>
                <tr style="margin-left: 250px;">

                    <td style="width: 64%;font-size:9px">
                        Barang diterima dengan baik dan cukup<br>
                        *Faktur asli merupakan bukti sah penagihan pelunasan.
                    </td>

                </tr>
            </table>
            </td>
            <br>
            </center>
        </body>
        <!-- print -->

    </div>
</div>