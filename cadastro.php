<?php 
    
    require '_app/Config.inc.php';
    require '_top.php'; 

    if (isset($_SESSION['cliente'])) {
        unset($_SESSION['cliente']);
    }
   
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    
    if (isset($post) && $post['SendPostForm']) {
        unset($post['SendPostForm']);
        $post['foto'] = ($_FILES['foto']['tmp_name'] ? $_FILES['foto'] : null);
        $post['idloja'] = LOJA; 
       

        require('_models/CadastroMesa.class.php');

        $cadastra = new CadastroMesa();
        $cadastra->ExeCreate($post);
        
        if ($cadastra->getResult()) {
            $Mesa = new Read();
            $Mesa->ExeRead("mesa", "WHERE idloja = " . LOJA . " ORDER BY idmesa DESC LIMIT 0,1 ");
            $rowMesa = $Mesa->getResult()[0];
            
            // Envia SMS
            $sms_fone = Check::SoNumeros($rowMesa['telefone']);
            $msg = "Ola, Muito obrigado por ter criado sua mesa de " . $rowMesa['tipo'] . ": " . $rowMesa['titulo'] . " na loja " . $FilialConfig[LOJA]['nome'] . ". Ficamos muito felizes.";
            $sms = file_get_contents("http://app.smsfast.com.br/api.ashx?action=sendsms&lgn=96981120634&pwd=509921&content=" . urlencode($msg) . "&numbers=" . $sms_fone . "&type_service=LONGCODE");
            
            
            header("Location:admin/mesa.php?action=cadastrada&idmesa=" . $rowMesa['idmesa']);
        } else {
            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
        }
    }
?>            

<script type="text/javascript">
    function SelecionaCadastro(idcadastro) {
        $.getJSON("<?php echo HOME; ?>/ajax/cadastro.php?id="+idcadastro, function(data) {
            $("#nome").val(data.Nome);
        });
    }
</script>

<div class="container">
    <div class="py-5 text-center">
        <!--<img class="d-block mx-auto mb-4" src="../../assets/brand/bootstrap-solid.svg" alt="" width="72" height="72"> -->
        <h2>Cadastrar Mesa</h2>
        <p class="lead">Preencha todos os campos corretamente.</p>
    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8 order-md-1">

            <form class="needs-validation" action="" name="PostForm" enctype="multipart/form-data" method="POST" novalidate>

                <h4 class="mb-3">Dados Pessoais</h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="idcadastro">Código</label>
                        <input type="text" class="form-control" id="idcadastro" onblur="SelecionaCadastro(this.value);" name="idcadastro" placeholder="Código de Cliente" value="<?php if (isset($post)) { echo $post['idcadastro']; } ?>" required>
                        <div class="invalid-feedback">
                            Este campo é de preenchimento obrigatório.
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nome">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" readonly placeholder="Nome do Responsável" value="<?php if (isset($post)) { echo $post['nome']; } ?>" required>
                        <div class="invalid-feedback">
                            Este campo é de preenchimento obrigatório.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefone">Telefone</label>
                        <input type="text" class="form-control fone" id="telefone" name="telefone" placeholder="" value="<?php if (isset($post)) { echo $post['telefone']; } ?>" required>
                        <div class="invalid-feedback">
                            Este campo é de preenchimento obrigatório.
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php if (isset($post)) { echo $post['email']; } ?>" placeholder="seu@email.com">
                </div>

                <div class="mb-3">
                    <label for="vendedor">Vendedor(a)</label>
                    <input type="vendedor" class="form-control" id="vendedor" name="vendedor" value="<?php if (isset($post)) { echo $post['vendedor']; } ?>" placeholder="Nome do Vendedor responsável pela mesa">
                    <div class="invalid-feedback">
                        Este campo é de preenchimento obrigatório.
                    </div>
                </div>
                
                <hr class="mb-4">
                
                <h4 class="mb-3">Dados da Mesa</h4>

                <div class="mb-3">
                    <label for="titulo">Nome da Mesa</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?php if (isset($post)) { echo $post['titulo']; } ?>" placeholder="Nome da Pessoa ou Casal" required>
                    <small class="text-muted">Aqui vai ficar o nome da pessoa ou casal. Exemplo: Priscila Moura, Alessandro &AMP; Bianca</small>
                    <div class="invalid-feedback">
                        Este campo é de preenchimento obrigatório.
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="pai">Pai</label>
                        <input type="text" class="form-control" id="pai" name="pai" placeholder="Nome do Pai" value="<?php if (isset($post)) { echo $post['pai']; } ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="mae">Mãe</label>
                        <input type="text" class="form-control" id="mae" name="mae" placeholder="Nome da Mãe" value="<?php if (isset($post)) { echo $post['mae']; } ?>">
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label for="tipo">Tipo</label>
                        <select class="custom-select d-block w-100" id="tipo" name="tipo" required>
                            <option value="">Escolha</option>
                            <option value="Aniversário" <?php if (isset($post)) { if ($post['tipo']=='Aniversário') { echo " selected "; } } ?>>Aniversário</option>
                            <option value="Casamento" <?php if (isset($post)) { if ($post['tipo']=='Casamento') { echo " selected "; } } ?>>Casamento</option>
                            <option value="Chá de Casa Nova" <?php if (isset($post)) { if ($post['tipo']=='Chá de Casa Nova') { echo " selected "; } } ?>>Chá de Casa Nova</option>
                            <option value="Baby Chá" <?php if (isset($post)) { if ($post['tipo']=='Baby Chá') { echo " selected "; } } ?>>Baby Chá</option>
                        </select>
                        <div class="invalid-feedback">
                            Escolha o tipo.
                        </div>
                    </div>

                    <div class="col-md-7">
                        <label for="data_encerramento">Data do Evento</label>
                        <input type="date" class="form-control" id="data_encerramento" name="data_encerramento" value="<?php if (isset($post)) { echo $post['data_encerramento']; } ?>" required="">
                        <div class="invalid-feedback">
                            É necessário especificar a data.
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

                <!--<hr class="mb-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="same-address">
                    <label class="custom-control-label" for="same-address">Deseja receber contatos futuros</label>
                </div> -->
                <hr class="mb-4">

                <hr class="mb-4">
                
                <button class="btn btn-primary btn-lg btn-block" name="SendPostForm" value="SendPostForm" type="submit">Finalizar Cadastro</button>
                
            </form>
        </div>
    </div>
</div>
    
    
    <script>
      // Example starter JavaScript for disabling form submissions if there are invalid fields
      (function() {
        'use strict';

        window.addEventListener('load', function() {
          // Fetch all the forms we want to apply custom Bootstrap validation styles to
          var forms = document.getElementsByClassName('needs-validation');

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
        }, false);
      })();
    </script>
<?php require '_bottom.php'; ?>            