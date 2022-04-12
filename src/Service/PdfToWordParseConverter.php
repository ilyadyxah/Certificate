<?php

namespace App\Service;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Smalot\PdfParser\Parser;

class PdfToWordParseConverter implements ConverterInterface
{
    private string $pathInputFile;
    private string $pathOutputFile;
    private ConverterInterface $wordConverter;

    public function __construct(string $pathInputFile, string $pathOutputFile, ConverterInterface $wordConverter)
    {
        $this->pathInputFile = $pathInputFile;
        $this->pathOutputFile = $pathOutputFile;
        $this->wordConverter = $wordConverter;
    }

    public function convert(string $fileName): string
    {
        // Сервис парсит из pdf текст, обрабатывает в нужную структуру и производит замену.
        // P.s. написан криво, самому не нравится. Оставил как память функционалу библиотеки Smalot/pdfparser

        // Pdf to Text
        $parser = new Parser();
        $pdf = $parser->parseFile($this->pathInputFile . $fileName);
        $text = $pdf->getText();
        $text = str_replace(["\n", "\t", ' '], '', $text);
        $text = str_replace(['}'], '}separator', $text);
        $text = explode('separator', $text);

        //Text to Word
        $phpWord = new PhpWord();

        $section = $phpWord->addSection();
        foreach ($text as $string) {
            $section->addText($string);
        }
        $fileName = pathinfo($fileName)['filename'] . '.docx';
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($this->pathOutputFile . $fileName);

        return $this->wordConverter->convert($fileName);
    }
}
