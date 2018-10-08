<?php

    require '_app/Config.inc.php';
    require '_top.php';
    
    if ((empty($_SESSION['cliente'])) || $_SESSION['cliente']['Codigo'] == "") {
        header("Location:cliente_entrar.php");
        exit;
    }

    $logoff = filter_input(INPUT_GET, "logoff", FILTER_VALIDATE_BOOLEAN);
    if ($logoff) {
        unset($_SESSION['cliente']);
        header("Location:cliente_entrar.php");
    }
    
    
    $Cadastro = $pdo->prepare("SELECT Ordem, Codigo, Nome, CPF, Limite_Credito, Limite_Credito_Dt_Validade, Bloqueado FROM Cli_For WHERE Codigo = ?");
    $Cadastro->bindParam(1, $_SESSION['cliente']['Codigo']);
    $Cadastro->execute();
    $rowCadastro = $Cadastro->fetch(PDO::FETCH_OBJ);

    $LimiteCredito = $pdo->prepare("SELECT SUM(Valor_Final_Calculado) AS total
                                    FROM Financeiro_Contas 
                                    WHERE (Financeiro_Contas.Pagar_Receber = 'R') AND (Financeiro_Contas.Tipo_Conta IN ('B', 'N', 'R', 'H', 'E')) 
                                    AND (Financeiro_Contas.Situacao IN ('E', 'A', 'D', 'U')) AND (Financeiro_Contas.Ordem_Cli_For = ?)");
    $LimiteCredito->bindParam(1, $_SESSION['cliente']['Ordem']);
    $LimiteCredito->execute();
    $rowLimiteCredito = $LimiteCredito->fetch(PDO::FETCH_OBJ);
    
    $LimiteDisponivel = $rowCadastro->Limite_Credito - $rowLimiteCredito->total;
?>

<?php
	$CadastroFoto = $pdo->prepare("SELECT TOP 1 * FROM Cli_For_Fotos WHERE Ordem_Cli_For = ?");
        $CadastroFoto->bindParam(1, $_SESSION['cliente']['Ordem']);
        $CadastroFoto->execute();
        $rowCadastroFoto = $CadastroFoto->fetch(PDO::FETCH_OBJ);
?>

<div class="shadow-lg" style="background:#f4f4f4;">
    
    
    <div class="container">       
        
        
        <div class="row pt-5 pb-4">

            <div class="col-sm-4 text-center" >
                    <div class="rounded-circle" style="width:180px; margin: 0px auto 20px; height: 180px; background: #777; <?php if (!empty($rowCadastroFoto->Foto)) { ?> background:url(<?php  echo 'data:image/jpeg;base64,' . $rowCadastroFoto->Foto;  ?>); <?php } ?> background-size: 130%; background-position: 50%;"></div>
            </div>

            <div class="col text-left">    
                <h2><?php echo $rowCadastro->Nome; ?></h2>
                <div class="lead"><b>CPF: <?php echo $rowCadastro->CPF; ?></b></p></div>

                <div class="row">
                    <div class="col">
                        Disponível para Compras<br>
                        <h2><span class="badge <?php if ($LimiteDisponivel >= 0) { echo "badge-success"; } else { echo "badge-danger"; } ?>">R$ <?php echo Check::Moeda($LimiteDisponivel); ?></span></h2>
                    </div>

                    <div class="col">
                        Limite de Crédito <br>
                        <h4><span class="badge badge-secondary">R$ <?php echo Check::Moeda($rowCadastro->Limite_Credito); ?></span></h4>
                    </div>

                    <div class="col">
                        <button type="button" onclick="location.href='cliente.php?logoff=true';" class="btn btn-primary btn-lg"> Sair / Fechar </button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>        
</div>

<div class="container mt-5">
        

            <?php
                if ($rowCadastro->Bloqueado == "1") {
            ?>
                <div class="alert alert-danger" role="alert">
                    <b>Seu cadastro está bloqueado. </b> É necessário que você procure o setor de crédito da loja para regularizar.
                </div>
            <?php 
                } else {
                    //Mostra mensagem se data de crédito estiver vencida
                    if ($rowCadastro->Limite_Credito_Dt_Validade < date("Y-m-i")) {
                ?>
                    <div class="alert alert-danger" role="alert">
                        <img src="img/card.png" align="absmiddle" /> <b>Seu Cartão Credfashion está vencido.</b> Procure o setor de Crédito da loja para atualizar e já começar a comprar na hora.
                    </div>
                <?php
                    }
                }
            ?>

            
            <?php if ($LimiteDisponivel <= 0) { ?>            
                <div class="alert alert-warning" role="alert">
                    <b>Limite de Compras: </b> Você não possuí limite disponível para compras. <?php if ($LimiteDisponivel < 0) { echo " Se você possuí contas em aberto, efetue o pagamento para liberar mais limite"; } ?>
                </div>
            <?php } ?>
            
            <!--
            <div class="alert alert-success mt-5" role="alert">
              <h4 class="alert-heading">Well done!</h4>
              <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
              <hr>
              <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.</p>
            </div>            
            -->
            
    <?php 
        $Parcelas = $pdo->prepare("SELECT *, (SELECT Filiais.Nome FROM Filiais WHERE Filiais.Ordem = Financeiro_Contas.Ordem_Filial) as Loja
                                    FROM Financeiro_Contas 
                                    WHERE 
                                            (Financeiro_Contas.Pagar_Receber = 'R') AND 
                                            (Financeiro_Contas.Tipo_Conta IN ('B', 'N', 'R', 'H', 'E')) AND 
                                            (Financeiro_Contas.Situacao IN ('E', 'A', 'D', 'U')) AND 
                                            (Financeiro_Contas.Ordem_Cli_For = ?)
                                    ORDER BY Data_Vencimento
                                ");
        $Parcelas->bindParam(1, $_SESSION['cliente']['Ordem']);
        $Parcelas->execute();
        $rowParcelas = $Parcelas->fetchAll();  
        $countParcelas = count($rowParcelas);
        
        if ($countParcelas > 0) {
    ?>
            <h4 class="mt-5">Parcelas em Aberto</h4>

            <div class="alert alert-warning" role="alert">
                Os valores apresentados abaixo são valores sem juros. Para consultar valores com juros dirija-se ao caixa ou ao setor de crédito.
            </div>
            
            <div class="table-responsive">
                <table class="mt-3 table table-striped">
                    <thead class="thead-dark">
                    <tr>
                      <th scope="col" width="6%">#</th>
                      <th scope="col" width="10%">Vencimento</th>
                      <th scope="col" width="18%" style="text-align: right;">Valor</th>
                      <th scope="col" width="20%" style="text-align: center;">Emissão</th>
                      <th scope="col" width="30%">Loja</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    $i=0;
                    //while ($rowParcelas = $Parcelas->fetch(PDO::FETCH_OBJ)) {
                    foreach ($rowParcelas as $row) {
                        $i++;
                        $DataVencimento = new DateTime($row['Data_Vencimento']); //strtotime($row['Data_Vencimento']);
                        $DataAtual = new DateTime(date("Y-m-d")); //strtotime(date("Y-m-d"));
                        //echo $DataVencimento . " A: " . $DataAtual;
                  ?>
                      <tr class="<?php if ($DataVencimento->getTimestamp() < $DataAtual->getTimestamp()) { echo "table-danger"; } ?>">
                      <th scope="row"><?php echo $i; ?></th>
                      <td><b><?php echo Check::DataBR($row['Data_Vencimento']); ?></b></td>
                      <td class="text-right text-nowrap"><b>R$ <?php echo Check::Moeda($row['Valor_Base']); ?></b></td>
                      <td class="text-center"> <?php echo Check::DataBR($row['Data_Emissao']); ?></td>
                      <td class="text-nowrap"><?php echo $row['Loja']; ?></td>
                    </tr>
                  <?php
                    }
                  ?>
                  </tbody>
                </table>    
            </div>
   <?php
        }
   ?>
    
</div>

<?php require '_bottom.php'; ?>
