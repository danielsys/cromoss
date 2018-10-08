<?php

class CadastroMesa {

    private $Data;
    private $Post;
    private $Error;
    private $Result;

    //Table
    const Entity = 'mesa';

    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        //Guarda Campos não obrigatórios
        $Email = $this->Data['email'];
        $Pai = $this->Data['pai'];
        $Pai = $this->Data['pai'];

        unset($this->Data['email'], $this->Data['pai'], $this->Data['mae']);

        if (in_array('', $this->Data)) {
            $this->Error = ["Preencha todos os campos corretamente", WS_INFOR];
            $this->Result = false;
        } else {
            //Recupera Campos
            $this->Data['email'] = $Email;
            $this->Data['pai'] = $Pai;
            $this->Data['mae'] = $Mae;

            $this->setData();
            //$this->setName();

            if ($this->Data['foto']) {
                $upload = new Upload();
                $upload->Image($this->Data['foto'], $this->Data['titulo']);
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
    
    public function ExeUpdate($Id, array $Data) {
        $this->Post = (int) $Id;
        $this->Data = $Data;
        
        $Foto = $this->Data['foto'];
        $Email = $this->Data['email'];
        $Pai = $this->Data['pai'];
        $Mae = $this->Data['mae'];
        
        unset($this->Data['foto'], $this->Data['email'], $this->Data['pai'], $this->Data['mae']);
        
        if (in_array('', $this->Data)) {
            $this->Error = ["Para atualizar, preencha todos os campos corretamente", WS_ALERT];
            $this->Result = false;
        } else {
            
            $this->Data['foto'] = $Foto;
            $this->Data['email'] = $Email;
            $this->Data['pai'] = $Pai;
            $this->Data['mae'] = $Mae;
            
            $this->setData();
            
            if (is_array($this->Data['foto'])) {
                $readFoto = new Read;
                $readFoto->ExeRead("mesa", "WHERE idmesa=:idmesa", "idmesa={$this->Post}");
                $foto = UPLOAD_DIR . '/' . $readFoto->getResult()[0]['foto'];
                if (file_exists($foto) && !is_dir($foto)) {
                    unlink($foto);
                }
                
                $uploadFoto = new Upload();
                $uploadFoto->Image($this->Data['foto'], $this->Data['titulo']);
            }
            
            if (isset($uploadFoto) && $uploadFoto->getResult()) {
                $this->Data['foto'] = $uploadFoto->getResult();
                $this->Update();
            } else {
                unset($this->Data['foto']);
                $this->Update();
            }
        }
    }

    private function setData() {
        $Foto = $this->Data['foto'];
        //$Email = $this->Data['email'];

        unset($this->Data['foto']);
        //unset($this->Data['email']);

        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);

        //$this->Data['data_encerramento'] = Check::Data($this->Data['data_encerramento']);

        $this->Data['foto'] = $Foto;
        //$this->Data['email'] = $Email;
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
    
    private function Update() {
        $Update = new Update();
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE idmesa=:idmesa", "idmesa={$this->Post}");
        if ($Update->getResult()) {
            $this->Error = ["Mesa atualizado com sucesso", WS_ACCEPT];
            $this->Result = true;
        }
    }

}
