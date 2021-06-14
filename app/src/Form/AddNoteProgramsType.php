<?php

namespace App\Form;

use App\Entity\Notes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class AddNoteProgramsType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter your title'),
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your title cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('text', TextareaType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter a text'),
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Notes::class,
        ]);
    }
}