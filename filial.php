<?php
require '_app/Config.inc.php';

$filial = filter_input_array(INPUT_GET, FILTER_DEFAULT);
$action = filter_input(INPUT_GET, "action");

if (isset($action) && $action == "filial") {
    unset($_COOKIE['loja']);
}

if (isset($filial) && isset($filial['filial']) && $filial['filial'] != "") {
    if (isset($filial['keyboard']) && $filial['keyboard']) {
        $filial['keyboard'] = true;
    } else {
        $filial['keyboard'] = false;
    }
    
    $expira = time() + 60 * 60 * 24 * 120;
    setcookie("loja", serialize($filial), $expira);
    header("Location:index.php");
}

if (isset($_COOKIE['loja'])) {
    $cLoja = unserialize($_COOKIE['loja']);
} else {
    $cLoja['filial'] = '';
}

if ($cLoja['filial'] <> '') {
    header("Location:index.php");
}
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Cromos S - Filial</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <style type="text/css">
            @font-face {
                font-family: Roboto;
                src: url(<?php echo HOME; ?>/fonts/roboto/Roboto-Regular.ttf);
            }
        </style>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="<?php echo HOME; ?>/css/bootstrap.css" crossorigin="anonymous">

        <!-- JS -->
        <script src="<?php echo HOME; ?>/_js/jquery/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="<?php echo HOME; ?>/_js/inputmask/branches/3.x/dist/jquery.inputmask.bundle.js"></script>
        <script src="<?php echo HOME; ?>/_js/popper/popper.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo HOME; ?>/_js/jquery.keypad/js/jquery.plugin.min.js" /></script>

    <style type="text/css">
        html,
        body {
            height: 100%;
        }

        body {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }

        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }
        .form-signin .checkbox {
            font-weight: 400;
        }
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

</head>
<body>
    <div class="form-signin">
        <form action="" method="GET">
        <h4 class="text-center">Cromos S</h4>
        <h6 class="mb-4 text-center">Selecione a filial</h6>
        
        
        <div class="btn-group-vertical">
            <button type="submit" name="filial" value="1" class="btn btn-secondary">01 - Visual Fashion Shop</button>
            <button type="submit" name="filial" value="4" class="btn btn-secondary">04 - Visual Fashion Shop (Santana)</button>
            <button type="submit" name="filial" value="6" class="btn btn-secondary">06 - Dular Baby Teen</button>
            <button type="submit" name="filial" value="11" class="btn btn-secondary">11 - Dular Free Shop</button>
        </div>


        <div class="mt-3">
            <input type="checkbox" name="keyboard" value="true" /> Ativar teclado virtual<br>Marque se for filial 01.
        </div>
        </form>
    </div>
</body>
</html>
