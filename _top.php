<?php
    if (!empty($_COOKIE['loja'])) {
        //LOJA
        $cLoja = unserialize($_COOKIE['loja']);
        define('LOJA', $cLoja['filial']);
    } else {
        header("Location:filial.php");
    }
?>
<!doctype html>
<html lang="pt-br">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Font -->
        <!-- <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> -->
        <style type="text/css">
            @font-face {
                font-family: Roboto;
                src: url(<?php echo HOME; ?>/fonts/roboto/Roboto-Regular.ttf);
            }
        </style>
        
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="<?php echo HOME; ?>/css/bootstrap.css" crossorigin="anonymous">

        <!-- Others CSS Code -->
        <link rel="stylesheet" href="<?php echo HOME; ?>/_js/jquery.keypad/css/jquery.keypad.css" />
        
        
        <!-- JS -->
        <script src="<?php echo HOME; ?>/_js/jquery/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="<?php echo HOME; ?>/_js/inputmask/branches/3.x/dist/jquery.inputmask.bundle.js"></script>
        <script src="<?php echo HOME; ?>/_js/popper/popper.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo HOME; ?>/_js/jquery.keypad/js/jquery.plugin.min.js" /></script>
        <script src="<?php echo HOME; ?>/_js/jquery.keypad/js/jquery.keypad.min.js" /></script>
        <script src="<?php echo HOME; ?>/_js/jquery.keypad/js/jquery.keypad-pt-BR.js" /></script>

        <link rel="stylesheet" href="<?php echo HOME; ?>/css/global.css" />

        <meta http-equiv="refresh" content="<?php if (isset($timer)) { echo $timer; } else { echo "500"; } ?>;URL='index.php'">

        <title>Cromos S - <?php echo $FilialConfig[LOJA]['nome']; ?> - Lista de Mesas</title>
    </head>
    <body>

        <header>
            <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
                <a class="navbar-brand" href="<?= HOME; ?>/index.php"><img src="<?php echo HOME; ?>/img/filial/<?php echo $FilialConfig[LOJA]['logo']; ?>" height="32"></a></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">

                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="<?= HOME; ?>/index.php">Mesas</a>
                        </li>
                        <?php if ($is_login) {?>
                        <li class="nav-item active">
                            <a class="nav-link" href="<?= HOME; ?>/cadastro.php">Monte sua Mesa</a>
                        </li>
                        <?php } ?>
                        <li class="nav-item active">
                            <a class="nav-link" href="<?= HOME; ?>/cliente.php">Consulta de Limite</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="<?= HOME; ?>/vendedor_entrar.php">Vendedor(a)</a>
                        </li>
                        <?php if (!$is_login) { ?>
                        <li class="nav-item active gerenciar" >
                            <a class="nav-link" href="<?= HOME; ?>/admin.php">Gerenciar</a>
                        </li>
                        <?php } ?>
                        <!--<li class="nav-item">
                            <a class="nav-link disabled" href="#">Disabled</a>
                        </li>-->
                    </ul>
                    
                    <?php if ($is_login) { ?>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <?php echo $_SESSION['userlogin']['apelido']; ?>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                          <a class="dropdown-item" href="<?php echo HOME; ?>/admin">Gerenciar</a>
                          <a class="dropdown-item" href="<?php echo HOME; ?>/filial.php?action=filial">Trocar Filial</a>
                          <a class="dropdown-item" href="<?php echo HOME; ?>/admin.php?logoff=true">Sair</a>
                        </div>
                    </div>                    
                    <?php } ?>
                </div>
            </nav>
        </header>

        <main role="main">
