<?php

    require '../_app/config.inc.php';
    require '../_top.php';

    $idmesa = filter_input(INPUT_GET, 'idmesa', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_GET, 'action');

    if ($idmesa=='') { header("Location:index.php"); }
    
    
    //Deleta Produto
    if ($action == 'deleta_produto') {
        $idmesa_produtos = filter_input(INPUT_GET, 'idmesa_produtos');
        
        $Dados['lixeira'] = 1;
        
        $Atualiza = new Update();
        $Atualiza->ExeUpdate("mesa_produtos", $Dados, "WHERE idmesa_produtos = :idmesa_produtos", "idmesa_produtos={$idmesa_produtos}");
        
        $aviso = "Produto enviado para a lixeira";
    }


    if ($action == 'deleta_kit') {
        $idmesa_produtos_kit = filter_input(INPUT_GET, 'idmesa_produtos_kit');
        
        require('../_models/CadastroMesaProduto.class.php');
        
        $Deleta = new CadastroMesaProduto;
        $Deleta->ExeDelete($idmesa_produtos_kit);
        
        $aviso = "Produto exclído do Kit";
    }
    
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    
    
    /*
     * Cadastra Produto
     */
    if (isset($post) && isset($post['SendPostForm'])) {
        unset($post['SendPostForm']);
        $post['foto'] = ($_FILES['foto']['tmp_name'] ? $_FILES['foto'] : null);
        $post['idmesa'] = $idmesa;
        
        require('../_models/CadastroMesaProduto.class.php');
        
        $cadastra = new CadastroMesaProduto();
        $cadastra->ExeCreate($post);
        
        if ($cadastra->getResult()) {
            $aviso = "Produto cadastrado com sucesso";
        } else {
            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
        }
    }
    
    
    /* 
     * Cadastra Kit Produto
     */
    if (isset($post) && isset($post['Kit_SendPostForm'])) {
        unset($post['Kit_SendPostForm']);

        require('../_models/CadastroMesaProduto.class.php');
        
        $cadastra_kit = new CadastroMesaProduto();
        $cadastra_kit->ExeCreateKit($post);
        
        if ($cadastra_kit->getResult()) {
            $aviso = "Produto adicionado ao Kit";
        } else {
            WSErro($cadastra_kit->getError()[0], $cadastra_kit->getError()[1]);
        }
    }


    
    $Mesa = new Read();
    $Mesa->ExeRead("mesa", "WHERE idmesa = :idmesa", "idmesa={$idmesa}");
    $rowMesa = $Mesa->getResult()[0];
    
?>

<script type="text/javascript">
    
    var mesa = <?php echo $idmesa; ?>
    
    function Calcula() {
        var quantidade = $("#quantidade").val();
        var preco = $("#valor").val();
        $("#valor_total").val(quantidade * preco);
    }
    
    function CalculaKit() {
        var kit_quantidade = $("#kit_quantidade").val();
        var kit_preco = $("#kit_valor").val();
        $("#kit_valor_total").val(kit_quantidade * kit_preco);
    }

    function SelecionaProduto(idproduto) {
        $.getJSON("<?php echo HOME; ?>/ajax/produto.php?id="+idproduto, function(data) {
            $("#nome").val(data.Nome);
            $("#valor").val(data.Preco);
            Calcula();
        });
    }
    
    function SelecionaProdutoKit(idproduto) {
        $.getJSON("<?php echo HOME; ?>/ajax/produto.php?id="+idproduto, function(data) {
            $("#kit_nome").val(data.Nome);
            $("#kit_valor").val(data.Preco);
            CalculaKit();
        });
    }

    function RemoveProduto(idproduto) {
        if (confirm("Você deseja realmente excluir este produto?")) {        
            location.href="mesa.php?idmesa=" + mesa + "&action=deleta_produto&idmesa_produtos=" + idproduto;
        }
    }
    
    function AdicionaKit(idproduto) {
        $('#addkit').modal('show');
        $("#idmesa_produtos").val(idproduto);
    }
    
    function DeletaKit(idproduto) {
        if (confirm("Você deseja excluir este produto do Kit?")) {
            location.href="mesa.php?idmesa=" + mesa + "&action=deleta_kit&idmesa_produtos_kit=" + idproduto;
        }
    }
