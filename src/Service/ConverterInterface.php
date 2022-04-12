<?php

namespace App\Service;

interface ConverterInterface
{
    public function convert(string $fileName): string;
}
