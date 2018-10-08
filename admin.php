<?php 
    require '_app/Config.inc.php';
    require '_top.php';

if (isset($_SESSION['cliente'])) {
    unset($_SESSION['cliente']);
}

    
    $logoff = filter_input(INPUT_GET, "logoff", FILTER_VALIDATE_BOOLEAN);
    
    if ($logoff) {
        unset($_SESSION['userlogin']);
        header("Location:admin.php");
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
    <form class="form-signin" name="PostForm" method="POST" action="">
      
       <?php 
       
        $Login = new Login(1);
        if ($Login->CheckLogin()) {
            header("Location:index.php");
        }
        
        $dataLogin = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        
        if (!empty($dataLogin['AdminLogin'])) {
            $Login->ExeLogin($dataLogin);
            if (!$Login->getResult()) {
                WSErro($Login->getError()[0], $Login->getError()[1]);
            } else { 
                header("Location:index.php");
            }
        }
       
       ?> 
        
        
      <h1 class="h3 mt-5 mb-3 text-center font-weight-normal">Entrar</h1>
      
      <label class="mt-4 lead" for="idfuncionario">Código</label>
      <input type="number" id="idfuncionario" name="idfuncionario" class="form-control" placeholder="Código do Sistema" maxlength="11" required>
      
      <label class="mt-4 lead" for="senha">Senha</label>
      <input type="password" id="senha" name="senha" class="form-control" maxlength="30" required>
      
      <button class="mt-4 btn btn-lg btn-primary btn-block" type="submit" name="AdminLogin" value="Entrar">Entrar</button>
      
      <p class="mt-5 mb-3 text-muted"></p>
    </form>
</div>

<?php require '_bottom.php'; ?>