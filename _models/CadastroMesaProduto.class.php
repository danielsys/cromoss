<?php

class CadastroMesaProduto {

    private $Data;
    private $Post;
    private $Error;
    private $Result;

    //Table
    const Entity = 'mesa_produtos';
    const EntityKit = 'mesa_produtos_kit';

    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        //Guarda Campos não obrigatórios
        $Caracteristica = $this->Data['caracteristica'];
        unset($this->Data['caracteristica']);

        if (in_array('', $this->Data)) {
            $this->Error = ["Preencha todos os campos corretamente", WS_INFOR];
            $this->Result = false;
        } else {
            //Recupera Campos
            $this->Data['caracteristica'] = $Caracteristica;

            $this->setData();
            //$this->setName();

            if ($this->Data['foto']) {
                $upload = new Upload();
                $upload->Image($this->Data['foto'], $this->Data['nome']);
            }

            if (isset($upload) && $upload->getResult()) {
                $this->Data['foto'] = $upload->getResult();
                $this->Create();
            } else {
                $this->Data['foto'] = null;
                
                $this->Error = ["Upload erro", WS_INFOR];
                $this->Result = false;
                
                //echo "ERRO U P L O A D ! ! !";
                //$this->Create();
            }
        }
    }


    public function ExeCreateKit(array $Data) {
        $this->Data = $Data;

        //Guarda Campos não obrigatórios
        $Caracteristica = $this->Data['caracteristica'];
        
        unset($this->Data['caracteristica']);

        if (in_array('', $this->Data)) {
            $this->Error = ["Preencha todos os campos corretamente", WS_INFOR];
            $this->Result = false;
        } else {
            //Recupera Campos
            $this->Data['caracteristica'] = $Caracteristica;

            $this->setDataKit();

            $this->CreateKit();
        }
    }

    
    public function ExeDelete($IdMesaProdutosKit) {
        $this->Post = (int) $IdMesaProdutosKit;

        $ReadPost = new Read();
        $ReadPost->ExeRead(self::EntityKit, "WHERE idmesa_produtos_kit=:idpost", "idpost={$this->Post}");

        if (!$ReadPost->getResult()):
            $this->Error = ['O post que vocÃª tentou deletar nÃ£o existe', WS_ERROR];
        else:
            $deleta = new Delete();
            $deleta->ExeDelete(self::EntityKit, "WHERE idmesa_produtos_kit=:id_mesa_produtos_kit", "id_mesa_produtos_kit={$this->Post}");
            
            $this->Error = ["Produto removido com sucesso do Kit", WS_ACCEPT];
            $this->Result = true;
        endif;
    }
    
    private function setData() {
        $Foto = $this->Data['foto'];
        //$Caracteristica = $this->Data['caracteristica'];

        unset($this->Data['foto']);
        //unset($this->Data['caracteristica']);

        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);

        //$this->Data['data_encerramento'] = Check::Data($this->Data['data_encerramento']);

        $this->Data['foto'] = $Foto;
        //$this->Data['email'] = $Email;
    }

    private function setDataKit() {
        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);
    }

    function getResult() {
        return $this->Result;
    }

    function getError() {
        return $this->Error;
    }

    private function setName() {
        $Where = (isset($this->Post) ? "idpost != {$this->Post} AND " : "");

        $readName = new Read();
        $readName->ExeRead(self::Entity, "WHERE {$Where} post = :t", "t={$this->Data['post']}");

        if ($readName->getResult()):
            $this->Data['post'] = $this->Data['post'] . '-' . $readName->getRowCount();
        endif;
    }

    private function Create() {
        $cadastra = new Create();
        $cadastra->ExeCreate(self::Entity, $this->Data);
        if ($cadastra->getResult()):
            $this->Error = ["Post {$this->Data['nome']} cadastrado com sucesso", WS_ACCEPT];
            $this->Result = $cadastra->getResult();
        endif;
    }

    private function CreateKit() {
        $cadastra_kit = new Create();
        $cadastra_kit->ExeCreate(self::EntityKit, $this->Data);
        if ($cadastra_kit->getResult()):
            $this->Error = ["Post {$this->Data['nome']} cadastrado com sucesso", WS_ACCEPT];
            $this->Result = $cadastra_kit->getResult();
        endif;
    }
}
