<?php

namespace App\Helpers\mgcp;
include_once 'simple_html_dom.php';

class WebHelper
{
    public $cUrl;

    public function __construct()
    {
        $this->cUrl = curl_init();
        curl_setopt($this->cUrl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->cUrl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->cUrl, CURLOPT_VERBOSE, true);
        curl_setopt($this->cUrl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->cUrl, CURLOPT_RETURNTRANSFER, true);
    }

    public function __destruct()
    {
        if ($this->cUrl!=null)
        {
            curl_close($this->cUrl);
        }
    }

    /** 
     * Descarga un archivo de internet a una ruta especificada
     * 
     * @param string $ruta URL del archivo
     * @param string $destino Ruta completa donde se guardará el archivo
     * 
     * @return void
     */
    public function descargarArchivo($ruta, $destino)
    {
        file_put_contents($destino, $this->visitarUrl($ruta));
    }

    public function parseHtml($pagina)
    {
        return str_get_html($pagina);
    }

    /** 
     * @param string $url URL que se desea visitar
     */
    public function visitarUrl($url)
    {
        curl_setopt($this->cUrl, CURLOPT_URL, $url);
        curl_setopt($this->cUrl, CURLOPT_HEADER, 0);
        return curl_exec($this->cUrl);
    }

    public function enviarData($dataEnviar, $url)
    {
        curl_setopt($this->cUrl, CURLOPT_URL, $url);
        curl_setopt($this->cUrl, CURLOPT_POST, true);
        //curl_setopt($this->cUrl, CURLOPT_POSTFIELDS, http_build_query($dataEnviar));
        curl_setopt($this->cUrl, CURLOPT_POSTFIELDS,(is_string($dataEnviar) ? $dataEnviar : http_build_query($dataEnviar)));
        curl_setopt($this->cUrl, CURLOPT_HEADER, 0);
        return curl_exec($this->cUrl);
    }
}
