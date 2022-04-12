<?php

namespace App\Service;

use Faker\Factory;
use FPDF;

class PdfCreatorFromImage
{
    private string $pathInputFile;
    private string $pathOutputFile;

    public function __construct(string $pathInputFile, string $pathOutputFile)
    {
        $this->pathInputFile = $pathInputFile;
        $this->pathOutputFile = $pathOutputFile;
    }

    public function create(string $fileName): string
    {
        // Сервис берёт картинку как фон, и накладывает текст поверх неё.
        $faker = Factory::create();

        $title = $faker->realText(20);
        $firstName = $faker->firstName;
        $lastName = $faker->lastName;
        $pdf = new FPDF('P', 'pt', array(500, 500));
        $pdf->AddPage();
        $pdf->Image($this->pathInputFile . '/' . $fileName, 0, 0);
        $pdf->SetFont('Arial', 'B', 23);
        $pdf->Text(100, 100, $title);
        $pdf->Text(150, 200, $firstName . $lastName);
        $pdf->Output($this->pathOutputFile . '/' . pathinfo($fileName)['filename'] . '.pdf', 'F');

        return pathinfo($fileName)['filename'] . '.pdf';
    }
}