<div id="sidebar-wrapper">
    <a id="menu-close" href="#" class="btn btn-default btn-lg pull-right toggle">&times;</a>
    <ul class="sidebar-nav">
        <li>
            <a href="#" class="active background-primary">
                <i class="fa fa-smile-o" aria-hidden="true"></i> CONTRATE
            </a>
        </li>

        <li>
            <span class="item"> SEGUROS </span>
        </li>
        <li>
            <a href="#" class="link"> <i class="fa fa-angle-right" aria-hidden="true"></i> COMPRAR </a>
        </li>
        <li>
            <a href="#" class="link"> <i class="fa fa-angle-right" aria-hidden="true"></i> COTAÇÕES SALVAS </a>
        </li>

        <li>
            <span class="item"> SERVIÇOS </span>
        </li>
        <li>
            <a href="#" class="link"> <i class="fa fa-angle-right" aria-hidden="true"></i> COMPRAR </a>
        </li>

        <li>
            <a href="#" class="active">
                <i class="fa fa-briefcase" aria-hidden="true"></i> APOLICES
            </a>
        </li>

        <li>
            <a href="#" class="active">
                <i class="fa fa-comments" aria-hidden="true"></i> ATENDIMENTO
            </a>
        </li>
    </ul>
</div>

<div class="">
    <?php
    //var_dump($this->session->userdata('logado'));
    ?>

    <div class="header-logo-menu">
        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-3">
                <img src="<?php echo $this->template->get('theme_logo'); ?>" alt="" title="" style="width: 90px;" />
            </div>

            <div class="col-md-9 col-sm-9 col-xs-9">

                <ul class="nav nav-pills pull-right">
                    <?php if (!empty($this->session->userdata('logado'))) { ?>
                        <li role="presentation">
                            <a href="javascript:void(0)" title="<?php echo $this->name; ?>" class="username">
                                <?php echo $this->name; ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="" title="">
                                <i class="fa fa-bell" aria-hidden="true"></i>
                            </a>
                        </li>
                    <?php } ?>
                    <li>
                        <button aria-controls="bs-navbar" aria-expanded="false" class="navbar-toggle collapsed" data-target="#bs-navbar" data-toggle="collapse" type="button" id="menu-toggle">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </li>
                </ul>

            </div>
        </div>
    </div>

    <?php
    $step = $this->uri->segment(5);
    if (empty($step)) {
        $step = 1;
    }
    ?>
    <h2 class="text-light text-center title-h2" id="step-title"><?php echo $title; ?></h2>
</div>
<!-- Column -->
<div class="col-md-12">
    <?php
    if ($step == 1) {
        $this->load->view('admin/venda/step', array('step' => 1, 'produto_parceiro_id' =>  issetor($produto_parceiro_id)));
    } elseif ($step == 2) {
        $this->load->view('admin/venda/step', array('step' => 2, 'produto_parceiro_id' =>  issetor($produto_parceiro_id)));
    } elseif ($step == 3) {
        $this->load->view('admin/venda/step', array('step' => 3, 'produto_parceiro_id' =>  issetor($produto_parceiro_id)));
    } elseif ($step == 4) {
        $this->load->view('admin/venda/step', array('step' => 4, 'produto_parceiro_id' =>  issetor($produto_parceiro_id)));
    } elseif ($step == 5) {
        $this->load->view('admin/venda/step', array('step' => 5, 'produto_parceiro_id' =>  issetor($produto_parceiro_id)));
    }
    ?>
</div>
<script>
    $("#menu-close").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#sidebar-wrapper").toggleClass("active");
    });
</script>