</script>

<div class="shadow-lg" style="background:#f4f4f4;">
<div class="container">
    


            <?php
             if ($action == "cadastrada") {
                echo '<div class="mt-5 alert alert-success" role="alert">
                         Mesa cadastrada com sucesso!
                      </div>';
             }
            ?>
            <?php
             if (isset($aviso)) {
                echo '<div class="mt-5 alert alert-success" role="alert">
                         ' . $aviso . '
                      </div>';
             }
            ?>

    <div class="row pt-5 pb-5">
        <div class="col text-center">    

            <div class="rounded-circle" style="width:150px; margin: 0px auto 20px; height: 150px; background: #777; background:url(<?= HOME . '/uploads/'; ?><?php echo $rowMesa['foto']; ?>); background-size: 110%; background-position: 100%;"></div>
            <h2><?php echo $rowMesa['titulo']; ?></h2>
            <p class="lead"><?php echo $rowMesa['tipo']; ?><br /><?php echo Check::DataExtenso($rowMesa['data_encerramento']); ?></p>
        </div>
        <div class="col">
            <h4>Informações da Mesa</h4>
            <p>Loja: <?php echo $rowMesa['idloja']; ?> - Código: <?php echo $rowMesa['idmesa']; ?><br />
            Vendedor(a): <?php echo $rowMesa['vendedor']; ?></p>
            <p>Responsável: <?php echo $rowMesa['nome']; ?>
                <br>Telefone: <?php echo $rowMesa['telefone']; ?>
                <br>E-mail: <?php echo $rowMesa['email']; ?></p>
            <p>
            <?php if ($rowMesa['pai'] != '') { ?>Pai: <?php echo $rowMesa['pai']; ?><?php } ?><br>
            <?php if ($rowMesa['mae'] != '') { ?>Mãe: <?php echo $rowMesa['mae']; ?><?php } ?>
            </p>
            
            <p><a href="mesa_editar.php?idmesa=<?php echo $rowMesa['idmesa']; ?>">Editar Dados</a></p>
        </div>
    </div>

