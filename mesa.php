<?php
require '_app/config.inc.php';

$timer = 800;

require '_top.php';

$idmesa = filter_input(INPUT_GET, 'idmesa', FILTER_VALIDATE_INT);
if ($idmesa == '') {
    header("Location:index.php");
}


$Mesa = new Read();
$Mesa->ExeRead("mesa", "WHERE idmesa = :idmesa", "idmesa={$idmesa}");
$rowMesa = $Mesa->getResult()[0];
?>

<script type="text/javascript">

    var mesa = <?php echo $idmesa; ?>

    function AbreProduto(idproduto) {
        $("#infoProduto").modal('show');
        $.ajax({
            type: "GET", url: "ajax/produto_info.ajax.php?idproduto=" + idproduto,
            success: function (data) {
                $("#modalContent").html(data);
            }
        })
    }

</script>

<div class="shadow-lg" style="background:#f4f4f4; background-image: url(<?php echo HOME; ?>/img/category/<?php
switch ($rowMesa['tipo']) {
    case 'Aniversário':
        echo "aniversario.png";
        break;
    case 'Casamento':
        echo "casamento.png";
        break;
}
?>); background-size: auto 100%;">
    <div class="container">        
        <div class="row pt-5 pb-3">
            <div class=" col-sm-5 text-center " >
                <div class="rounded-circle " style="width:210px; margin: 0px auto 20px; height: 210px; background: #777; background:url(<?= HOME . '/uploads/'; ?><?php echo $rowMesa['foto']; ?>); background-size: 110%; background-position: 100%;"></div>
            </div>

            <div class="col text-left">    

                <h2><?php echo $rowMesa['titulo']; ?></h2>
                <p class="lead"><b><?php echo $rowMesa['tipo']; ?></b><br /><?php echo Check::DataExtenso($rowMesa['data_encerramento']); ?></p>
                <p class="lead">
                    <?php if ($rowMesa['pai'] != '') { ?>Pai: <?php echo $rowMesa['pai']; ?><?php } ?> <?php if (($rowMesa['pai'] != "") && ($rowMesa['mae'] != "")) {
                        echo " | ";
                    } ?>
<?php if ($rowMesa['mae'] != '') { ?>Mãe: <?php echo $rowMesa['mae']; ?><?php } ?>
                </p>
                <p class="lead">Vendedor(a): <?php echo $rowMesa['vendedor']; ?>
            </div>
        </div>
    </div>        
</div>



<div class="container mt-5">


    <button type="button" onmousedown="location.href = 'index.php';" class="btn btn-primary btn-lg mb-4">&laquo; Voltar</button>

    <div class="container marketing">
    <div class="row">
        <?php
        $Produtos = new Read();
        $Produtos->ExeRead("mesa_produtos", "WHERE idmesa = :idmesa AND lixeira=0 ORDER BY vendido, idmesa_produtos DESC", "idmesa={$idmesa}");

        $i = 0;

        foreach ($Produtos->getResult() as $rowProduto) {

            $idmesa_produtos = $rowProduto['idmesa_produtos'];
            $total = $rowProduto['valor_total'];

            if ($i == 0) {
                //echo '<div class="row" >';
            }

            $Kit = new Read();
            $Kit->ExeRead("mesa_produtos_kit", "WHERE idmesa_produtos = :idmesa_produtos", "idmesa_produtos={$idmesa_produtos}");
            ?>


            <div class="col-sm-4 text-center mt-5">
    <?php if ($rowProduto['vendido'] == 1) { ?><h4><span class="badge badge-warning">Produto Vendido</span></h4><?php } ?>
                <div><img onclick="AbreProduto(<?php echo $rowProduto['idmesa_produtos']; ?>);" <?php if ($rowProduto['vendido'] == 1) { ?> style="opacity:0.5;" <?php } ?> class="mt-4" src="<?= HOME; ?>/uploads/<?php echo $rowProduto['foto']; ?>"  height="130" /></div>
                <h5 class="mt-3" <?php if ($rowProduto['vendido'] == 1) { ?>style="text-decoration:line-through; "<?php } ?>>
                <?php if ($Kit->getRowCount() > 0) { ?><span class="badge badge-success">KIT </span><?php } ?> <?php echo $rowProduto['nome']; ?></h5>
    <?php if ($rowProduto['caracteristica'] != "") { ?><p class="font-italic"><?php echo $rowProduto['caracteristica']; ?></p><?php } ?>

                <p>
    <?php echo $rowProduto['quantidade']; ?> x <?php echo Check::Moeda($rowProduto['valor']); ?>
                </p>    


                <?php
                if ($Kit->getRowCount() > 0) {
                    ?>
                    <div style="margin-top:10px;">
                        <ul class="produtos_kit">
                            <?php
                            foreach ($Kit->getResult() as $rowKit) {
                                $total += $rowKit['valor_total'];
                                ?>
                                <li>
                                    <?php echo $rowKit['quantidade'] . "x " . Check::Words($rowKit['nome'], 3) . " > " . Check::Moeda($rowKit['valor']) . " | <u>" . Check::Moeda($rowKit['valor_total']) . "</u>"; ?>
                                <?php if ($rowKit['caracteristica'] != "") { ?><p class="font-italic"><?php echo $rowKit['caracteristica']; ?></p><?php } ?>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
    <?php } ?>

                <h4><span class="badge badge-secondary">R$ <?php echo Check::Moeda($total); ?></span></h4>

    <!-- <p><a class="btn btn-danger" href="mesa.php?idmesa=<?php echo $rowMesa['idmesa']; ?>" role="button">Ver Produtos</a></p> -->
            </div><!-- /.col-lg-4 -->



            <?php
            if ($i == 2) {
                //echo '</div><!-- /.row -->';
            }

            $i++;
            if ($i == 3) {
                $i = 0;
            }
        } //foreach
        ?>





        

    </div>
        
        
        
    </div>
    
    <button onmousedown="location.href = 'index.php';" type="button" class="btn btn-primary btn-lg mt-4">&laquo; Voltar</button>
    
</div>


<!-- Modal -->
<div class="modal fade" id="infoProduto" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Informação do Produto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modalContent">
                ...
            </div>
            <div style="margin:0px; padding: 6px; background:#ffaaaa;">Para comprar este produto. Procure o(a) Vendedor(a): <b><?php echo $rowMesa['vendedor']; ?></b></div>
        </div>
    </div>
</div>

<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function () {
        'use strict';

        window.addEventListener('load', function () {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            var formskit = document.getElementsByClassName('needs-validation-kit');

            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            var validationkit = Array.prototype.filter.call(formskit, function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

        }, false);
    })();
</script>






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
