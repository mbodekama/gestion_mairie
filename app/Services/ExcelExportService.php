<?php

namespace App\Services;

use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelExportService
{
    public function telecharger(string $nomFichier, callable $ecrire): BinaryFileResponse
    {
        $cheminTemp = tempnam(sys_get_temp_dir(), 'xlsx_') . '.xlsx';

        $writer = new Writer();
        $writer->openToFile($cheminTemp);
        $ecrire($writer);
        $writer->close();

        return response()->download(
            $cheminTemp,
            $nomFichier . '_' . now()->format('Ymd_His') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        )->deleteFileAfterSend(true);
    }
}
