<?php

        require '../_app/config.inc.php';
        
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        $Cadastro = $pdo->query("SELECT Codigo, Nome
                                FROM Cli_For
                                WHERE Codigo <> 1 AND Codigo='{$id}'");
        $rowCadastro = $Cadastro->fetch(PDO::FETCH_OBJ);
        
        //var_dump($rowProduto);
        echo json_encode($rowCadastro);
	
?>	
