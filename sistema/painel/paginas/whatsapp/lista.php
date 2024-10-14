<!-- Lista -->
<div class="bs-example widget-shadow" style="padding:15px">

    <div class="row">
        <div class="col-md-5" style="margin-bottom:5px;">
            <div style="float:left; margin-right:10px"><span><small><i title="Data de Vencimento Inicial" class="fa fa-calendar-o"></i></small></span></div>
            <div style="float:left; margin-right:20px">
                <input type="date" class="form-control " name="data-inicial" id="data-inicial-caixa" value="<?php echo $data_inicio_mes ?>" required>
            </div>

            <div style="float:left; margin-right:10px"><span><small><i title="Data de Vencimento Final" class="fa fa-calendar-o"></i></small></span></div>
            <div style="float:left; margin-right:30px">
                <input type="date" class="form-control " name="data-final" id="data-final-caixa" value="<?php echo $data_final_mes ?>" required>
            </div>
        </div>



        <div class="col-md-2" style="margin-top:5px;" align="center">
            <div>
                <small>
                    <a title="Conta de Ontem" class="text-muted" href="#" onclick="valorData('<?php echo $data_ontem ?>', '<?php echo $data_ontem ?>')"><span>Ontem</span></a> /
                    <a title="Conta de Hoje" class="text-muted" href="#" onclick="valorData('<?php echo $data_hoje ?>', '<?php echo $data_hoje ?>')"><span>Hoje</span></a> /
                    <a title="Conta do Mês" class="text-muted" href="#" onclick="valorData('<?php echo $data_inicio_mes ?>', '<?php echo $data_final_mes ?>')"><span>Mês</span></a>
                </small>
            </div>
        </div>



        <div class="col-md-3" style="margin-top:5px;" align="center">
            <div>
                <small>
                    <a title="Todas as Contas" class="text-muted" href="#" onclick="buscarContas('')"><span>Todas</span></a> /
                    <a title="Contas Pendentes" class="text-muted" href="#" onclick="buscarContas('Não')"><span>Pendentes</span></a> /
                    <a title="Contas Pagas" class="text-muted" href="#" onclick="buscarContas('Sim')"><span>Pagas</span></a>
                </small>
            </div>
        </div>

        <input type="hidden" id="buscar-contas">

    </div>

    <hr>
    <div id="listar">

    </div>

</div>