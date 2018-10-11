<?php

/**
 * Check.class [ HELPER ]
 * Manupula e valida dados do sistema
 * @author Daniel Lima
 */
class Check {

    private static $Data;
    private static $Format;

    /** Valida E-mail */
    public static function Email($Email) {
        self::$Data = (string) $Email;
        self::$Format = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';

        if (preg_match(self::$Format, self::$Data)):
            return true;
        else:
            return false;
        endif;
    }

    //URL Amigavel
    public static function Name($Name) {
        self::$Format = array();
        self::$Format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        self::$Format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';

        self::$Data = strtr(utf8_decode($Name), utf8_decode(self::$Format['a']), self::$Format['b']);
        self::$Data = strip_tags(trim(self::$Data));
        self::$Data = str_replace(' ', '-', self::$Data);
        self::$Data = str_replace(array('--------', '-------', '------', '-----', '----', '---', '--'), '-', self::$Data);

        return strtolower(utf8_encode(self::$Data));
    }

    /*
     * Data to EN
     */

    public static function DataHora($Data) {
        self::$Format = explode(' ', $Data);
        self::$Data = explode('/', self::$Format[0]);

        if (empty(self::$Format[1])):
            self::$Format[1] = date('H:i:s');
        endif;

        self::$Data = self::$Data[2] . '-' . self::$Data[1] . '-' . self::$Data[0] . ' ' . self::$Format[1];
        return self::$Data;
    }

    /*
     * Data to EN
     */
    public static function Data($Data) {
        self::$Data = explode('/', self::$Format[0]);
        self::$Data = self::$Data[2] . '-' . self::$Data[1] . '-' . self::$Data[0];
        return self::$Data;
    }

    /*
     * Data to BR
     */

    public static function DataBR($Data) {
        self::$Format = explode(' ', $Data);
        self::$Data = explode('-', self::$Format[0]);

        if (empty(self::$Format[1])):
            self::$Format[1] = date('H:i:s');
        endif;

        self::$Data = self::$Data[2] . '/' . self::$Data[1] . '/' . self::$Data[0];
        return self::$Data;
    }

    public static function DataHoraBR($Data) {
        self::$Format = explode(' ', $Data);
        self::$Data = explode('-', self::$Format[0]);

        if (empty(self::$Format[1])):
            self::$Format[1] = date('H:i:s');
        endif;

        self::$Data = self::$Data[2] . '/' . self::$Data[1] . '/' . self::$Data[0] . ' ' . self::$Format[1];
        return self::$Data;
    }

    public static function DataDiaMes($Data) {
        self::$Format = explode(' ', $Data);
        self::$Data = explode('-', self::$Format[0]);

        self::$Data = self::$Data[2] . '/' . self::$Data[1];
        return self::$Data;
    }

    /*
     * Data Extenso
     */

    public static function DataExtenso($Data) {
        self::$Format = explode(' ', $Data);
        self::$Data = explode('-', self::$Format[0]);

        $mes = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

        return self::$Data[2] . " de " . $mes[intval(self::$Data[1])-1] . " de " . self::$Data[0];
    }

    /*
     * Limita Caracteres
     */

    public static function Words($String, $Limite, $Pointer = null) {
        self::$Data = strip_tags(trim($String));
        self::$Format = (int) $Limite;

        $ArrWords = explode(' ', self::$Data);
        $NumWords = count($ArrWords);
        $NewWords = implode(' ', array_slice($ArrWords, 0, self::$Format));

        $Pointer = (empty($Pointer) ? '...' : ' ' . $Pointer);
        $Result = (self::$Format < $NumWords ? $NewWords . $Pointer : $String);
        return $Result;
    }

    /*
     * Mostra ID da Categoria do Artigos por Nome
     */

    public static function CategoriaArtigoByName($CategoryName) {
        $read = new Read;
        $read->ExeRead('artigo_categoria', "WHERE categoria_titulo = :name", "name={$CategoryName}");
        if ($read->getRowCount()):
            return $read->getResult()[0]['idartigo_categoria'];
        else:
            echo "A categoria {$CategoryName} não foi encontrada";
            die;
        endif;
    }

    /*
     * Mostra Informacoes do site por ID
     */

    public static function SiteById($SiteID) {
        $read = new Read;
        $read->ExeRead('site', "WHERE idsite = :idsite", "idsite={$SiteID}");
        if ($read->getRowCount()):
            return $read->getResult()[0]['site'];
        else:
            echo "Site {$SiteID} não encontrado";
        endif;
    }

    /*
     * Usuários Online
     */

    public static function UserOnline() {
        $now = date('Y-m-d H:i:s');
        $deleteUserOnline = new Delete;
        $deleteUserOnline->ExeDelete('online', "WHERE online_endview < :now", "now={$now}");

        $readUserOnline = new Read;
        $readUserOnline->ExeRead('online');
        return $readUserOnline->getRowCount();
    }

    /**
     * Redimensiona Imagem
     * 
     * @param  ImageUrl = Caminho da Imagem
     * 
     */
    public static function Image($ImageUrl, $ImageDesc, $ImageW = null, $ImageH = null) {

        self::$Data = $ImageUrl;

        if (file_exists(self::$Data) && !is_dir(self::$Data)):
            $patch = HOME;
            $imagem = self::$Data;
            return "<img src=\"{$patch}/tim.php?src={$patch}/{$imagem}&w={$ImageW}&h={$ImageH}\" alt=\"{$ImageDesc}\" title=\"{$ImageDesc}\"/>";
        else:
            return false;
        endif;
    }
    
    
    /**
     * Retorna no formato moeda
     */
    public static function Moeda($Valor) {
        
        self::$Data = $Valor;
        return number_format(self::$Data, 2, ",", ".");
        
    }
    
    
    /**
     * Somente números
     */
    public static function SoNumeros($str) {
        return preg_replace("/[^0-9]/", "", $str);
    }

    public static function DomingosMes($Mes) {
        $ano = date("Y");

        $data = $ano . '-' . $Mes. '-01';

        $inicio = new DateTime($data);
        $fim = new DateTime($inicio->format('Y-m-t'));
        $dias = $inicio->diff($fim, true)->days;

        return intval($dias / 7) + ($inicio->format('N') + $dias % 7 >= 7);
    }

    
    public static function DomingosPeriodo($Inicio, $Fim) {
        $ano = date("Y");

        //$data = $ano . '-' . $Mes. '-01';

        $inicio = new DateTime($Inicio);
        $fim = new DateTime($Fim); //$inicio->format('Y-m-t'));
        $dias = $inicio->diff($fim, true)->days;

        return intval($dias / 7) + ($inicio->format('N') + $dias % 7 >= 7);
    }
    
}
