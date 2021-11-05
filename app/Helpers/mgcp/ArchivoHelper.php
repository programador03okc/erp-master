<?php

namespace App\Helpers\mgcp;

class ArchivoHelper
{

    public static function limpiarNombre($nombre)
    {
        $nombre = str_replace(' ', '-', $nombre);
        $nombre = preg_replace('/[^A-Za-z0-9.\-]/', '', $nombre);
        return preg_replace('/-+/', '-', $nombre);
    }

    public static function convertirPdfAHtml($rutaArchivo, $rutaConversion)
    {
        ArchivoHelper::eliminarCarpeta($rutaConversion);
        $cmd = app_path() . "\mgcp\Helpers\Ejecutables\PdfToHtml\pdftohtml $rutaArchivo $rutaConversion";
        exec($cmd, $out, $ret);
        return $ret;
    }

    public static function eliminarCarpeta($ruta)
    {
        if (is_dir($ruta)) {
            $filelist = glob($ruta . '\*');
            foreach ($filelist as $myfiles) {
                if (is_file($myfiles)) {
                    unlink($myfiles);
                }
            }
            rmdir($ruta);
        }
    }

    public static function eliminarArchivo($ruta)
    {
        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }
}
