<?php
    require '../_app/config.inc.php';
    require '../_top.php'; 
    
    if (isset($_SESSION['cliente'])) { unset($_SESSION['cliente']); } 
?>            

      <div class="py-5 text-center">
        <h2>Gerenciamento das Mesas</h2>
        <p class="lead">Escolha uma para fazer edição.</p>
      </div>

            <!-- Marketing messaging and featurettes
            ================================================== -->
            <!-- Wrap the rest of the page in another container to center all the content. -->

            <div class="container marketing">
                


            <?php if ($is_login) { ?>
                    <form class="form-inline mt-2 mt-md-0" action="<?php echo HOME; ?>/admin/index.php" method="GET">
                        <input class="form-control mr-sm-2" type="text" name="nome" placeholder="Nome da Pessoa" aria-label="Search">
                        <button class="btn btn-outline-success my-2 my-sm-0" name="Search" value="Search" type="submit">Buscar</button>
                    </form>
            <?php } ?>

                
                

                <div class="row">

                    
                    
                <?php
                
                $get = filter_input_array(INPUT_GET, FILTER_DEFAULT);
                $sql = "";
                if (isset($get) && $get['Search']) {
                    $sql = " AND titulo LIKE '%" . $get['nome'] . "%' or pai LIKE '%" . $get['nome'] . "%' or mae LIKE '%" . $get['nome'] . "%' ";
                }
                
                $Mesa = new Read();
                $Mesa->FullRead("SELECT * FROM mesa WHERE 1=1 {$sql} AND idloja=" . LOJA . " ORDER BY data_encerramento DESC LIMIT 0, 200 ");

                $i = 0;

                foreach ($Mesa->getResult() as $rowMesa) {


                    if ($i == 0) {
                        //echo '<div class="row">';
                    }
                ?>


                    <div class="col-sm-4 text-center mt-5">
                        <div onclick="location.href='mesa.php?idmesa=<?php echo $rowMesa['idmesa']; ?>';" class="rounded-circle" style="width:150px; margin: 0px auto; height: 150px; background:url(<?= HOME; ?>/uploads/<?php echo $rowMesa['foto']; ?>); background-size: 100%; background-position: 100%;"></div>
                        <h2><?php echo $rowMesa['titulo']; ?></h2>
                        <p>
                            <?php echo $rowMesa['tipo']; ?>
                            <?php
                                switch ($rowMesa['tipo']) {
                                    case 'Aniversário':
                                        if ($rowMesa['mae'] != '') { echo '<br>Mãe: ' . $rowMesa['mae']; }
                                        if ($rowMesa['pai'] != '') { echo '<br>Pai: ' . $rowMesa['pai']; }
                                        break;
                                }
                            ?>
                            <br>Data: <?php echo Check::DataDiaMes($rowMesa['data_encerramento']); ?>
                        </p>    
                        <p><a class="btn btn-danger" href="mesa.php?idmesa=<?php echo $rowMesa['idmesa']; ?>" role="button">Abrir</a></p>
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

<?php require '../_bottom.php'; ?>            