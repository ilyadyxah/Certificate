<?php

namespace App\DataFixtures;

use App\Entity\Certificate;
use App\Service\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\HttpFoundation\File\File;

class CertificateFixtures extends Fixture
{
    protected Generator $faker;
    protected ObjectManager $manager;
    private FileUploader $fileUploader;

    private static array $files = [ // Имитация
        'file-1.pdf',
        'file-2.pdf',
        'file-3.pdf',
        'file-4.pdf',
        'file-5.pdf',
    ];

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        $this->manager = $manager;

        $this->createMany(Certificate::class, 25, function (Certificate $entity) use ($manager) {

            $fileName = $this->faker->randomElement(self::$files);
            $entity
                ->setTitle($this->faker->text(30))
                ->setFilename($this->fileUploader->uploadFile(new File(dirname(dirname(__DIR__)) . '/public/files/' . $fileName)));
        });

        $manager->flush();
    }

    protected function createMany(string $className, int $count, callable $factory)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = $this->create($className, $factory);
            $this->addReference("$className|$i", $entity);
        }
    }

    protected function create($className, callable $factory)
    {
        $entity = new $className();
        $factory($entity);
        $this->manager->persist($entity);

        return $entity;
    }
}
