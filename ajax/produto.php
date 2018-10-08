<?php

        require '../_app/config.inc.php';
        
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        $Produto = $pdo->query("SELECT Prod_Serv.Codigo, Prod_Serv.Nome, View_Prod_Serv_Precos.Preco 
                                FROM Prod_Serv 
                                INNER JOIN View_Prod_Serv_Precos ON View_Prod_Serv_Precos.Codigo=Prod_Serv.Codigo AND View_Prod_Serv_Precos.Ordem_Tabela_Preco=7 
                                WHERE Prod_Serv.Codigo='{$id}'");
        $rowProduto = $Produto->fetch(PDO::FETCH_OBJ);
        
        //var_dump($rowProduto);
        echo json_encode($rowProduto);
	
?>	
