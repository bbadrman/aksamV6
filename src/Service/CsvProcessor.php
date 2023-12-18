<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvProcessor
{
    public function processCsv(UploadedFile $csvFile): array
    {
        $csvFilePath = $csvFile->getPathname();
        $csvData = [];
        if (($handle = fopen($csvFilePath, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $csvData[] = $data;
            }
            fclose($handle);
        }
        return $csvData;
    }
}
