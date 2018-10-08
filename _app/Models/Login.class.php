<?php

/**
 * Login.class [ MODEL ]
 * Responável por autenticar, validar, e checar usuário do sistema de login!
 * 
 * @author Daniel Lima IAMAZON
 */
class Login {

    private $Level;
    private $IdFuncionario;
    private $Senha;
    private $Error;
    private $Result;

    /**
     * <b>Informar Level:</b> Informe o nível de acesso mínimo para a área a ser protegida.
     * @param INT $Level = Nível mínimo para acesso
     */
    function __construct($Level) {
        $this->Level = (int) $Level;
    }

    /**
     * <b>Efetuar Login:</b> Envelope um array atribuitivo com índices STRING user [email], STRING pass.
     * Ao passar este array na ExeLogin() os dados são verificados e o login é feito!
     * @param ARRAY $UserData = user [email], pass
     */
    public function ExeLogin(array $UserData) {
        $this->IdFuncionario = (string) strip_tags(trim($UserData['idfuncionario']));
        $this->Senha = (string) strip_tags(trim($UserData['senha']));
        $this->setLogin();
    }

    /**
     * <b>Verificar Login:</b> Executando um getResult é possível verificar se foi ou não efetuado
     * o acesso com os dados.
     * @return BOOL $Var = true para login e false para erro
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com uma mensagem e um tipo de erro WS_.
     * @return ARRAY $Error = Array associatico com o erro
     */
    public function getError() {
        return $this->Error;
    }

    /**
     * <b>Checar Login:</b> Execute esse método para verificar a sessão USERLOGIN e revalidar o acesso
     * para proteger telas restritas.
     * @return BOLEAM $login = Retorna true ou mata a sessão e retorna false!
     */
    public function CheckLogin() {
        if (empty($_SESSION['userlogin']) /* || $_SESSION['userlogin']['users_level'] < $this->Level */):
            unset($_SESSION['userlogin']);
            return false;
        else:
            return true;
        endif;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */
    
    public function CheckLevel($nivel) {
      if ($_SESSION['userlogin']['users_level'] >= $nivel):
        return false;
      else:
        return true;
      endif;
    }
    
    //Valida os dados e armazena os erros caso existam. Executa o login!
    private function setLogin() {
        if (!$this->IdFuncionario || !$this->Senha /* || !Check::Email($this->Email) */ ):
            $this->Error = ['Informe seu Código e Senha para efetuar o login!', WS_INFOR];
            $this->Result = false;
        elseif (!$this->getUser()):
            $this->Error = ['Os dados informados não são compatíveis!', WS_ALERT];
            $this->Result = false;
        /* elseif ($this->Result['users_level'] < $this->Level):
            $this->Error = ["Desculpe {$this->Result['users_name']}, você não tem permissão para acessar esta área!", WS_ERROR];
            $this->Result = false; */
        else:
            $this->Execute();
        endif;
    }

    //Vetifica usuário e senha no banco de dados!
    private function getUser() {
        $this->Senha = $this->Senha;//md5($this->Senha);

        $read = new Read;
        $read->ExeRead("funcionario", "WHERE idfuncionario = :e AND senha = :p", "e={$this->IdFuncionario}&p={$this->Senha}");
        //$read->ExeRead("users", "WHERE users_email = :e AND users_password = :p", "e={$this->Email}&p={$this->Senha}");

        if ($read->getResult()):
            $this->Result = $read->getResult()[0];
            return true;
        else:
            return false;
        endif;
    }

    //Executa o login armazenando a sessão!
    private function Execute() {
        if (!session_id()):
            session_start();
        endif;

        $_SESSION['userlogin'] = $this->Result;

        $this->Error = ["Olá {$this->Result['apelido']}, seja bem vindo(a). Aguarde redirecionamento!", WS_ACCEPT];
        $this->Result = true;
    }

}
