<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvProcessor
{
    public function processCsv(UploadedFile $csvFile): array
    {
        $csvFilePath = $csvFile->getPathname();
        $csvData = [];
        $firstLine = true; // Variable pour suivre la première ligne

        if (($handle = fopen($csvFilePath, 'r')) !== false) {
            while (($line = fgets($handle)) !== false) {
                // Si c'est la première ligne, passer à la suivante
                if ($firstLine) {
                    $firstLine = false;
                    continue;
                }

                // Diviser chaque ligne en utilisant un point-virgule (;)
                $data = explode(';', $line);

                // Supprimer les espaces autour de chaque élément
                $data = array_map('trim', $data);

                // Ajouter les données à la liste
                $csvData[] = $data;
            }
            fclose($handle);
        }
        return $csvData;
    }
}
