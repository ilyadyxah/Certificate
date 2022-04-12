<?php

namespace App\Service;

class PdfToWordConverter implements ConverterInterface
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
        // Для конвертирования в pdf to word используется внешняя программа LibreOffice.
        // Старый формат .doc - не поддерживается библиотекой PhpWord

        $command = 'soffice --headless --infilter="writer_pdf_import" --convert-to docx --outdir ' .
            $this->pathOutputFile .
            ' ' .
            $this->pathInputFile . $fileName;
        shell_exec($command);

        return pathinfo($fileName)['filename'] . '.docx';

    }
}
