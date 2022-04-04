<?php

namespace App\Repositories;

class SaveAsCSVRepository implements ISaveXMLDataRepository
{

    public function saveXmlData($data)
    {
        $fileName = time() . '.csv';
        $columns = array_keys($data[0]);
        $filePath = storage_path() . '/'. $fileName;
        $fileResource = fopen($filePath, 'w');
        fputcsv($fileResource, $columns);
        foreach ($data as $xmlDatum) {
            fputcsv($fileResource, $xmlDatum);
        }
        fclose($fileResource);

        return $filePath;
    }
}
