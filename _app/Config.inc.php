<?php

//Timezone
date_default_timezone_set("America/Sao_Paulo");

// CONFIGURAÇÃO SQL SERVER
$pdo = new PDO("sqlsrv:Server=192.168.3.250;Database=S9_Real;", "sa", "@sh@vis!");


// CONFIGRAÇÕES DO BANCO ####################
define('HOST', '192.168.3.251');
define('USER', 'root');
define('PASS', 'k(@uma90');
define('DBSA', 'cromos');

// DEFINE SERVIDOR DE E-MAIL ################
define('MAILUSER', '');
define('MAILPASS', '');
define('MAILPORT', ''); //porta
define('MAILHOST', '');

//DOMINIO
define('DOMINIO', 'univoto.com.br');

// DEFINE IDENTIDADE DO SITE ################
define('SITENAME', 'Univoto');
define('SITEDESC', 'Descricao');

// DEFINE A BASE DO SITE ####################
if ($_SERVER['SERVER_NAME'] == "192.168.3.251") {
    define('HOME', 'http://192.168.3.251:8080/cromoss');
} else {
    define('HOME', 'http://186.228.20.130:8080/cromoss');
}

//define('HOME', 'http://192.168.2.41/cromoss');
define('THEME', 'candidato');

define('INCLUDE_PATH', HOME . '/' . 'theme' . '/' . THEME);
define('REQUIRE_PATH', 'theme' . '/' . THEME);

//Diretorio de Upload
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/cromoss/uploads/');

// AUTO LOAD DE CLASSES ####################
function __autoload($Class) {

    $cDir = ['Conn', 'Helpers', 'Models'];
    $iDir = null;

    foreach ($cDir as $dirName):
        if (!$iDir && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR . $Class . '.class.php') && !is_dir(__DIR__ . DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR . $Class . '.class.php')):
            include_once (__DIR__ . DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR . $Class . '.class.php');
            $iDir = true;
        endif;
    endforeach;

    if (!$iDir):
        trigger_error("Não foi possível incluir {$Class}.class.php", E_USER_ERROR);
        die;
    endif;
}

// TRATAMENTO DE ERROS #####################
//CSS constantes :: Display div com Erros
define('WS_ACCEPT', 'accept');
define('WS_INFOR', 'infor');
define('WS_ALERT', 'alert');
define('WS_ERROR', 'error');


// CONFIGURAÇÃO DE FILIAIS ######################
$FilialConfig[1]['logo'] = 'visualfashion.png';
$FilialConfig[1]['nome'] = 'Visual Fashion Shop';

$FilialConfig[4]['logo'] = 'visualfashion.png';
$FilialConfig[4]['nome'] = 'Visual Fashion Shop Santana';

$FilialConfig[6]['logo'] = 'dularbabyteen.png';
$FilialConfig[6]['nome'] = 'Dular Baby Teen';

$FilialConfig[11]['logo'] = 'dularfreeshop.jpg';
$FilialConfig[11]['nome'] = 'Dular Free Shop';


//WSErro :: Exibe erros lançados :: Front
function WSErro($ErrMsg, $ErrNo, $ErrDie = null) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? WS_INFOR : ($ErrNo == E_USER_WARNING ? WS_ALERT : ($ErrNo == E_USER_ERROR ? WS_ERROR : $ErrNo)));
    echo "<div class=\"alert alert-primary\" role=\"alert\">{$ErrMsg}</div>";

    if ($ErrDie):
        die;
    endif;
}

//PHPErro :: personaliza o gatilho do PHP
function PHPErro($ErrNo, $ErrMsg, $ErrFile, $ErrLine) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? WS_INFOR : ($ErrNo == E_USER_WARNING ? WS_ALERT : ($ErrNo == E_USER_ERROR ? WS_ERROR : $ErrNo)));
    echo "<p class=\"trigger {$CssClass}\">";
    echo "<b>Erro na Linha: #{$ErrLine} ::</b> {$ErrMsg}<br>";
    echo "<small>{$ErrFile}</small>";
    echo "<span class=\"ajax_close\"></span></p>";

    if ($ErrNo == E_USER_ERROR):
        die;
    endif;
}


$getUrl = strip_tags(trim(filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
$setUrl = (empty($getUrl) ? 'index' : $getUrl);
$Url = explode('/', $setUrl);



set_error_handler('PHPErro');

ob_start();

if (!session_id()) {
    session_start();
}

if (!empty($_SESSION['userlogin']) && $_SESSION['userlogin']['idfuncionario'] != "") {
    $is_login = true;
} else {
    $is_login = false;
}