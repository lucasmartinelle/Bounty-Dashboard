<?php

namespace App\Form;

use App\Entity\Templates;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;


class CreateTemplateTemplatesType extends AbstractType
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
                        'message' => $this->translator->trans('Please enter a title'),
                    ]),
                    new Assert\Length([
                        'max' => 200,
                        'maxMessage' => $this->translator->trans('Your title cannot be more than {{ limit }} characters long.')
                    ])
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('stepstoreproduce', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['style' => 'display:none;']
            ])
            ->add('impact', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['style' => 'display:none;']
            ])
            ->add('mitigation', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['style' => 'display:none;']
            ])
            ->add('ressources', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['style' => 'display:none;']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Templates::class,
        ]);
    }
}
