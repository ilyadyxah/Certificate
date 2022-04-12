<?php

namespace App\Service;

use Com\Tecnick\Unicode\Convert;
use Faker\Factory;
use PhpOffice\PhpWord\TemplateProcessor;


class ReplaceContent
{
    private ConverterInterface $wordConverter;

    public function __construct(ConverterInterface $wordConverter)
    {
        $this->wordConverter = $wordConverter;
    }

    public function replace(string $fileName, string $pathInputFile, string $pathOutputFile)
    {
        $faker = Factory::create();
        $phpWord = new TemplateProcessor($pathInputFile . '/' . $fileName);
        $phpWord->setValue('TITLE', $faker->realText(20));
        $phpWord->setValue('FIRST_NAME', $faker->firstName);
        $phpWord->setValue('LAST_NAME', $faker->lastName);
        $phpWord->saveAs($pathOutputFile . '/' . $fileName);

        return $this->wordConverter->convert($fileName);
    }
}
