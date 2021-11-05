<?php

namespace App\Helpers\mgcp;

class PeruComprasHelper extends WebHelper
{

    private $rutaCookie;
    public $token;
    public $sesionIniciada;

    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
        $this->finalizar();
    }

    public function login($empresa, $cuenta, $recordarToken = true)
    {
        $this->rutaCookie = public_path() . '/mgcp/cookies/' . substr(md5(rand()), 0, 14) . '.txt';
        $fopen = fopen($this->rutaCookie, 'w');
        curl_setopt($this->cUrl, CURLOPT_COOKIEJAR, $this->rutaCookie);
        curl_setopt($this->cUrl, CURLOPT_COOKIEFILE, $this->rutaCookie);
        curl_setopt($this->cUrl, CURLOPT_URL, 'https://www.catalogos.perucompras.gob.pe/AccesoGeneral');
        fclose($fopen);

        $pagina_inicial = str_get_html(curl_exec($this->cUrl));
        $dataEnviar = "";
        switch ($cuenta) {
            case 1:
                $dataEnviar = "ID_Usuario=$empresa->ruc&Contrasena=$empresa->password";
                break;
            case 2:
                $dataEnviar = "ID_Usuario=$empresa->usuario2&Contrasena=$empresa->password2";
                break;
            case 3:
                $dataEnviar = "ID_Usuario=$empresa->usuario3&Contrasena=$empresa->password3";
                break;
        }

        $contenedorToken = $pagina_inicial->find('input[name=__RequestVerificationToken]', 0);
        if ($contenedorToken == null) {
            return false;
        }
        $dataEnviar .= "&__RequestVerificationToken=$contenedorToken->value&btnLogin=";
        $pagina_login = str_get_html($this->enviarData($dataEnviar, 'https://www.catalogos.perucompras.gob.pe/AccesoGeneral'));

        if ($pagina_login->find('ul[id=form-errors] li', 0)) {
            return false;
        } else {
            $this->sesionIniciada = true;
            if ($recordarToken) {
                $this->token = $pagina_login->find('input[name=__RequestVerificationToken]', 0)->value;
            }
            return true;
        }
    }

    public function finalizar()
    {
        $this->sesionIniciada = false;
        if ($this->rutaCookie != null && file_exists($this->rutaCookie)) {
            unlink($this->rutaCookie);
        }
    }
}
