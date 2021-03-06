<div class="section-header">
    <ol class="breadcrumb">
        <li class="active"><?php echo app_recurso_nome(); ?></li>
    </ol>
</div>

<div class="row">
    <div class="col-md-6">
        <?php $this->load->view('admin/partials/messages'); ?>
    </div>
</div>

<!-- Widget -->
<div class="card">

    <div class="card-head style-primary">
        <header><?php echo $title; ?></header>
    </div>

    <div class="card-body">

        <div class="row">
            <button onclick="exportTableToCSV('relatorio.csv')">Exportar CSV</button>
            <?php echo $filters; ?>
        </div>
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <?php echo $tbody ?>
                        <tr id="loading_icon" style="display:none">
                            <td colspan="23" style="text-align:center">''
                                <img src="/assets/loading.gif" alt="">
                            </td>
                        </tr>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>