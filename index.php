<?php
require './_app/config.inc.php';
require '_top.php';

if (isset($_SESSION['cliente'])) {
    unset($_SESSION['cliente']);
}
?>            

<div id="myCarousel" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active" style="background:url(<?= HOME; ?>/img/featured/15anos.jpg); background-size: auto 100%; background-position: center center;">
            <div class="first-slide"></div>
            <div class="container">
                <div class="carousel-caption text-left">
                </div>
            </div>
        </div>
        <div class="carousel-item" style="background:url(<?= HOME; ?>/img/featured/casamento.jpg); background-size: auto 100%; background-position: center center;">
            <div class="first-slide"></div>
            <div class="container">
                <div class="carousel-caption text-left">
                </div>
            </div>
        </div>
        <div class="carousel-item" style="background:url(<?= HOME; ?>/img/featured/casanova.jpg); background-size: auto 100%; background-position: center center;">
            <div class="first-slide"></div>
            <div class="container">
                <div class="carousel-caption text-left">
                </div>
            </div>
        </div>
        <!--
        <div class="carousel-item">
            <img class="third-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Third slide">
            <div class="container">
                <div class="carousel-caption text-right">
                    <h1>Descontos para os seus Convidados</h1>
                    <p>Monte sua Mesa na Dular Free Shop e presenteamos seus convidados com um ticket de até 15% de Desconto</p>
                    <p><a class="btn btn-lg btn-primary" href="#" role="button">Monte sua Mesa aqui</a></p>
                </div>
            </div>
        </div>
        -->
    </div>
    <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

<div class="container">
    <a onmousedown="location.href = 'cliente_entrar.php';" type="button" class="btn btn-danger btn-lg btn-block" style="padding:16px; color:#ffffff; font-weight: bold;">Consulte o Limite e Parcelas do<br> Cartão Credfashion. Clique aqui!</a>
</div>

<div class="py-5 text-center">
    <h2>Mesas</h2>
    <p class="lead">Escolha uma para ver os produtos.</p>
</div>

<!-- Marketing messaging and featurettes
================================================== -->
<!-- Wrap the rest of the page in another container to center all the content. -->

<div class="container marketing">
    <div class="row">
        <?php
        $Mesa = new Read();
        $Mesa->FullRead("SELECT * FROM mesa WHERE data_encerramento + 1 >= now() AND idloja=" . LOJA . " ORDER BY data_encerramento");

        $i = 0;

        foreach ($Mesa->getResult() as $rowMesa) {


            if ($i == 0) {
                //echo '<div class="row">';
            }
            ?>


            <div class="col-sm-4 text-center mt-4">
                <div onclick="location.href = 'mesa.php?idmesa=<?php echo $rowMesa['idmesa']; ?>';" class="rounded-circle" style="width:150px; margin: 0px auto; height: 150px; background:url(<?= HOME; ?>/uploads/<?php echo $rowMesa['foto']; ?>); background-size: 100%; background-position: 100%;"></div>
                <h2><?php echo $rowMesa['titulo']; ?></h2>
                <p>
                    <?php echo $rowMesa['tipo']; ?>
                    <?php
                    switch ($rowMesa['tipo']) {
                        case 'Aniversário':
                            if ($rowMesa['mae'] != '') {
                                echo '<br>Mãe: ' . $rowMesa['mae'];
                            }
                            if ($rowMesa['pai'] != '') {
                                echo '<br>Pai: ' . $rowMesa['pai'];
                            }
                            break;
                    }
                    ?>
                    <br>Data: <?php echo Check::DataDiaMes($rowMesa['data_encerramento']); ?>
                    <br>Vendedor: <?php echo $rowMesa['vendedor']; ?>
                </p>    
                <p>
                    <a class="btn btn-danger" href="mesa.php?idmesa=<?php echo $rowMesa['idmesa']; ?>" role="button">Ver Produtos</a>
                </p>
            </div><!-- /.col-lg-4 -->


            <?php
            if ($i == 2) {
                // echo '</div><!-- /.row -->';
            }

            $i++;
            if ($i == 3) {
                $i = 0;
            }
        } //foreach
        ?>


    </div>


</div>



<div class="container">
                            <!-- START THE FEATURETTES -->

                            <hr class="featurette-divider">

                            <div class="row featurette">
                              <div class="col-md-7">
                                <h2 class="featurette-heading">Seu aniversário com <span class="text-muted">os melhores presentes.</span></h2>
                                <p class="lead">Monte a sua mesa de aniversário na Dular e tenha a certeza de ter os melhores presentes. Além de proporcionar aos seus convidados um desconto exclusivo de até 15% no Cartão Credfashion.</p>
                              </div>
                              <div class="col-md-5">
                                <img class="featurette-image img-fluid mx-auto" src="img/painel-aniversario.png" >
                              </div>
                            </div>

                            <hr class="featurette-divider">

                            <div class="row featurette">
                              <div class="col-md-7 order-md-2">
                                <h2 class="featurette-heading">Mesas de<span class="text-muted"> Casamento</span></h2>
                                <p class="lead">Vai Casar? Que tal fazer a sua mesa de casamento na Dular e enviar para seus convidados um ticket de desconto.</p>
                              </div>
                              <div class="col-md-5 order-md-1">
                                <img class="featurette-image img-fluid mx-auto" src="img/painel-casamento.png" >
                              </div>
                            </div>

                            <!-- /END THE FEATURETTES -->    

</div>
<?php require '_bottom.php'; ?>            