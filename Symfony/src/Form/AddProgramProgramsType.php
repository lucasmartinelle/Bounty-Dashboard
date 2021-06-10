<?php

namespace App\Form;

use App\Entity\Programs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class AddProgramProgramsType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter your name'),
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your name cannot be more than {{ limit }} characters long.')
                    ]),
                ],
            ])
            ->add('scope', TextType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('scope_hidden', HiddenType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter a scope'),
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans('Your scope cannot be more than {{ limit }} characters long.')
                    ])
                ],
                'mapped' => false
            ])
            ->add('date', TextType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('status', ChoiceType::class, [
                'label' => false,
                'placeholder' => $this->translator->trans('Choose a status'),
                'choices'  => [
                    $this->translator->trans('Open')=> 'Open',
                    $this->translator->trans('Close') => 'Close'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter a status'),
                    ]),
                ],
            ])
            ->add('tags', TextType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('tags_hidden', HiddenType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('platforms', ChoiceType::class, [
                'label' => false,
                'placeholder' => $this->translator->trans('Choose a Platform'),
                'choices'  => $options['platformsName'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->translator->trans('Please enter a platform'),
                    ]),
                ],
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Programs::class,
        ]);
        $resolver->setRequired('platformsName');
    }
}
