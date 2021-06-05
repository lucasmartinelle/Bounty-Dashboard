<?php

namespace App\Form;

use App\Entity\Reports;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;


class CreateReportReportsType extends AbstractType
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
            ->add('severity', NumberType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('date', TextType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('endpoint', TextType::class, [
                'label' => false,
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your endpoint cannot be more than {{ limit }} characters long.')
                    ])
                ],
            ])
            ->add('identifiant', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter an identifier'),
                    ]),
                    new Assert\Length([
                        'max' => 200,
                        'maxMessage' => $this->translator->trans('Your identifier cannot be more than {{ limit }} characters long.')
                    ])
                ],
            ])
            ->add('template_id', HiddenType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('program_id', ChoiceType::class, [
                'label' => false,
                'placeholder' => 'Choose a program',
                'choices'  => $options['programsName'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter a program'),
                    ])
                ],
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
            'data_class' => Reports::class,
        ]);
        $resolver->setRequired('programsName');
    }
}