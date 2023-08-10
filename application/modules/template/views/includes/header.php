<a style="position: fixed;height:44px;line-height:44px;background-color:#023b6d" href="<?php echo base_url('home') ?>" class="logo">
    <span class="logo-lg">
        ERP SYSTEM <sup>v1.2</sup>
        <!-- <img style="height: 30px;" src="<?php echo base_url((!empty($setting->logo) ? $setting->logo : 'assets/img/icons/logo.png')) ?>" alt=""> -->
    </span>
    <span class="logo-mini">

        <img style="height: 30px;" src="<?php echo base_url((!empty($setting->favicon) ? $setting->favicon : 'assets/img/icons/mini-logo.png')) ?>" alt="">
    </span>
</a>
<div class="se-pre-con"></div>
<!-- Header Navbar -->
<?php $gui_p = $this->uri->segment(1);
if ($gui_p != 'gui_pos') {
?>
    <nav class="navbar " style="position: fixed;right:0;left:0;top:0;height:45px;min-height:45px;background-color:#023b6d">
        <a href="#" class="sidebar-toggle text-muted" data-toggle="offcanvas" role="button" style="border-right:0px;height:45px;margin-top:3px;padding-top:5px"> <!-- Sidebar toggle button-->
            <span class="sr-only">Toggle navigation</span>
            <span class="pe-7s-menu"></span>
        </a>
        <span class="top-fixed-links">
            <?php

            if ($this->permission1->method('new_invoice', 'create')->access()) {
            ?>
                <a style="margin-top:5px" href="<?php echo base_url('add_invoice') ?>" class="btn btn-sm btn-primary btn-outline"><i class="fa fa-shopping-bag"></i> <?php echo display('invoice') ?></a>
            <?php } ?>


            <?php if ($this->permission1->method('customer_receive', 'create')->access()) { ?>
                <a style="margin-top:5px" href="<?php echo base_url('customer_receive') ?>" class="btn btn-sm btn-primary btn-outline"><i class="fa fa-money"></i> <?php echo display('customer_receive') ?></a>
            <?php } ?>

            <?php if ($this->permission1->method('supplier_payment', 'create')->access()) { ?>
                <a style="margin-top:5px" href="<?php echo base_url('supplier_payment') ?>" class="btn btn-sm btn-primary btn-outline"><i class="fa fa-money" aria-hidden="true"></i> <?php echo display('supplier_payment') ?></a>
            <?php } ?>

            <?php if ($this->permission1->method('add_purchase', 'create')->access()) { ?>
                <a style="margin-top:5px" href="<?php echo base_url('add_purchase') ?>" class="btn btn-sm btn-primary btn-outline"><i class="ti-shopping-cart"></i> <?php echo display('purchase') ?></a>
            <?php } ?>
        </span>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav" style="margin-top:0px">
                <!-- Messages -->
                <?php if ($this->permission1->method('pos_invoice', 'create')->access()) {
                ?>
                    <li>
                        <a style="margin-top:5px" href="<?php echo base_url('gui_pos') ?>" class="text-white btn-sm btn-success pos-btn"> <span class="fa fa-plus"></span> <?php echo display('pos_invoice') ?></a>
                    </li>
                <?php } ?>
                <?php if ($max_version > $current_version) { ?>
                    <li>
                        <blink><a href="<?php echo base_url('autoupdate/Autoupdate') ?>" class="text-white btn-smx  btn-danger update-btn"> <?php echo $max_version . ' Version Available'; ?></a></blink>
                    </li>
                <?php } ?>
                <li class="dropdown notifications-menu">
                    <a style="margin-top:2px;padding-top: 1px;" href="<?php echo base_url('out_of_stock') ?>">
                        <i class="fa fa-fw fa-ban text-white" title="<?php echo display('out_of_stock') ?>"></i>
                        <span class="label label-danger"><?php echo html_escape($out_of_stocks) ?></span>
                    </a>
                </li>
                <!-- settings -->
                <li class="dropdown dropdown-user" style="">

                    <a style="margin-top:2px;padding-top: 1px;margin-left:2px;padding-left:12px;color:tan" href="#" class="dropdown-toggle" data-toggle="dropdown"> <?php echo $this->session->userdata('fullname') . '<br><small class="text-success"><i class="fa fa-circle text-success"></i> ' . $this->session->userdata('user_level') . '</small>' ?></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo base_url('edit_profile') ?>">
                                <i class="pe-7s-users"></i><?php echo $this->session->userdata('fullname') ?>
                            </a>
                        </li>
                        <li>
                            <a href="javascript::void">
                                <i class="pe-7s-lock"></i><?php echo $this->session->userdata('user_level') ?>
                            </a>
                        </li>
                </li>
                <li><a href="<?php echo base_url('change_password') ?>"><i class="pe-7s-settings"></i><?php echo display('change_password') ?></a></li>
                <li><a href="<?php echo base_url('logout') ?>"><i class="pe-7s-key"></i> <?php echo display('logout') ?></a></li>
            </ul>
            </li>
            </ul>
        </div>

    </nav>
<?php } ?>