</div>
</div>
<div class="container mt-5">
    <div class="row">
        <div class="col">
            <h3>Produtos</h3>
        </div>
        <div class="col text-right">
            <a class="btn btn-primary" href="#collapseProduto" data-toggle="collapse"  aria-expanded="false" aria-controls="collapseProduto" role="button">Adicionar +</a>
        </div>
    </div>
    
    <div class="collapse" id="collapseProduto"  style="background:#ccc; margin:0px; padding:20px;">
        
        <form class="needs-validation" action="mesa.php?idmesa=<?php echo $idmesa; ?>" name="PostForm" enctype="multipart/form-data" method="POST" novalidate>
            <div class="text-center">
                <h4>Cadastrar Produto</h4>
            </div>
            
            <div class="row">
                <div class="col-sm-3">
                    <label for="quantidade">Quantidade</label>
                    <select class="custom-select d-block w-100" id="quantidade" onchange="Calcula();" name="quantidade" required>
                            <option value="">Escolha</option>
                            <?php for ($i=1;$i<=20;$i++) { ?>
                            <option value="<?php echo $i; ?>" <?php //if (isset($post)) { if ($post['tipo']=='Aniversário') { echo " selected "; } } ?>><?php echo $i; ?></option>
                            <?php } ?>
                    </select>
                    <div class="invalid-feedback">
                        Este campo é de preenchimento obrigatório.
                    </div>
                </div>
                
                <div class="col-sm-3">
                    <label for="idproduto">Código</label>
                    <input type="text" class="form-control" onblur="SelecionaProduto(this.value);" id="idproduto" name="idproduto" placeholder="" required>
                    <div class="invalid-feedback">
                        Este campo é de preenchimento obrigatório.
                    </div>
                </div>

                <div class="col">
                    <label for="nome">Nome</label>
                    <input type="text" class="form-control" id="nome" readonly name="nome" placeholder="" required>
                    <div class="invalid-feedback">
                        Este campo é de preenchimento obrigatório.
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-sm-3">
                   <label for="valor">Preço Unitário</label>
                    <input type="text" class="form-control f-money" id="valor" name="valor"  placeholder="" readonly required>
                    <div class="invalid-feedback">
                        Este campo é de preenchimento obrigatório.
                    </div>
                </div>
                
                <div class="col-sm-3">
                    <label for="valor_total">Total</label>
                    <input type="text" class="form-control f-money" id="valor_total" name="valor_total" readonly placeholder="" required>
                    <div class="invalid-feedback">
                        Este campo é de preenchimento obrigatório.
                    </div>
                </div>

                <div class="col">
                    <label for="caracteristica">Característica</label>
                    <input type="text" class="form-control" id="caracteristica" name="caracteristica" placeholder="Cor / Modelo / Tamanho">
                    <small class="text-muted">Cor / Modelo / Tamanho</small>
                    <div class="invalid-feedback">
                        Este campo é de preenchimento obrigatório.
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                    <label for="foto">Foto</label>
                    <input type="file" class="form-control" id="foto" name="foto" required>
                    <div class="invalid-feedback">
                        Este campo é de preenchimento obrigatório.
                    </div>
            </div>
            
            <hr class="mb-4">
                
            <button class="btn btn-primary btn-lg btn-block" name="SendPostForm" value="SendPostForm" type="submit">Adicionar Produto</button>
        </form>
        
    </div>
    
    
    
    
    
    <!--
        ### MODAL WINDOW
    -->

    <div class="modal fade bd-example-modal-lg" id="addkit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Adicionar Kit</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              
                <!-- FORM MODAL -->
                <form class="needs-validation-kit" action="mesa.php?action=kit&idmesa=<?php echo $idmesa; ?>" name="PostFormKit" method="POST" novalidate>
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="kit_quantidade">Quantidade</label>
                            <select class="custom-select d-block w-100" id="kit_quantidade" onchange="CalculaKit();" name="quantidade" required>
                                    <option value="">Escolha</option>
                                    <?php for ($i=1;$i<=20;$i++) { ?>
                                    <option value="<?php echo $i; ?>" <?php //if (isset($post)) { if ($post['tipo']=='Aniversário') { echo " selected "; } } ?>><?php echo $i; ?></option>
                                    <?php } ?>
                            </select>
                            <div class="invalid-feedback">
                                Este campo é de preenchimento obrigatório.
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <label for="kit_idproduto">Código</label>
                            <input type="text" class="form-control" onblur="SelecionaProdutoKit(this.value);" id="kit_idproduto" name="idproduto" placeholder="" required>
                            <div class="invalid-feedback">
                                Este campo é de preenchimento obrigatório.
                            </div>
                        </div>

                        <div class="col">
                            <label for="kit_nome">Nome</label>
                            <input type="text" class="form-control" id="kit_nome" readonly name="nome" placeholder="" required>
                            <div class="invalid-feedback">
                                Este campo é de preenchimento obrigatório.
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-3">
                           <label for="kit_valor">Preço Unitário</label>
                            <input type="text" class="form-control f-money" id="kit_valor" name="valor"  placeholder="" required>
                            <div class="invalid-feedback">
                                Este campo é de preenchimento obrigatório.
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <label for="kit_valor_total">Total</label>
                            <input type="text" class="form-control f-money" id="kit_valor_total" name="valor_total" readonly placeholder="" required>
                            <div class="invalid-feedback">
                                Este campo é de preenchimento obrigatório.
                            </div>
                        </div>

                        <div class="col">
                            <label for="kit_caracteristica">Característica</label>
                            <input type="text" class="form-control" id="kit_caracteristica" name="caracteristica" placeholder="Cor / Modelo / Tamanho">
                            <small class="text-muted">Cor / Modelo / Tamanho</small>
                            <div class="invalid-feedback">
                                Este campo é de preenchimento obrigatório.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                       
                        <div class="col">
                            <!--<label for="idmesa_produtos">#Ref</label>-->
                            <input type="hidden" id="idmesa_produtos" readonly name="idmesa_produtos" required>
                        </div>
                        
                    </div>

                    <hr class="mb-4">

                    <button class="btn btn-primary btn-lg btn-block" name="Kit_SendPostForm" value="Kit_SendPostForm" type="submit">Adicionar Produto ao Kit</button>
                </form>
                
            </div>
            <!--<div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Send message</button>
            </div> -->
          </div>
      </div>
    </div>    
    
    <hr class="mb-4">

    <div class="container">

        <?php
        $Produtos = new Read();
        $Produtos->ExeRead("mesa_produtos", "WHERE idmesa = :idmesa AND lixeira=0 ORDER BY idmesa_produtos DESC", "idmesa={$idmesa}");

        $i = 0;

        foreach ($Produtos->getResult() as $rowProdutos) {
            $i++;
            $idmesa_produtos = $rowProdutos['idmesa_produtos'];
            $total = $rowProdutos['valor_total'];
            ?>

            <div class="row" style="padding:20px 0px 20px 0px; border-bottom:1px solid #ccc; <?php if ($i % 2) {
            echo 'background:#e4e4e4;';
        } ?>">
                <div class="col-sm-2">
                    <img src="../uploads/<?php echo $rowProdutos['foto']; ?>" width="100%" />
                </div>
                <div class="col">
                    <b><?php echo $rowProdutos['nome']; ?></b>
                    <p class="font-italic"><?php echo $rowProdutos['caracteristica']; ?></p>
                    <span class="text-muted"><?php echo $rowProdutos['quantidade']; ?> x <?php echo Check::Moeda($rowProdutos['valor']); ?></span><br>

                    <?php
                    $Kit = new Read();
                    $Kit->ExeRead("mesa_produtos_kit", "WHERE idmesa_produtos = :idmesa_produtos", "idmesa_produtos={$idmesa_produtos}");
                    if ($Kit->getRowCount() > 0) {
                        ?>
                        <div style="margin-top:10px;">
                            <h6>Kit +</h6>
                            <ul>
                                <?php
                                foreach ($Kit->getResult() as $rowKit) {
                                    $total += $rowKit['valor'];
                                    ?>
                                    <li>
                                        <?php echo $rowKit['quantidade'] . " x " . $rowKit['nome'] . " de " . Check::Moeda($rowKit['valor']) . " | <u>" . Check::Moeda($rowKit['valor_total']) . "</u>"; ?> (<a onclick="DeletaKit(<?php echo $rowKit['idmesa_produtos_kit']; ?>);" href="javascript:void(0);">Remover</a>)
                                        <?php if ($rowKit['caracteristica'] != "") { ?><p class="font-italic"><?php echo $rowKit['caracteristica']; ?></p><?php } ?>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
    <?php } ?>

                    <b>R$ <?php echo Check::Moeda($total); ?></b>
                </div>
                <div class="col-sm-3 text-right">
                    <button type="button" onclick="AdicionaKit(<?php echo $rowProdutos['idmesa_produtos']; ?>);" class="btn btn-dark btn-sm">Adicionar Kit</button>
                    <button type="button" onclick="RemoveProduto(<?php echo $rowProdutos['idmesa_produtos']; ?>);" class="btn btn-danger btn-sm">Remover</button>
                </div>
            </div>
            <?php
        } //foreach
        ?>

    </div>
</div>
 <script>
      // Example starter JavaScript for disabling form submissions if there are invalid fields
      (function() {
        'use strict';

        window.addEventListener('load', function() {
          // Fetch all the forms we want to apply custom Bootstrap validation styles to
          var forms = document.getElementsByClassName('needs-validation');
          var formskit = document.getElementsByClassName('needs-validation-kit');
          
          // Loop over them and prevent submission
          var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
              if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
              }
              form.classList.add('was-validated');
            }, false);
          });

          var validationkit = Array.prototype.filter.call(formskit, function(form) {
            form.addEventListener('submit', function(event) {
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
<?php require '../_bottom.php'; ?>