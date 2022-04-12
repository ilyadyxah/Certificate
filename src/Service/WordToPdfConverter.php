<?php

namespace App\Service;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class WordToPdfConverter implements ConverterInterface
{
    private string $pathInputFile;
    private string $pathOutputFile;

    public function __construct(string $pathInputFile, string $pathOutputFile)
    {
        $this->pathInputFile = $pathInputFile;
        $this->pathOutputFile = $pathOutputFile;
    }

    public function convert(string $fileName): string
    {
//        Settings::setPdfRendererPath(dirname(dirname(__DIR__)) . '/vendor/dompdf/dompdf/');
//        Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);

        Settings::setPdfRendererPath(dirname(dirname(__DIR__)) . '/vendor/mpdf');
        Settings::setPdfRendererName(Settings::PDF_RENDERER_MPDF);

        $doc = IOFactory::load($this->pathInputFile . $fileName);
        $pdfWriter = IOFactory::createWriter($doc, 'PDF');

        $fileName = pathinfo($fileName)['filename'] . '.pdf';
        $pdfWriter->save($this->pathOutputFile . $fileName);

        return $fileName;
    }
}
