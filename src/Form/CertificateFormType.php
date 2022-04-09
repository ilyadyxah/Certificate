<?php

namespace App\Form;

use App\Entity\Certificate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class CertificateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Certificate|null $certificate */
        $certificate = $options['data'] ?? null;

        $fileConstrains = [
            new File([
                'maxSize' => '10M',
                'maxSizeMessage' => 'Файл слишком большой. Максимальный размер 20 МБ'
            ]),
        ];

        if (!$certificate || !$certificate->getFilename()) {
            $fileConstrains[] = new NotNull([
                'message' => 'Не выбран файл шаблона',
            ]);
        }

        $builder
            ->add('title', TextType::class, [
                'label' => 'Название статьи',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Укажите название шаблона',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Название шаблона должно быть длиной не менее 3-х символов',
                    ]),
                ],
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => $fileConstrains

            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Certificate::class,
        ]);
    }
}
