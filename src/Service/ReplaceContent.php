<?php

namespace App\Service;

use Faker\Factory;
use PhpOffice\PhpWord\TemplateProcessor;


class ReplaceContent
{
    private ConverterInterface $wordConverter;
    private string $pathInputFile;
    private string $pathOutputFile;

    public function __construct(
        string $pathInputFile,
        string $pathOutputFile,
        ConverterInterface $wordConverter)
    {
        $this->wordConverter = $wordConverter;
        $this->pathInputFile = $pathInputFile;
        $this->pathOutputFile = $pathOutputFile;
    }

    public function replace(string $fileName, string $pathInputFile = null)
    {
        $pathInputFile = $pathInputFile ?: $this->pathInputFile;
        $faker = Factory::create();
        $phpWord = new TemplateProcessor($pathInputFile . $fileName);
        $phpWord->setValue('TITLE', $faker->realText(20));
        $phpWord->setValue('FIRST_NAME', $faker->firstName);
        $phpWord->setValue('LAST_NAME', $faker->lastName);
        $phpWord->saveAs($this->pathOutputFile . $fileName);

        return $this->wordConverter->convert($fileName);
    }
}
