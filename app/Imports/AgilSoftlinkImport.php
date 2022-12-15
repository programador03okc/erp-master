<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class AgilSoftlinkImport implements ToCollection
{

    public function collection(Collection $collection)
    {
        //
        // dd($collection);exit;
        return $collection;
    }
    // public function model(array $row)
    // {
    //     return $row;
    // }

    // public function batchSize(): int
    // {
    //     return 1000;
    // }
}
