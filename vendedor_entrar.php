<?php 
    require '_app/Config.inc.php';
    
    $timer = 60;
    
    require '_top.php';
    
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    
    //var_dump($post);
    
    if (isset($post) && $post['SendPostForm']) {
        unset($post['SendPostForm']);
                
        if ($post['codigo'] == '') {
            $erro = true;
        } else {
            

            $Cadastro = $pdo->prepare("SELECT Codigo, Nome FROM Funcionarios WHERE Codigo = ? ");
            $Cadastro->bindParam(1, $post['codigo']);
            $Cadastro->execute();
            $rowCadastro = $Cadastro->fetchAll();//(PDO::FETCH_OBJ);
            $countCadastro = count($rowCadastro);

            //var_dump($rowCadastro);

            //echo $countCadastro;

            if ($countCadastro > 0) {

                //$caracteres1 = strlen(trim($rowCadastro[0]['Fone_1']));
                //$caracteres2 = strlen(trim($rowCadastro[0]['Fone_2']));

                //echo "<p>";

                //echo $caracteres1;
                //echo "ppp" . trim($rowCadastro[0]['Fone_1']);

                //echo substr($rowCadastro->Fone_1, $caracteres1-4, 4);
                //if (substr(trim($rowCadastro[0]['Fone_1']), $caracteres1-4, 4) != $post['fone']) {
                //    $erro = true;
                //} else {

                //    $dadosCliente['Ordem'] = $rowCadastro[0]['Ordem'];
                //    $dadosCliente['Codigo'] = $rowCadastro[0]['Codigo'];
                //    $dadosCliente['CPF'] = $rowCadastro[0]['CPF'];

                //    if (!session_id()) {
                //        session_start();
                //    }

                //    $_SESSION['cliente'] = $dadosCliente;

                    header("Location:vendedor.php?codigo=" . $post['codigo']);

                //}
            } else { $erro = true; }

            if (isset($erro) && ($erro)) {
                $aviso = "Cadastro não encontrado.";
            }
            
            
        }
        
    }
?>

<style type="text/css">
.form-signin {   width: 100%;   max-width: 330px;  padding: 15px; margin: auto; }
.form-signin .checkbox {   font-weight: 400; }
.form-signin .form-control {
  position: relative;
  box-sizing: border-box;
  height: auto;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
</style>



    <div class="container">
        <div class="py-5 lead col text-center"><b>  
            <?php
             if (isset($aviso)) {
                echo '<div class="mt-5 alert alert-warning" role="alert">
                         ' . $aviso . '
                      </div>';
             }
             ?></b>
        </div>
    </div>


<div>

    <div class="container">
        
        <button type="button" onmousedown="location.href='index.php';" class="btn btn-primary btn-lg">&laquo; Voltar para Mesas</button>
        
    </div>    
    
    
    <form class="form-signin" id="form-signin" name="PostForm" method="POST" action="">
      
        <div class="text-center">
      <img class="mt-5" class="mb-4" src="img/icon/homem.png" />
      <img class="mt-5" class="mb-4" src="img/icon/mulher.png" />
        </div>
      <h1 class="h3 mt-4 mb-3 text-center font-weight-normal">Exclusivo para Funcionários</h1>
      
      <label class="mt-3 lead" for="codigo">Informe seu Código</label>
      <input type="text" id="codigo" name="codigo" autocomplete="off" class="form-control lead" placeholder="Código" autofocus maxlength="11" required>
      
      <!--
      <label class="mt-4 lead" for="pin">PIN</label>
      <input type="password" id="pin" name="pin" class="form-control" maxlength="10" required> 
      -->
      <?php if (isset($cLoja['keyboard']) && $cLoja['keyboard'] == true) { ?>
      <div class="botoes">
          <button type="button" class="btn btn-secondary mt-3" onmousedown="$('#codigo').val($('#codigo').val() + '1');">1</button>
          <button type="button" class="btn btn-secondary mt-3" onmousedown="$('#codigo').val($('#codigo').val() + '2');">2</button>
          <button type="button" class="btn btn-secondary mt-3" onmousedown="$('#codigo').val($('#codigo').val() + '3');">3</button>
          <button type="button" class="btn btn-secondary mt-1" onmousedown="$('#codigo').val($('#codigo').val() + '4');">4</button>
          <button type="button" class="btn btn-secondary mt-1" onmousedown="$('#codigo').val($('#codigo').val() + '5');">5</button>
          <button type="button" class="btn btn-secondary mt-1" onmousedown="$('#codigo').val($('#codigo').val() + '6');">6</button>
          <button type="button" class="btn btn-secondary mt-1" onmousedown="$('#codigo').val($('#codigo').val() + '7');">7</button>
          <button type="button" class="btn btn-secondary mt-1" onmousedown="$('#codigo').val($('#codigo').val() + '8');">8</button>
          <button type="button" class="btn btn-secondary mt-1" onmousedown="$('#codigo').val($('#codigo').val() + '9');">9</button>
          <button type="button" class="btn btn-secondary mt-1" onmousedown="$('#codigo').val($('#codigo').val() + '0');">0</button>
          <button type="button" class="btn btn-warning mt-1" style="width:65%;" onmousedown="$('#codigo').val('');">Limpar</button>
      </div>
      <?php } ?>
      <button class="mt-4 btn btn-lg btn-primary btn-block" type="submit" name="SendPostForm" id="SendPostForm" value="SendPostForm">Entrar</button>
      
      <p class="mt-5 mb-3 text-muted"></p>
    </form>
</div>

<script type="text/javascript">
    //$('#cpf,#fone').keypad({keypadClass: 'midnightKeypad', prompt: '', closeText: 'OK', clearText: '«', backText: '‹'});
    
    $("#form-signin").submit(function() {
       $("#SendPostForm").html("Carregando...");
    });
    
        
</script>
<style type="text/css">
    
    .botoes button { width:32%; height: 60px; padding-top: 5px; }
    #cpf { font-size:18px; font-weight: bold; }
    
    /* .keypad-popup.midnightKeypad { background: #10085a; }  */
    .midnightKeypad .keypad-key, .midnightKeypad .keypad-special { 
        width: 60px; height: 60px; padding: 5px; border: 1px solid #ccc;  font-size: 2em; font-weight: bold;     } 
    .midnightKeypad .keypad-key-down { padding: 7px 3px 3px 7px; } 
    .midnightKeypad .keypad-space { width: 38px; height: 8px; }
</style>
<?php require '_bottom.php'; ?>