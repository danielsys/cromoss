<?php
    
    $timer = 200;
    
    
    require '_app/Config.inc.php';
    require '_top.php';
    
    $filial = LOJA;
    
    $codigo = filter_input(INPUT_GET, "codigo", FILTER_VALIDATE_INT);
    
    $Vendedor = $codigo;
    
    $Cadastro = $pdo->prepare("SELECT * FROM Funcionarios WHERE Codigo = ?");
    $Cadastro->bindParam(1, $Vendedor);
    $Cadastro->execute();
    $rowCadastro = $Cadastro->fetch(PDO::FETCH_OBJ);
    

    $UltimasVendas = $pdo->prepare("SELECT TOP 20 Classe_Imposto.Tipo_Operacao as Tipo_Operacao,
                                    View_Movimento_Prod_Serv.Codigo, View_Movimento_Prod_Serv.Nome, View_Movimento_Prod_Serv.Quantidade, View_Movimento_Prod_Serv.Preco_Unitario, 
                                    View_Movimento_Prod_Serv.Preco_Total_Sem_Desconto FROM View_Movimento_Prod_Serv, Classe_Imposto 
                                    WHERE Classe_Imposto.Ordem = View_Movimento_Prod_Serv.Ordem_Classe_Imposto AND View_Movimento_Prod_Serv.Efetivado_Financeiro=1 
                                        AND View_Movimento_Prod_Serv.Ordem_Vendedor = ? AND 
                                        CONVERT(DATE, View_Movimento_Prod_Serv.Data_Efetivacao_Estoque, 111) = CONVERT(DATE, GETDATE(), 111) ORDER BY View_Movimento_Prod_Serv.Ordem DESC");
    $UltimasVendas->bindParam(1, $rowCadastro->Ordem);
    $UltimasVendas->execute();
    $rowUltimasVendas = $UltimasVendas->fetchAll();
    
    
    
    /***
     * RANK DIA
     */
    $data1 = date("Y-m-d") . 'T00:00:00.000';
    $data2 = date("Y-m-d", strtotime("+1 day")) . 'T00:00:00.000';
    
    echo "<h1>" . $data1 . '/' . $data2;
    
    
    $RankSQL = ("
                            SELECT 
                                    CAST(ISNULL(SUM(Comissoes.Count_Vendas), 0) as money) Quantidade_Itens,
                                    CAST(ISNULL(SUM(Comissoes.Quantidade_Vendas), 0) as money) Quantidade_Pecas,
                                    CAST(ISNULL(count(distinct (case when Operacoes_Entrada_Saida = 'S' then Comissoes.Sequencia end)), 0) as int) as Quantidade_Vendas,
                                    CAST(SUM(Preco_Total_Com_Desconto) as money) Valor_Vendas,
                                    CAST(SUM(Comissoes.Comissao_Vendedor1) as money) Valor_Comissoes,
                                    Funcionarios.Apelido as Vendedor_Apelido,
                                    Funcionarios.Codigo as Vendedor_Codigo

                            FROM
                            (
                                    SELECT 
                                            Movimento.Ordem_Filial AS Ordem_Filial,
                                            Movimento_Prod_Serv.Ordem_Vendedor AS Ordem_Vendedor,
                                            Movimento_Prod_Serv.Ordem_Vendedor2 AS Ordem_Vendedor2,		
                                            Movimento_Prod_Serv.Ordem_Prod_Serv Ordem_Prod_Serv ,
                                            Movimento.Sequencia Sequencia ,	
                                            Vendedor.Ordem Vendedor_Ordem,
                                            Operacoes.Entrada_Saida Operacoes_Entrada_Saida,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E' AND ((Operacoes.Movimenta_Comissao = 1 And Prod_Serv.Tipo <> 'V') OR (Operacoes.S_Processa_Servico = 1 And Prod_Serv.Tipo = 'V')) 
                                            THEN (-1) * Movimento_Prod_Serv.Comissao_Vendedor1 * Movimento_Prod_Serv.Quantidade_Itens 
                                            ELSE Movimento_Prod_Serv.Comissao_Vendedor1 * Movimento_Prod_Serv.Quantidade_Itens
                                            END AS Comissao_Vendedor1,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E' AND ((Operacoes.Movimenta_Comissao = 1 And Prod_Serv.Tipo <> 'V') OR (Operacoes.S_Processa_Servico = 1 And Prod_Serv.Tipo = 'V')) 
                                            THEN (-1) * Movimento_Prod_Serv.Comissao_Vendedor2 * Movimento_Prod_Serv.Quantidade_Itens 
                                            ELSE Movimento_Prod_Serv.Comissao_Vendedor2 * Movimento_Prod_Serv.Quantidade_Itens
                                            END AS Comissao_Vendedor2,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E' AND ((Operacoes.Movimenta_Comissao = 1 And Prod_Serv.Tipo <> 'V') OR (Operacoes.S_Processa_Servico = 1 And Prod_Serv.Tipo = 'V')) 
                                            THEN (-1) * Movimento_Prod_Serv.Comissao_Comissionado * Movimento_Prod_Serv.Quantidade_Itens 
                                            ELSE Movimento_Prod_Serv.Comissao_Comissionado * Movimento_Prod_Serv.Quantidade_Itens
                                            END AS Comissao_Comissionado,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E'
                                            THEN -Movimento_Prod_Serv.Quantidade_Itens
                                            ELSE Movimento_Prod_Serv.Quantidade_Itens
                                            END AS Quantidade_Itens ,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E'
                                            THEN -Movimento_Prod_Serv.Preco_Final_Sem_Frete
                                            ELSE Movimento_Prod_Serv.Preco_Final_Sem_Frete
                                            END AS Preco_Total_Com_Desconto ,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E'
                                            THEN 0
                                            ELSE 1
                                            END AS Count_Vendas,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E'
                                            THEN -Movimento_Prod_Serv.Quantidade 
                                            ELSE Movimento_Prod_Serv.Quantidade 
                                            END AS Quantidade_Vendas

                                    FROM Movimento
                                            INNER JOIN Operacoes ON Operacoes.Ordem = Movimento.Ordem_Operacao
                                            INNER JOIN Movimento_Prod_Serv ON Movimento_Prod_Serv.Ordem_Movimento = Movimento.Ordem
                                            INNER JOIN Prod_Serv ON Prod_Serv.Ordem = Movimento_Prod_Serv.Ordem_Prod_Serv
                                            LEFT JOIN Funcionarios Vendedor ON Vendedor.Ordem = Movimento_Prod_Serv.Ordem_Vendedor
                                    WHERE 1 = 1
                                    AND Movimento_Prod_Serv.Data_Passou_Efetivacao_Estoque >= '" . $data1 . "'
                                    AND Movimento_Prod_Serv.Data_Passou_Efetivacao_Estoque < '" . $data2 . "'
                                    AND Movimento.Apagado = 0 
                                    AND Movimento_Prod_Serv.Linha_Excluida = 0 
                                    AND Movimento_Prod_Serv.Data_Passou_Desefetivacao_Estoque IS NULL
                                    AND Vendedor.Codigo > 0 AND Vendedor.Codigo <> 5 AND Movimento.Situacao_Expedicao <> 'A' AND Movimento.Tipo_Operacao IN ('VND', 'VEF', 'VPC', 'FPV', 'DEV', 'CVE')
                            AND Movimento.Ordem_Filial = (SELECT Ordem FROM Filiais WHERE Codigo = " . LOJA . ")
                            ) Comissoes
                            INNER JOIN Funcionarios ON Funcionarios.Ordem = Comissoes.Vendedor_Ordem
                            GROUP BY Funcionarios.Apelido,
                                    Funcionarios.Codigo 
                            ORDER BY Valor_Vendas DESC");
    
    //echo "<br><br>";
    
    //echo $RankSQL;
    $Rank = $pdo->prepare($RankSQL);
    //$Rank->bindParam(1, $data1);
    //$Rank->bindParam(2, $data2);
    //$Rank->bindParam(3, $filial);
    
    $Rank->execute();
    $rowRank = $Rank->fetchAll();
    
    //var_dump($rowRank);
    //echo "<h1>.</h1>";
    
    //var_dump(array_search($rowCadastro->Codigo, array_column($rowRank, 'Vendedor_Codigo')));
    $RankKey = array_search($rowCadastro->Codigo, array_column($rowRank, 'Vendedor_Codigo'));
    
    if ($RankKey !== false) { 
        $RankDia = $RankKey + 1;
        //echo "FOUND";
    } else { 
        //echo "NF";
        $RankDia = 0;
        //echo "<h4>RK:" . $RankKey . " / ";
        //var_dump($RankKey);
    }
    

    
    /*
    if (($RankKey === NULL) or ($RankKey === '') or (!isset($RankKey)) or (empty($RankKey))) {
        $RankDia = 0;
        echo "Exe";
    } elseif ($RankKey == 0) {
        $RankDia = 1;
        echo "exe233";
    } else { 
        echo "Exe2";
        $RankDia = $RankKey + 1;
    }
     * 
     */
    
    





    /***
     * RANK MÊS
     */
    
    $mes = date("m");      // Mês desejado, pode ser por ser obtido por POST, GET, etc.
    $ano = date("Y"); // Ano atual
    $ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
    
    $data1 = date("Y-m-") . '01T00:00:00.000';
    $data2 = date("Y-m-") . $ultimo_dia . 'T00:00:00.000';
    
    $RankMesSQL = ("
                            SELECT 
                                    CAST(ISNULL(SUM(Comissoes.Count_Vendas), 0) as money) Quantidade_Itens,
                                    CAST(ISNULL(SUM(Comissoes.Quantidade_Vendas), 0) as money) Quantidade_Pecas,
                                    CAST(ISNULL(count(distinct (case when Operacoes_Entrada_Saida = 'S' then Comissoes.Sequencia end)), 0) as int) as Quantidade_Vendas,
                                    CAST(SUM(Preco_Total_Com_Desconto) as money) Valor_Vendas,
                                    CAST(SUM(Comissoes.Comissao_Vendedor1) as money) Valor_Comissoes,
                                    Funcionarios.Apelido as Vendedor_Apelido,
                                    Funcionarios.Codigo as Vendedor_Codigo

                            FROM
                            (
                                    SELECT 
                                            Movimento.Ordem_Filial AS Ordem_Filial,
                                            Movimento_Prod_Serv.Ordem_Vendedor AS Ordem_Vendedor,
                                            Movimento_Prod_Serv.Ordem_Vendedor2 AS Ordem_Vendedor2,		
                                            Movimento_Prod_Serv.Ordem_Prod_Serv Ordem_Prod_Serv ,
                                            Movimento.Sequencia Sequencia ,	
                                            Vendedor.Ordem Vendedor_Ordem,
                                            Operacoes.Entrada_Saida Operacoes_Entrada_Saida,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E' AND ((Operacoes.Movimenta_Comissao = 1 And Prod_Serv.Tipo <> 'V') OR (Operacoes.S_Processa_Servico = 1 And Prod_Serv.Tipo = 'V')) 
                                            THEN (-1) * Movimento_Prod_Serv.Comissao_Vendedor1 * Movimento_Prod_Serv.Quantidade_Itens 
                                            ELSE Movimento_Prod_Serv.Comissao_Vendedor1 * Movimento_Prod_Serv.Quantidade_Itens
                                            END AS Comissao_Vendedor1,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E' AND ((Operacoes.Movimenta_Comissao = 1 And Prod_Serv.Tipo <> 'V') OR (Operacoes.S_Processa_Servico = 1 And Prod_Serv.Tipo = 'V')) 
                                            THEN (-1) * Movimento_Prod_Serv.Comissao_Vendedor2 * Movimento_Prod_Serv.Quantidade_Itens 
                                            ELSE Movimento_Prod_Serv.Comissao_Vendedor2 * Movimento_Prod_Serv.Quantidade_Itens
                                            END AS Comissao_Vendedor2,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E' AND ((Operacoes.Movimenta_Comissao = 1 And Prod_Serv.Tipo <> 'V') OR (Operacoes.S_Processa_Servico = 1 And Prod_Serv.Tipo = 'V')) 
                                            THEN (-1) * Movimento_Prod_Serv.Comissao_Comissionado * Movimento_Prod_Serv.Quantidade_Itens 
                                            ELSE Movimento_Prod_Serv.Comissao_Comissionado * Movimento_Prod_Serv.Quantidade_Itens
                                            END AS Comissao_Comissionado,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E'
                                            THEN -Movimento_Prod_Serv.Quantidade_Itens
                                            ELSE Movimento_Prod_Serv.Quantidade_Itens
                                            END AS Quantidade_Itens ,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E'
                                            THEN -Movimento_Prod_Serv.Preco_Final_Sem_Frete
                                            ELSE Movimento_Prod_Serv.Preco_Final_Sem_Frete
                                            END AS Preco_Total_Com_Desconto ,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E'
                                            THEN 0
                                            ELSE 1
                                            END AS Count_Vendas,

                                            CASE WHEN Operacoes.Entrada_Saida = 'E'
                                            THEN -Movimento_Prod_Serv.Quantidade 
                                            ELSE Movimento_Prod_Serv.Quantidade 
                                            END AS Quantidade_Vendas

                                    FROM Movimento
                                            INNER JOIN Operacoes ON Operacoes.Ordem = Movimento.Ordem_Operacao
                                            INNER JOIN Movimento_Prod_Serv ON Movimento_Prod_Serv.Ordem_Movimento = Movimento.Ordem
                                            INNER JOIN Prod_Serv ON Prod_Serv.Ordem = Movimento_Prod_Serv.Ordem_Prod_Serv
                                            LEFT JOIN Funcionarios Vendedor ON Vendedor.Ordem = Movimento_Prod_Serv.Ordem_Vendedor
                                    WHERE 1 = 1
                                    AND Movimento_Prod_Serv.Data_Passou_Efetivacao_Estoque >= '" . $data1 . "'
                                    AND Movimento_Prod_Serv.Data_Passou_Efetivacao_Estoque < '" . $data2 . "'
                                    AND Movimento.Apagado = 0 
                                    AND Movimento_Prod_Serv.Linha_Excluida = 0 
                                    AND Movimento_Prod_Serv.Data_Passou_Desefetivacao_Estoque IS NULL
                                    AND Vendedor.Codigo > 0 AND Vendedor.Codigo <> 5 AND Movimento.Situacao_Expedicao <> 'A' AND Movimento.Tipo_Operacao IN ('VND', 'VEF', 'VPC', 'FPV', 'DEV', 'CVE')
                            AND Movimento.Ordem_Filial = (SELECT Ordem FROM Filiais WHERE Codigo = " . LOJA . ")
                            ) Comissoes
                            INNER JOIN Funcionarios ON Funcionarios.Ordem = Comissoes.Vendedor_Ordem
                            GROUP BY Funcionarios.Apelido,
                                    Funcionarios.Codigo 
                            ORDER BY Valor_Vendas DESC");
    
    //echo "<br><br>";
    
    //echo $RankSQL;
    $RankMes = $pdo->prepare($RankMesSQL);
    //$Rank->bindParam(1, $data1);
    //$Rank->bindParam(2, $data2);
    //$Rank->bindParam(3, $filial);
    
    $RankMes->execute();
    $rowRankMes = $RankMes->fetchAll();
    
    $RankMesKey = array_search($rowCadastro->Codigo, array_column($rowRankMes, 'Vendedor_Codigo'));
    if ($RankMesKey !== false) { 
        $RankMes = $RankMesKey + 1;
        //echo "FOUND";
    } else { 
        //echo "NF";
        $RankMes = 0;
        //echo "<h4>RK:" . $RankKey . " / ";
        //var_dump($RankKey);
    }
    
    /*
    if ($RankMesKey == NULL) {
        $RankMes = 0;
    } else {
        $RankMes = $RankMesKey + 1;
    }
     * 
     */

    

    
    
    
    
    /***
     * META
     */
    
    $data1 = date("Y-m-01");
    $data2 = date("Y-m-") . $ultimo_dia;
    
    $Meta = $pdo->prepare("SELECT Meta_Funcionario.*, Meta_Funcionario.Meta as MetaMes, Meta_Venda.* FROM Meta_Funcionario, Meta_Venda
                            WHERE CONVERT(DATE, Meta_Venda.Periodo_Inicio, 111) = CONVERT(DATE, ? , 111)
                            AND CONVERT(DATE, Meta_Venda.Periodo_Fim, 111) = CONVERT(DATE, ?, 111)
                            AND Meta_Funcionario.Ordem_Meta = Meta_Venda.Ordem AND Meta_Funcionario.Ordem_Funcionario = ?
                            ORDER BY Meta_Funcionario.Ordem DESC");
    $Meta->bindParam(1, $data1);
    $Meta->bindParam(2, $data2);
    $Meta->bindParam(3, $rowCadastro->Ordem);
    
    $Meta->execute();
    $rowMeta = $Meta->fetchAll();
   
    if (count($rowMeta) > 0) {
        
        $MetaDiaria = $rowMeta[0]['MetaMes'] / ($ultimo_dia-Check::DomingosMes(date("m")));
        $MetaMensal = $rowMeta[0]['MetaMes'];

        $MetaDiariaPorcCalc = ($RankDia > 0 ? $rowRank[$RankKey]['Valor_Vendas'] : 0);
        $MetaDiariaPorc = ceil((100/$MetaDiaria) * $MetaDiariaPorcCalc);
        $MetaMensalPorc = ceil((100/$MetaMensal) * $rowRankMes[$RankMesKey]['Valor_Vendas']);
    } else {
        $MetaDiaria = 0;
        $MetaMensal = 0;
        
        $MetaDiariaPorcCalc=0;
        $MetaDiariaPorc = 0;
        $MetaMensalPorc = 0;
    }
   
    //Cor Meta Diaria
    if ($MetaDiariaPorc <= 30) {
        $MetaCor = "danger";
    } elseif (($MetaDiariaPorc > 30) and ($MetaDiariaPorc <=60)) {
        $MetaCor = "warning";
    } elseif (($MetaDiariaPorc > 61) and ($MetaDiariaPorc < 100)) {
        $MetaCor = "info";
    } elseif ($MetaDiariaPorc >= 100) {
        $MetaCor = "success";
    }
   
    //Cor Meta Mensal
    if ($MetaMensalPorc <= 30) {
        $MetaMesCor = "danger";
    } elseif (($MetaMensalPorc > 30) and ($MetaMensalPorc <=60)) {
        $MetaMesCor = "warning";
    } elseif (($MetaMensalPorc > 61) and ($MetaMensalPorc < 100)) {
        $MetaMesCor = "info";
    } elseif ($MetaMensalPorc >= 100) {
        $MetaMesCor = "success";
    }
   
    
    
    $CadastroFoto = $pdo->prepare("SELECT TOP 1 * FROM Funcionarios_Fotos WHERE Ordem_Funcionario = ?");
    $CadastroFoto->bindParam(1, $rowCadastro->Ordem);
    $CadastroFoto->execute();
    $rowCadastroFoto = $CadastroFoto->fetch(PDO::FETCH_OBJ);

    
    
?>

<div class="shadow-lg" style="background:#f4f4f4;">
    
    
    <div class="container">       
        
        
        <div class="row pt-5 pb-4">

            <div class="col-sm-3 text-center" >
                    <div class="rounded-circle" style="width:80px; margin: 0px auto 20px; height: 80px; background: #777; <?php if (!empty($rowCadastroFoto->Imagem)) { ?> background:url(<?php  echo 'data:image/jpeg;base64,' . $rowCadastroFoto->Imagem;  ?>); <?php } ?> background-size: 130%; background-position: 50%;"></div>
            </div>

            <div class="col text-left">    
                <h2><?php echo Check::Words($rowCadastro->Nome, 3, ' '); ?></h2>
                <div class="lead"><b>CPF: <?php echo $rowCadastro->CPF; ?></b></p></div>
            </div>
            <div class="col-2">
                <button type="button" onclick="location.href='index.php';" class="btn btn-primary btn-lg"> Sair / Fechar </button>
            </div>
            
        </div>
    </div>        
</div>

<div class="container mt-5">
        

                <div class="row">
                    <div class="col-sm">
                        Rank/Dia<br>
                        <h2><span style="width:100%;" class="badge badge-secondary">
                                <?php  
                                    if ($RankDia == 1) {
                                        echo '<img src="' . HOME . '/img/icon/trophy.png" valign="absmiddle" /> ';
                                    }
                                        echo $RankDia . "º" ?>   
                            </span></h2>
                        <?php if ($RankDia > 1) { echo "<h6>" . ($RankDia-1) . "º  &raquo; " . $rowRank[$RankKey-1]['Vendedor_Apelido'] . "</h6>"; } ?>
                        <?php if (isset($rowRank[$RankKey+1]['Vendedor_Apelido'])) { echo "<h6>" . ($RankDia+1) . "º &raquo; " . $rowRank[$RankKey+1]['Vendedor_Apelido'] . "</h6>"; } ?>
                    </div>

                    <div class="col-sm">
                        Vendas/Dia<br>
                        <h2><span style="width:100%;" class="badge badge-secondary">R$ <?php if ($RankDia != 0) { echo Check::Moeda($rowRank[$RankKey]['Valor_Vendas']); } else { echo "0,00"; } ?></span></h2>
                    </div>
                    
                    <div class="col-sm">
                        Rank/Mês<br>
                        <h2><span style="width:100%;" class="badge badge-info">
                            <?php 
                            if ($RankMes == 1) {
                                        echo '<img src="' . HOME . '/img/icon/trophy.png" valign="absmiddle" /> ';
                                    }
                            echo $RankMes . "º" ?></span></h2>
                        <?php if ($RankMes > 1) { echo "<h6>" . ($RankMes-1) . "º  &raquo; " . $rowRankMes[$RankMesKey-1]['Vendedor_Apelido'] . "</h6>"; } ?>
                        <?php if ($rowRankMes[$RankMesKey+1]['Vendedor_Apelido'] != NULL) { echo "<h6>" . ($RankMes+1) . "º &raquo; " . $rowRankMes[$RankMesKey+1]['Vendedor_Apelido'] . "</h6>"; } ?>
                    </div>

                    <div class="col-sm">
                        Vendas/Mês<br>
                        <h2><span style="width:100%;" class="badge badge-info">R$ <?php if ($RankMes != 0) { echo Check::Moeda($rowRankMes[$RankMesKey]['Valor_Vendas']); } else { echo "0,00"; } ?></span></h2>
                    </div>
                </div>
    
    
                <div>
                    <div class="mt-3">
                        <h5>Meta Diária: <span class="badge badge-<?php echo $MetaCor; ?>">R$ <?php echo Check::Moeda($MetaDiaria); ?></span> <?php if ($MetaDiariaPorc >= 100) { echo '<img align="absmiddle" src="' . HOME . '/img/icon/trophy.png" />'; } ?> &nbsp; &nbsp; <span style="font-size:14px;">Meta até hoje</span> <span class="badge badge-primary">R$ <?php echo Check::Moeda($MetaDiaria * (date('d') - (Check::DomingosPeriodo(date("Y-m") . "-01", date("Y-m-d"))))); ?></span></h5>
                        <div class="progress" style="background:#ccc;">
                          <div class="progress-bar progress-bar-striped progress-bar-animated bg-<?php echo $MetaCor; ?>" role="progressbar" style="width: <?php echo $MetaDiariaPorc; ?>%" aria-valuenow="<?php echo $MetaDiariaPorc; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $MetaDiariaPorc; ?>%</div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Meta Mensal: <span class="badge badge-<?php echo $MetaMesCor; ?>">R$ <?php echo Check::Moeda($MetaMensal); ?></span> <?php if ($MetaMensalPorc >= 100) { echo '<img align="absmiddle" src="' . HOME . '/img/icon/trophy.png" />'; } ?></h5>
                        <div class="progress" style="background:#ccc;">
                          <div class="progress-bar progress-bar-striped progress-bar-animated bg-<?php echo $MetaMesCor; ?>" role="progressbar" style="width: <?php echo $MetaMensalPorc; ?>%" aria-valuenow="<?php echo $MetaMensalPorc; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $MetaMensalPorc; ?>%</div>
                        </div>
                    </div>
                </div>


            <!--
            <div class="alert alert-success mt-5" role="alert">
              <h4 class="alert-heading">Well done!</h4>
              <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
              <hr>
              <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.</p>
            </div>            
            -->
            
    <?php 
          
        $countUltimasVendas = count($rowUltimasVendas);
        
        if ($countUltimasVendas > 0) {
    ?>
            <h4 class="mt-5">Últimas Vendas Realizadas</h4>

            <!--<div class="alert alert-warning" role="alert">
                Trocas e cancelamentos serão exibidos na tabela abaixo.
            </div> -->
            
            <div class="table-responsive">
                <table class="mt-3 table table-striped">
                    <thead class="thead-dark">
                    <tr>
                      <th scope="col" width="10%">Código</th>
                      <th scope="col" width="10%">Quant.</th>
                      <th scope="col" width="60%">Nome</th>
                      <th scope="col" width="10%" style="text-align: right;">P. Unit.</th>
                      <th scope="col" width="10%" style="text-align: right;">P. Total</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    foreach ($rowUltimasVendas as $row) {
                  ?>
                      <tr class="<?php
                            if ($row['Tipo_Operacao'] == 'E') {
                                echo 'table-danger';
                            }
                          ?>">
                      <th scope="row"><?php echo $row['Codigo'] ?></th>
                      <td><b><?php echo ceil($row['Quantidade']); ?></b></td>
                      <td>
                          <?php
                            if ($row['Tipo_Operacao'] == 'E') {
                                echo '<img src="' . HOME . '/img/icon/arrow.png" align="absmiddle" />';
                            }
                          ?>
                          <?php echo $row['Nome']; ?>
                      </td>
                      <td class="text-right text-nowrap"><b>R$ <?php echo Check::Moeda($row['Preco_Unitario']); ?></b></td>
                      <td class="text-right text-nowrap"> <?php echo Check::Moeda($row['Preco_Total_Sem_Desconto']); ?></td>
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
