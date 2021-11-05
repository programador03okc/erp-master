<?php

namespace App\Helpers\mgcp;

use CloudConvert\CloudConvert;
use CloudConvert\Models\Job;
use CloudConvert\Models\Task;

class CloudConvertHelper
{
    private static $token=' eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiODRlOTEwNjk0MmNkMGNmM2NmN2UyOWUwOTIyN2RiMzlmNzc0OTM4MzE3M2Q5ODVlMjVjYjk2NjBmZGRkYjE5OTg4NWQ3MGEzNTZkMWE1ZWQiLCJpYXQiOiIxNjA4MjU1Nzk3LjY0OTg2MSIsIm5iZiI6IjE2MDgyNTU3OTcuNjQ5ODY0IiwiZXhwIjoiNDc2MzkyOTM5Ny42MTI5OTIiLCJzdWIiOiI0NzY4OTk5NyIsInNjb3BlcyI6WyJwcmVzZXQud3JpdGUiLCJwcmVzZXQucmVhZCIsIndlYmhvb2sud3JpdGUiLCJ3ZWJob29rLnJlYWQiLCJ0YXNrLndyaXRlIiwidGFzay5yZWFkIl19.B3dLXm-EzHM6aWn1HTLWQPfgY5jcAw_czN0ODZXK61j6CEw4xbgxpYGP9xgybA8BNExOP94RNAxDqXOYFw84nUQpHxw4yymk9LlHmnnd4iHATl_QvcfHxbci9ZjVXjlfWYKVuIrxxQaPHnj-bQFmDkbH40Siybf-TJJhZP9WtufpfbXfzXHMthBCQCkA4bHlfI0hBgJwO9fB3J8Vc0E6dLShGONVg_rCToPCZJf-10epb0EzEmThpl9b2UfV1loeWTzTj-PxijvvehYnVV713MQl_xgNFm1fd2RjNnNYLeFnchzAajH9BOXGRu-pt3j97lllJ_ucCItsMEvRWiNXzaKlV7ZBjk49Q21pFeIDFxtuRVAAHIX79bYxxUvDRlz56gCW-Nb1Qbch0BQOlmKSZ1DY5dMv0j0XA5o1JGRfOcBRdC2JbH19LX1k6sB_7NAEl8c2coXIhsCc3z0KfKgOemLV3ZCK2XxSEpau6atAxlXAwctA91ez7Erjuf0Tr2Ei5GYPS852q6PoPNll0Da8UofnMYwCZNfma7Cgj_33EtPPVV6reT0V5epjt2gSwYLblJCbllLZt-zfym0_Odwq_Mg6Is0AqGjQcpmzs3roaytQUPUxZQWE3gYUUmkWiVLYcmpbG_NC2OLPVPUKmmvV7Xt2S58xKvVG4PuItZyWeYw';

    public static function convertir($rutaArchivoConvertir,$formatoConversion,$rutaArchivoConvertido)
    {
        $cloudconvert = new CloudConvert([
            'api_key' => CloudConvertHelper::$token,
            'sandbox' => false
        ]);
        $job = (new Job())
            ->addTask(new Task('import/upload', 'import-1'))
            ->addTask(
                (new Task('convert', 'task-1'))
                    //->set('input_format', 'pdf')
                    ->set('input', 'import-1')
                    ->set('output_format', $formatoConversion)
            )
            ->addTask(
                (new Task('export/url', 'export-1'))
                    ->set('input', 'task-1')
            );

        $cloudconvert->jobs()->create($job);

        $uploadTask = $job->getTasks()->whereName('import-1')[0];

        $cloudconvert->tasks()->upload($uploadTask, fopen($rutaArchivoConvertir, 'r'));

        $cloudconvert->jobs()->wait($job); // Wait for job completion

        foreach ($job->getExportUrls() as $file) {
            $source = $cloudconvert->getHttpTransport()->download($file->url)->detach();
            $dest = fopen($rutaArchivoConvertido, 'w');
            stream_copy_to_stream($source, $dest);
        }
        ArchivoHelper::eliminarArchivo($rutaArchivoConvertir);
    }
}