<?php
require '../_app/Config.inc.php';

$idmesa_produtos = filter_input(INPUT_GET, 'idproduto');

$Produtos = new Read();
$Produtos->ExeRead("mesa_produtos", "WHERE idmesa_produtos = :idmesa_produtos", "idmesa_produtos={$idmesa_produtos}");
$rowProduto = $Produtos->getResult()[0];

$total = $rowProduto['valor_total'];


$Kit = new Read();
$Kit->ExeRead("mesa_produtos_kit", "WHERE idmesa_produtos = :idmesa_produtos", "idmesa_produtos={$idmesa_produtos}");
?>


<div class="text-center">
<?php if ($rowProduto['vendido'] == 1) { ?><h4><span class="badge badge-warning">Produto Vendido</span></h4><?php } ?>
    <div><img onclick="AbreProduto(<?php echo $rowProduto['idmesa_produtos']; ?>);" <?php if ($rowProduto['vendido'] == 1) { ?> style="opacity:0.5;" <?php } ?> class="mt-4" src="<?= HOME; ?>/uploads/<?php echo $rowProduto['foto']; ?>"  height="130" /></div>
    <h5 class="mt-3" <?php if ($rowProduto['vendido'] == 1) { ?>style="text-decoration:line-through; "<?php } ?>>
<?php if ($Kit->getRowCount() > 0) { ?><span class="badge badge-success">KIT </span><?php } ?> <?php echo $rowProduto['nome']; ?></h5>
        <?php if ($rowProduto['caracteristica'] != "") { ?><p class="font-italic"><?php echo $rowProduto['caracteristica']; ?></p><?php } ?>

    <p>Código: <b><?php echo $rowProduto['idproduto']; ?></b> | 
<?php echo $rowProduto['quantidade']; ?> x <?php echo Check::Moeda($rowProduto['valor']); ?>
    </p>    


<?php
if ($Kit->getRowCount() > 0) {
    ?>
    <div style="margin-top:10px;" class="text-left">
            <ul class="produtos_kit">
    <?php
    foreach ($Kit->getResult() as $rowKit) {
        $total += $rowKit['valor_total'];
        ?>
                <li style="margin-top: 10px;">Código: <b><?php echo $rowKit['idproduto']; ?></b><br/>
                        <?php echo $rowKit['quantidade'] . " x " . $rowKit['nome'] . " > " . Check::Moeda($rowKit['valor']) . " | <u>" . Check::Moeda($rowKit['valor_total']) . "</u>"; ?>
                        <?php if ($rowKit['caracteristica'] != "") { ?><p class="font-italic"><?php echo $rowKit['caracteristica']; ?></p><?php } ?>
                    </li>
                        <?php
                    }
                    ?>
            </ul>
        </div>
<?php } ?>

    <h4 class="mt-3"><span class="badge badge-secondary">R$ <?php echo Check::Moeda($total); ?></span></h4>
    
    <!-- <p><a class="btn btn-danger" href="mesa.php?idmesa=<?php echo $rowMesa['idmesa']; ?>" role="button">Ver Produtos</a></p> -->
</div><!-- /.col-lg-4 -->

