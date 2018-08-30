<div id="wizard" class="form-wizard form-wizard-horizontal">
    <div class="form-wizard-nav">
        <div class="progress">
            <div class="progress-bar progress-bar-primary" style="width: <?php echo ($step-1)*20 + 10 ?>%;"></div>
        </div>
        <ul class="nav nav-justified">

            <?php $max = 5; ?>

            <li class="<?php echo ($step == 1) ? 'active' : ''; if($step > 1) echo " done"; ?>">
                <a href="#"><span class="step">1<span class="de_max">/ <?php echo $max ?></span></span> <span class="title">Dados iniciais</span></a>
            </li>

            <li class="<?php echo ($step == 2) ? 'active' : ''; if($step > 2) echo " done"; ?>">
                <a href="#"><span class="step">2<span class="de_max">/ <?php echo $max ?></span></span> <span class="title">Cotação</span></a>
            </li>

            <li class="<?php echo ($step == 3) ? 'active' : ''; if($step > 3) echo " done"; ?>">
                <a href="#"><span class="step">3<span class="de_max">/ <?php echo $max ?></span></span> <span class="title">Contratação</span></a>
            </li>
            <li class="<?php echo ($step == 4) ? 'active' : ''; if($step > 4) echo " done"; ?>">
                <a href="#"><span class="step">4<span class="de_max">/ <?php echo $max ?></span></span> <span class="title">Pagamento</span></a>
            </li>
            <li class="<?php echo ($step == 5) ? 'active' : ''; if($step > 5) echo " done"; ?>">
                <a href="#"><span class="step">5<span class="de_max">/ <?php echo $max ?></span></span> <span class="title">Certificado / Bilhete</span></a>
            </li>

        </ul>
    </div><!--end .form-wizard-nav -->
    <br>
</div><!--end #rootwizard -->


