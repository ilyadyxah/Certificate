<?php

namespace App\Service;

use App\Entity\Certificate;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class ImportFromXlsService
{
    private string $pathInputFile;
    private ManagerRegistry $doctrine;
    private ConverterInterface $pdfConverter;
    private ReplaceContent $replaceContent;
    private string $titleField;
    private string $fileField;

    public function __construct(
        string             $pathInputFile,
        string             $titleField,
        string             $fileField,
        ManagerRegistry    $doctrine,
        ConverterInterface $pdfConverter,
        ReplaceContent     $replaceContent
    )
    {
        $this->pathInputFile = $pathInputFile;
        $this->doctrine = $doctrine;
        $this->pdfConverter = $pdfConverter;
        $this->replaceContent = $replaceContent;
        $this->titleField = $titleField;
        $this->fileField = $fileField;
    }

    public function import(string $fileName)
    {
        $manager = $this->doctrine->getManager();
        $filePath = $this->pathInputFile . $fileName;

        $rows = $this->getData($filePath); // Получение данных из xls в свой массив
        if ($this->validate($rows)) { // Валидация значений массива
            for ($i = 0; $i < count($rows[$this->titleField]); $i++) {
                $title = $rows[$this->titleField][$i];
                $file = $rows[$this->fileField][$i];

                // Для каждой пары значений "название" и "файл" конвертация (при необходимости) и замена значений. Затем запись в БД.
                $this->save($manager, $title, $this->convertAndReplace($file));
            }
            $manager->flush();
        }
    }

    public function convertAndReplace(string $file): string
    {
        $fileName = pathinfo($file)['basename'];
        $fileDirectory = substr(pathinfo($file)['dirname'] . '/', 1);

        switch (pathinfo($file)['extension']) {
            case 'docx':
                $fileName = $this->replaceContent->replace(
                    $fileName,
                    dirname(__DIR__, 2) . '/public/' . $fileDirectory
                );
                break;
            case 'pdf':
                $fileName = $this->pdfConverter->convert($fileName, $fileDirectory);
                $fileName = $this->replaceContent->replace($fileName);
                break;
        }
        return $fileName;
    }

    public function save(ObjectManager $manager, string $title, string $fileName): void
    {
        $certificate = new Certificate();
        $certificate
            ->setTitle($title)
            ->setFilename($fileName);
        $manager->persist($certificate);
    }

    public function validate(array $rows): bool
    {
        $patternTitle = '/^[a-z0-9 \s]{3,256}$/ui';
        $patternFile = '/\//ui';

        foreach ($rows[$this->titleField] as $title) {
            if (!preg_match($patternTitle, $title)) {
                return false;
            }
        }
        foreach ($rows[$this->fileField] as $title) {
            if (!preg_match($patternFile, $title)) {
                return false;
            }
        }

        return true;
    }

    public function getData(string $filePath): array
    {
        $reader = ReaderEntityFactory::createReaderFromFile($filePath);

        $reader->open($filePath);
        $data = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $data[] = $row->getCells();
            }
        }
        $reader->close();

        $titleField = $data[0][0]->getValue();
        $fileField = $data[0][1]->getValue();
        $rows = [$titleField => [], $fileField => []];

        for ($i = 1; $i < count($data); $i++) {
            $rows[$this->titleField][] = $data[$i][0]->getValue();
            $rows[$this->fileField][] = $data[$i][1]->getValue();
        }

        return $rows;
    }
}
