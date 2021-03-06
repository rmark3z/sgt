<?php
include '../include/head.php';
include '../include/funcoes.php';
include '../include/conexao/conecta.php';
$conexao = conecta();
validaAcesso();


if (isset($_GET['acao']) && $_GET['acao'] != '') {

    $acao = $_GET['acao'];

    $id = (isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id'] : 0;
    $id = base64_decode($id);

    if ($acao == 'excluir') {
        $deletaMulta = $conexao->prepare("DELETE FROM multa WHERE id_multa = :id");
        $deletaMulta->bindValue(":id", $id);
        $deletaMulta->execute();

        if ($deletaMulta->rowCount() == 0) {
            echo "<script>alert('Houve um erro ao deletar!');
                location.href=\"index.php\"</script>";
        } else {
            echo "<script>alert('Registro excluido com sucesso!');
                   location.href=\"index.php\"</script>";
             auditoria("Multa id ".$id." deletada" );
        }
    }
    if ($acao == 'visualizar' || $acao == 'editar') {
        $pegaMultas = $conexao->prepare("SELECT * FROM multa WHERE id_multa = :id");
        $pegaMultas->bindValue(":id", $id);
        $pegaMultas->execute();
        $multa = $pegaMultas->fetch(PDO::FETCH_ASSOC);

        $id = $multa['id_multa'];
        $tipoVeiculo = $multa['cd_modalidade'];
        $prefixo = $multa['cd_prefixo'];
        $placa = $multa['cd_placa'];
        $nomeInfracao = $multa['nm_infracao'];
        $dataInfracao = $multa['dt_infracao'];
        $horaInfracao = $multa['hr_infracao'];
        $dataVencimentoInfracao = $multa['dt_vencimento_infracao'];
        $numeroAit = $multa['cd_ait'];
        $nomeAgente = $multa['nm_agente'];
        $descricao = $multa['ds_observacao'];
        $pagamentoMulta = $multa['dt_pagamento_multa'];
    }

    if ($acao == 'novo') {

        $id = 0;
        $tipoVeiculo = "";
        $prefixo = "";
        $placa = "";
        $nomeInfracao = "";
        $dataInfracao = "";
        $horaInfracao = "";
        $dataVencimentoInfracao = "";
        $numeroAit = "";
        $nomeAgente = "";
        $descricao = "";
        $pagamentoMulta = "";
    }
}

if (isset($_POST['id_multa']) && $_POST['id_multa'] != '') {
    $id = $_POST['id_multa'];
   
    $placa = $_POST['cd_placa'];
    
     try {
                                            $testes = $conexao->prepare("SELECT DISTINCT c.cd_modalidade, t.nm_modalidade FROM veiculo c , tipoVeiculo t where c.cd_placa = '$placa' AND c.cd_modalidade = t.cd_modalidade");
                                            $testes->execute();
                                            while ($teste = $testes->fetch(PDO::FETCH_ASSOC)) { 
                                         
                                            $tipoVeiculo = $teste['cd_modalidade'];}
                                        } catch (Exception $e) {
                                            echo $e;
                                            exit();
                                        }
                                        try {
                                            $prefixoVeiculos = $conexao->prepare("SELECT DISTINCT cd_prefixo FROM veiculo where cd_placa = '$placa'");
                                            $prefixoVeiculos->execute();
                                            while ($prefixoVeiculo = $prefixoVeiculos->fetch(PDO::FETCH_ASSOC)) { 
                                         
                                            $prefixo = $prefixoVeiculo['cd_prefixo'];}
                                        } catch (Exception $e) {
                                            echo $e;
                                            exit();
                                        }
    $nomeInfracao = $_POST['nm_infracao'];
    $dataInfracao = $_POST['dt_infracao'];
    $horaInfracao = $_POST['hr_infracao'];
    $dataVencimentoInfracao = $_POST['dt_vencimento_infracao'];
    $numeroAit = $_POST['cd_ait'];
    $nomeAgente = $_POST['nm_agente'];
    $descricao = $_POST['ds_observacao'];
    $pagamentoMulta = $_POST['dt_pagamento_multa'];
    ;
    
    if (empty($id)) {


        try {
            $novaMulta = $conexao->prepare("INSERT INTO multa ( cd_placa, cd_prefixo, cd_modalidade, nm_infracao, dt_infracao, hr_infracao,"
                    . "dt_vencimento_infracao, cd_ait, nm_agente, ds_observacao, dt_pagamento_multa) "
                    . "VALUES (  :placa, :prefixo, :tipoVeiculo, :nomeInfracao, :dataInfracao, :horaInfracao, :dataVencimentoInfracao, :numeroAit, :nomeAgente, :descricao, :pagamentoMulta )");
            
            
            $novaMulta->bindValue(":placa", $placa, PDO::PARAM_STR);
            $novaMulta->bindValue(":prefixo", $prefixo, PDO::PARAM_STR);
            $novaMulta->bindValue(":tipoVeiculo", $tipoVeiculo, PDO::PARAM_STR);
            $novaMulta->bindValue(":nomeInfracao", $nomeInfracao, PDO::PARAM_STR);
            $novaMulta->bindValue(":dataInfracao", $dataInfracao, PDO::PARAM_STR);
            $novaMulta->bindValue(":horaInfracao", $horaInfracao, PDO::PARAM_STR);
            $novaMulta->bindValue(":dataVencimentoInfracao", $dataVencimentoInfracao, PDO::PARAM_STR);
            $novaMulta->bindValue(":numeroAit", $numeroAit);
            $novaMulta->bindValue(":nomeAgente", $nomeAgente, PDO::PARAM_STR);
            $novaMulta->bindValue(":descricao", $descricao, PDO::PARAM_STR);
            $novaMulta->bindValue(":pagamentoMulta", $pagamentoMulta, PDO::PARAM_STR);

            $novaMulta->execute();
            //echo $novaMulta->rowCount();
              //var_dump($novaMulta);
               //echo $novaMulta->errorCode();
              //exit();
            auditoria("Multa id ".$conexao->lastInsertId()." Inserida" );
           $retorno = 'inserido';
        } catch (Exception $e) {
            echo $e;
            exit($e);
        }
    } else {
        try {
            $atualizarMulta = $conexao->prepare("UPDATE multa SET  cd_placa = :placa, cd_prefixo = :prefixo, cd_modalidade = :tipoVeiculo,  nm_infracao = :nomeInfracao, dt_infracao = :dataInfracao,"
                    . " hr_infracao = :horaInfracao, dt_vencimento_infracao = :dataVencimentoInfracao, cd_ait = :numeroAit , nm_agente = :nomeAgente, ds_observacao = :descricao, dt_pagamento_multa = :pagamentoMulta "
                    . "WHERE id_multa = :id");
            
            
            $atualizarMulta->bindValue(":placa", $placa, PDO::PARAM_STR);
            $atualizarMulta->bindValue(":prefixo", $prefixo, PDO::PARAM_STR);
            $atualizarMulta->bindValue(":tipoVeiculo", $tipoVeiculo, PDO::PARAM_STR);
            $atualizarMulta->bindValue(":nomeInfracao", $nomeInfracao, PDO::PARAM_STR);
            $atualizarMulta->bindValue(":dataInfracao", $dataInfracao, PDO::PARAM_STR);
            $atualizarMulta->bindValue(":horaInfracao", $horaInfracao, PDO::PARAM_STR);
            $atualizarMulta->bindValue(":dataVencimentoInfracao", $dataVencimentoInfracao, PDO::PARAM_STR);
            $atualizarMulta->bindValue(":numeroAit", $numeroAit);
            $atualizarMulta->bindValue(":nomeAgente", $nomeAgente, PDO::PARAM_STR);
            $atualizarMulta->bindValue(":descricao", $descricao, PDO::PARAM_STR);
            $atualizarMulta->bindValue(":pagamentoMulta", $pagamentoMulta, PDO::PARAM_STR);
            
            $atualizarMulta->bindValue(":id", $id);
            $atualizarMulta->execute();

//                echo $atualizarUsuario->rowCount();
//                var_dump($atualizarUsuario);
//                echo $atualizarUsuario->errorCode();
//                exit();
            auditoria("Dados do multa id ".$id." atualizados" );
            $retorno = 'alterado';
        } catch (Exception $e) {
            echo $e;
            exit();
        }
    }

    if ($retorno) {
        header('Location: index.php?retorno=' . $retorno);
    } else {
        header('Location: gerencia.php?id=' . base64_encode($id) . '&acao=novo&retorno=invalido');
    }
}
?>
<script>alertaSucesso("a", "a", "a")</script>


<body class="">
    <?php include '../include/header.php'; ?>

    <div id="page-container">

        <?php include '../include/menu.php'; ?>

        <div id="page-content">
            <div id='wrap'>
                <div id="page-heading">
                    <ol class="breadcrumb">
                        <li class='active'><a href="../home.php">Home</a> > <a href="index.php">Multas</a> > <?php echo $acao ?></li>

                    </ol>
                    <h1>Multas</h1>            
                </div>       

                <div class="container">               

                    <div class="panel panel-midnightblue">

                        <div class="panel-heading">
                            <h4>Dados da multa</h4>

                        </div>
                        <div class="panel-body collapse in">

                            <form id="formMulta" name="formMulta"  action="gerencia.php" method="post"  class="form-horizontal" />
                            <input type="hidden" name="id_multa" id="id_multa" value="<?php echo($id); ?>">
                            
                                <?php if ($acao != 'novo') { ?>
                            <div class="form-group">                                                    
                                <label class="col-sm-2 control-label">Tipo do Serviço</label>
                                <div class="col-sm-4">                                        
                                    <select name="cd_modalidade" id="cd_modalidade" class="form-control" disabled="disabled" required>
                                        
                                        <?php
                                        try {
                                            $tipoServicos = $conexao->prepare("SELECT DISTINCT c.cd_modalidade, t.nm_modalidade FROM veiculo c , tipoVeiculo t where c.cd_placa = '$placa' AND c.cd_modalidade = t.cd_modalidade");
                                            $tipoServicos->execute();
                                            
                                            
                                        } catch (Exception $e) {
                                            echo $e;
                                            exit();
                                        }
                                        ?>
                                        <?php while ($tipoServico = $tipoServicos->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <option value='<?php echo $tipoServico['cd_modalidade']; ?>' selected><?php echo $tipoServico['nm_modalidade']; ?>  </option>
                                        <?php } ?>                                              
                                    </select>
                                </div>
                            </div> 


<div class="form-group">
                                 <label class="col-sm-2 control-label">Prefixo</label>
                                 <div class="col-sm-4">
                                     <?php
                                        try {
                                            $Prefixos = $conexao->prepare("SELECT DISTINCT cd_prefixo FROM veiculo where cd_placa = '$placa'");
                                            $Prefixos->execute();
                                            
                                            
                                        } catch (Exception $e) {
                                            echo $e;
                                            exit();
                                        }
                                        ?>
                                        <?php while ($Prefixo = $Prefixos->fetch(PDO::FETCH_ASSOC)) { ?>
                                     <input name="cd_prefixo" id="cd_prefixo" type="text" class="form-control" value="<?php echo $Prefixo['cd_prefixo'] ?>" readonly="readonly" </>
                                        <?php } ?>  
                                 </div>
                             </div>

 <?php } ?> 
                             
                            <div class="form-group">                                                    
                                <label class="col-sm-2 control-label">Placa do Veiculo</label>
                                <div class="col-sm-4">                                        
                                    <select name="cd_placa" id="cd_placa" class="form-control" <?php if ($acao == 'visualizar') { ?>disabled="disabled" <?php }; ?> required>
                                        <option value='' >Placa..</option>
                                        <?php
                                        try {
                                            $placasVeiculo = $conexao->prepare("SELECT * FROM veiculo");
                                            $placasVeiculo->execute();
                                        } catch (Exception $e) {
                                            echo $e;
                                            exit();
                                        }
                                        ?>
                                        <?php while ($placas = $placasVeiculo->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <option value='<?php echo $placas['cd_placa']; ?>' <?php echo ($placas['cd_placa']== $placa) ? 'selected' : ''; ?>><?php echo $placas['cd_placa']; ?>  </option>
                                        <?php } ?>                                              
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Infração</label>
                                <div class="col-sm-4"> 
                                <select name="nm_infracao" id="nm_infracao" class="form-control" <?php if ($acao == 'visualizar') { ?>disabled="disabled" <?php }; ?> required>
                                        <option value='' >Descrição...</option>
                                        <?php
                                        try {
                                            $decricoes = $conexao->prepare("SELECT DISTINCT nm_infracao FROM infracao ");

                                            $decricoes->execute();
                                        } catch (Exception $e) {
                                            echo $e;
                                            exit();
                                        }
                                        ?>
<?php while ($descricaoInfracao = $decricoes->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <option value='<?php echo $descricaoInfracao['nm_infracao']; ?>' <?php echo ($descricaoInfracao['nm_infracao'] == $nomeInfracao) ? 'selected' : ''; ?>><?php echo $descricaoInfracao['nm_infracao']; ?>  </option>
<?php } ?>                                              
                                    </select>
                                    </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label">Data da Infração</label>
                                <div class="col-sm-4">
                                    <input name="dt_infracao" id="dt_infracao" type="date" class="form-control"  value="<?php echo $dataInfracao ?>" <?php if ($acao == 'visualizar') { ?>readonly="readonly" <?php }; ?> required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Hora da Infração</label>
                                <div class="col-sm-4">
                                    <input name="hr_infracao" id="hr_infracao" type="time" class="form-control"  value="<?php echo $horaInfracao ?>" <?php if ($acao == 'visualizar') { ?>readonly="readonly" <?php }; ?> required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Data da Venc. Infração</label>
                                <div class="col-sm-4">
                                    <input name="dt_vencimento_infracao" id="dt_vencimento_infracao" type="date" class="form-control"  value="<?php echo $dataVencimentoInfracao ?>" <?php if ($acao == 'visualizar') { ?>readonly="readonly" <?php }; ?> required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Numero AIT</label>
                                <div class="col-sm-4">
                                    <input name="cd_ait" id="cd_ait" type="number" class="form-control"  value="<?php echo $numeroAit ?>" <?php if ($acao == 'visualizar') { ?>readonly="readonly" <?php }; ?> min="0" required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Agente</label>
                                <div class="col-sm-4">
                                    <input name="nm_agente" id="nm_agente" type="text" class="form-control"  value="<?php echo $nomeAgente ?>" <?php if ($acao == 'visualizar') { ?>readonly="readonly" <?php }; ?> required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Descrição</label>
                                <div class="col-sm-4">
                                    <textarea name="ds_observacao" id="ds_observacao" class="form-control" rows="4" cols="50" 
                                              <?php if ($acao == 'visualizar') { ?>readonly="readonly" <?php }; ?> ><?php echo $descricao ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Pagamento</label>
                                <div class="col-sm-4">
                                    <input name="dt_pagamento_multa" id="dt_pagamento_multa" type="date" class="form-control"  value="<?php echo $pagamentoMulta ?>" <?php if ($acao == 'visualizar') { ?>readonly="readonly" <?php }; ?> />
                                </div>
                            </div>

                            <div class="panel-footer">
                                <div class="row">
                                    <div class="col-sm-6 col-sm-offset-3">
                                        <div class="btn-toolbar">
                                            <?php if ($acao == 'visualizar') { ?>
                                                <a class="btn-primary btn" href='index.php'>Voltar</a>
                                            <?php } else {
                                                ?>
                                                <button class="btn-primary btn" id="btn_gravar" >Gravar</button>
                                                <a class="btn-default btn" href='index.php'>Cancelar</a>
                                            <?php }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            </form>

                        </div>

                    </div>

                </div> <!-- container -->
            </div> <!--wrap -->
        </div> <!-- page-content -->

        <?php include '../include/footer.php'; ?>

    </div> <!-- page-container -->

    <!--
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    
    <script>!window.jQuery && document.write(unescape('%3Cscript src="assets/js/jquery-1.10.2.min.js"%3E%3C/script%3E'))</script>
    <script type="text/javascript">!window.jQuery.ui && document.write(unescape('%3Cscript src="assets/js/jqueryui-1.10.3.min.js'))</script>
    -->

    <script type='text/javascript' src='../assets/js/jquery-1.10.2.min.js'></script> 
    <script type='text/javascript' src='../assets/js/alertas.js'></script> 
    <script type='text/javascript' src='..assets/js/jqueryui-1.10.3.min.js'></script> 
    <script type='text/javascript' src='../assets/js/bootstrap.min.js'></script> 
    <script type='text/javascript' src='../assets/js/enquire.js'></script> 
    <script type='text/javascript' src='../assets/js/jquery.cookie.js'></script> 
    <script type='text/javascript' src='../assets/js/jquery.touchSwipe.min.js'></script> 
    <script type='text/javascript' src='../assets/js/jquery.nicescroll.min.js'></script> 
    <script type='text/javascript' src='../assets/plugins/codeprettifier/prettify.js'></script> 
    <script type='text/javascript' src='../assets/plugins/easypiechart/jquery.easypiechart.min.js'></script> 
    <script type='text/javascript' src='../assets/plugins/sparklines/jquery.sparklines.min.js'></script> 
    <script type='text/javascript' src='../assets/plugins/form-toggle/toggle.min.js'></script> 
    <script type='text/javascript' src='../assets/plugins/form-wysihtml5/wysihtml5-0.3.0.min.js'></script> 
    <script type='text/javascript' src='../assets/plugins/form-wysihtml5/bootstrap-wysihtml5.js'></script> 
    <script type='text/javascript' src='../assets/plugins/fullcalendar/fullcalendar.min.js'></script> 
    <script type='text/javascript' src='../assets/plugins/form-daterangepicker/daterangepicker.min.js'></script> 
    <script type='text/javascript' src='../assets/plugins/form-daterangepicker/moment.min.js'></script> 
    <script type='text/javascript' src='../assets/plugins/charts-flot/jquery.flot.min.js'></script> 
    <script type='text/javascript' src='../assets/plugins/charts-flot/jquery.flot.resize.min.js'></script> 
    <script type='text/javascript' src='../assets/plugins/charts-flot/jquery.flot.orderBars.min.js'></script> 
    <script type='text/javascript' src='../assets/demo/demo-index.js'></script> 
    <script type='text/javascript' src='../assets/js/placeholdr.js'></script> 
    <script type='text/javascript' src='../assets/js/application.js'></script> 
    <script type='text/javascript' src='../assets/demo/demo.js'></script> 

</body>
</html>

